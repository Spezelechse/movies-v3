<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Zend\View\Model\ViewModel;
use Movies\Model\User;
use Movies\Model\Medium;
use Movies\Model\Genre;
use Movies\Model\Type;
use Movies\Model\Publisher;
use Movies\Model\Director;
use Movies\Model\Config;
use Movies\Form\UserForm;
use Movies\Form\MediumForm;
use Movies\Table\UserTable;
use Movies\Table\ConfigTable;
use Movies\Table\ExportTable;
use Zend\I18n\Filter\Alnum;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\Int;
use Zend\Validator\File\MimeType;
use SimpleImage;
use DOMPDFModule\View\Model\PdfModel;

class AdminController extends BasisController
{
    private $medium_form;

    public function init()
    {
        parent::init();

        if(!$this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('auth', array('lang'=>$this->language, 'action'=>'login')); 
        }
    }

    private function getMediumForm(){
        if(empty($this->medium_form)){
            $form = new MediumForm();
            $form->get('submit')->setValue($this->translate('Update'));
            $form->get('genre')->setValueOptions($this->Tables()->genre()->fetchAllForSelect($this->language));
            $form->get('director')->setValueOptions($this->Tables()->director()->fetchAllForSelect());
            $form->get('publisher')->setValueOptions($this->Tables()->publisher()->fetchAllForSelect());
            $form->get('type_id')->setValueOptions($this->Tables()->type()->fetchAllForSelect($this->language));
            $form->get('owner_id')->setValueOptions($this->Tables()->user()->fetchAllForSelect());
            
            $this->medium_form = $form;
        }
        return $this->medium_form;
    }

    public function indexAction(){
        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
    }

    private function checkImage($post, $file, $old_name=''){
        $filename = $old_name;
        $mime = new MimeType(array('image/png', 'image/jpeg'));
        $mime_check = true;

        if($file['name']!=''){
            $mime_check = $mime->isValid($file['tmp_name']);

            if($mime_check){
                $extension='';
                $alnum = new Alnum();
                $int =  new Int();

                $filename = $alnum->filter($post['title_en']);
                $filename .= '_'.$int->filter($post['premiere']);
                
                if($file['type']=='image/jpeg')
                {
                    $extension='.jpg';
                }
                else if($file['type']=='image/png')
                {
                    $extension='.png';
                }

                $id = uniqid();
                while(file_exists('./public/img/cover/'.$filename.'_'.$id.$extension)){
                    $id = uniqid();
                }

                $filename .= '_'.$id.$extension;

                if($old_name!=''){
                    unlink('./public/img/cover/'.$old_name);
                    unlink('./public/img/thumb/'.$old_name);
                }
            }
            else{
                $this->showFormMessages($mime);
            }
        }

        return array('name'=>$filename,'result'=>$mime_check);
    }

    private function createThumbnail($filename){
        if($filename!=''){                                
            $thumbnail = new SimpleImage();
            $thumbnail->load('./public/img/cover/'.$filename);
            $thumbnail->resizeToHeight(300);
            $thumbnail->save('./public/img/thumb/'.$filename);
        }
    }

    public function addMediumAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','add')){
            $form = $this->getMediumForm();
            $form->get('submit')->setValue($this->translate('Add'));

            $request = $this->getRequest();
            if ($request->isPost()) {
                $medium = new Medium();
                $post = $request->getPost()->toArray();
                $files = $request->getFiles()->toArray();
             
                $file_check = $this->checkImage($post, $files['cover_file']);   

                $form->setInputFilter($medium->getInputFilter('add',$file_check['name']));

                $form->setData(array_merge($post, $files));

                if ($form->isValid()) {
                    if($file_check['result']){
                        $formData = $form->getData();

                        $formData['cover_file']=$file_check['name'];
                        
                        $this->createThumbnail($file_check['name']);

                        $medium->exchangeArray($formData);

                        $id = $this->Tables()->medium()->save($medium);
                        
                        $this->Tables()->actor()->connectToMedium($id, $formData['actors_text'], $formData['roles_text']);
                        $this->Tables()->genre()->connectToMedium($id, $formData['genre']);
                        $this->Tables()->director()->connectToMedium($id, $formData['director']);
                        $this->Tables()->publisher()->connectToMedium($id, $formData['publisher']);

                        $this->flashMessenger()->addSuccessMessage($this->translate('Medium added'));
                        
                        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
                    }
                }
                else{
                    $this->showFormMessages($form);
                }
            }

            $this->view->form=$form;
            
            return $this->view;
        }
        else{
            return $this->rightMissing();
        }
    }

    public function deleteMediumAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','delete')){
            $id=$this->params()->fromRoute('id',0);
            
            if($id>0){
                $request = $this->getRequest();
                if ($request->isPost()) {
                    $medium=$this->Tables()->medium()->get($id);

                    if(isset($medium->cover_file)){
                        unlink('./public/img/cover/'.$medium->cover_file);
                        unlink('./public/img/thumb/'.$medium->cover_file);
                    }

                    $user=$this->Tables()->medium()->delete($id);

                    $this->flashMessenger()->addSuccessMessage($this->translate('Medium deleted'));

                    return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
                }
                else{
                    $medium=$this->Tables()->medium()->get($id);

                    $this->view->id=$id;
                    $this->view->medium=$medium;
                    
                    return $this->view;
                }
            }
        }
        else{
            return $this->rightMissing();
        }
    }

    public function editMediumAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','edit')){
            $id=(int)$this->params()->fromRoute('id',0);

            if($id>0){
                $medium=$this->Tables()->medium()->get($id);
                $medium_data=$medium->toArray();

                $medium_data['genre']=$this->Tables()->genre()->fetchForPreselect($id);
                $medium_data['director']=$this->Tables()->director()->fetchForPreselect($id);
                $medium_data['publisher']=$this->Tables()->publisher()->fetchForPreselect($id);
                $actors=$this->Tables()->actor()->fetchForEdit($id);
                $medium_data['actors_text']=$actors['names'];
                $medium_data['roles_text']=$actors['roles'];

                $form = $this->getMediumForm();
                $form->get('submit')->setValue($this->translate('Update'));
                $form->setData($medium_data);

                $request = $this->getRequest();
                if ($request->isPost()) {
                    $medium = new Medium();
                    $post = $request->getPost()->toArray();
                    $files = $request->getFiles()->toArray();

                    $file_check = $this->checkImage($post, $files['cover_file'], $medium_data['cover_file']);   

                    $form->setInputFilter($medium->getInputFilter('update',$file_check['name']));

                    $form->setData(array_merge($post, $files));

                    if ($form->isValid()) {
                        if($file_check['result']){
                            $formData = $form->getData();

                            $formData['cover_file']=$file_check['name'];

                            $this->createThumbnail($file_check['name']);

                            $medium->exchangeArray($formData);

                            $this->Tables()->medium()->save($medium);
                            
                            $this->Tables()->actor()->updateMediumConnection($medium->id, $formData['actors_text'], $formData['roles_text']);

                            $this->Tables()->genre()->updateMediumConnection($medium->id, $formData['genre']);
                            $this->Tables()->director()->updateMediumConnection($medium->id, $formData['director']);
                            $this->Tables()->publisher()->updateMediumConnection($medium->id, $formData['publisher']);

                            $this->flashMessenger()->addSuccessMessage($this->translate('Medium updated'));

                            return $this->redirect()->toRoute('movies', array('action'=>'show','id'=>$id,'lang'=>$this->language));
                        }
                    }
                    else{
                        $this->showFormMessages($form);
                    }
                }
                
                $this->view->form=$form;
                $this->view->id=$id;
                $this->view->cover_file=$medium->getCover();
                
                return $this->view;
            }
            else{
                $this->flashMessenger()->addErrorMessage($this->translate('Medium id not found'));
                return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
            }
        }
        else{
            return $this->rightMissing();
        }
    }

    public function importMediumAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','import')){
            $request = $this->getRequest();
            $files = $request->getFiles()->toArray();

            if ($request->isPost()&&!empty($files['import_file']['tmp_name'])) {
                $post = $request->getPost()->toArray();

                $import_data = json_decode(file_get_contents($files['import_file']['tmp_name']));
                $owner_id = (int)$post['owner_id'];
                $result = array('succeed'=>array(), 'failed'=>array());

                if(is_array($import_data)){
                    foreach ($import_data as $data) {
                        $import = $this->processSingleImport($data, $owner_id);

                        if(isset($import['failed'])){
                            array_push($result['failed'], $import['failed']);
                        }
                        else{
                            array_push($result['succeed'], $import['succeed']);
                        }
                    }
                }

                $this->view->import_result=$result;
            }
            else{
                $this->view->users = $this->Tables()->user()->fetchAllForSelect();
            }
            
            return $this->view;
        }
        else{
            return $this->rightMissing();
        }
    }

    private function processSingleImport($data, $owner_id){
        $data = $this->prepareImport($data, $owner_id);

        $medium = new Medium();

        $form = $this->getMediumForm();
        $form->setInputFilter($medium->getInputFilter('add'));
        $form->remove('csrf');
        $form->setData($data);

        if ($form->isValid()) {
            $formData = $form->getData();
            $medium->exchangeArray($formData);

            $id = $this->Tables()->medium()->save($medium);
            
            $this->Tables()->actor()->connectToMedium($id, $formData['actors_text'], $formData['roles_text']);

            $this->Tables()->genre()->connectToMedium($id, $formData['genre']);
            $this->Tables()->director()->connectToMedium($id, $formData['director']);
            $this->Tables()->publisher()->connectToMedium($id, $formData['publisher']);

            return array('succeed'=>$data['title_'.$this->language]);
        }
        else{
            //$this->showFormMessages($form);
            return array('failed'=>$data['title_'.$this->language]);
        }
    }

    private function prepareImport($data, $owner_id){
        $keys =  array('director','publisher','genre');

        foreach ($keys as $key) {
            $ids = array();
            foreach ($data->$key as $value) {
                $id = $this->Tables()->$key()->import($value);
                if($id>0){
                    array_push($ids,$id);
                }
            }
            $data->$key = $ids;
        }

        $data->type_id = $this->Tables()->type()->import($data->type);
        
        unset($data->type);
        
        $data->actors_text = '';
        $data->roles_text = '';

        foreach ($data->actors as $actor) {
            $data->actors_text .= $actor->name.'#'.$actor->year_of_birth.PHP_EOL;
            $data->roles_text .= $actor->role.PHP_EOL;
        }

        $data->actors_text=substr($data->actors_text,0,strlen($data->actors_text)-1);
        $data->roles_text=substr($data->roles_text,0,strlen($data->roles_text)-1);

        unset($data->actors);

        $data->owner_id = $owner_id;

        return (array)$data;
    }

    public function exportMediumAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','export')){
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $export = array();
                $ids =  array();

                if(isset($post['export_selected'])){
                    $ids = $post['export_selected'];
                }
                
                if(isset($post['export_all'])){
                    $media = $this->Tables()->medium()->fetchAll();

                    foreach ($media as $medium) {
                        array_push($export,$this->prepareForExport((int)$medium->id));
                    }
                }
                else{
                    foreach ($ids as $id) {
                        array_push($export,$this->prepareForExport((int)$id));
                    }
                }                
                
                $content = json_encode($export);

                $response = new \Zend\Http\PhpEnvironment\Response();
                $response->setContent($content);
                $response->setStatusCode(200);

                $headers = new \Zend\Http\Headers();
                $headers->addHeaderLine('Content-Type', 'application/json; charset=UTF-8')
                        ->addHeaderLine('Content-Disposition', 'attachment; filename="export_' . date('y-m-d__H-i-s') . '.json"')
                        ->addHeaderLine('Content-Length', strlen($content));

                $response->setHeaders($headers);

                return $response;
                //$this->view->json_export=json_encode($export);
            }

            return $this->view;
        }
        else{
            return $this->rightMissing();
        }
    }

    public function exportMediumAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','export')){
            $media_select=$this->Tables()->medium()->fetchAllForList_Select($this->language);

            $params=array(
                'translator' =>$this->Translator()->getTranslator(),
                'basicPath' =>$this->url()->fromRoute('movies'),
                'path' =>$this->getRequest()->getUri()->getPath(),
                'disableShowAs'=>true,
            );

            $table = new ExportTable($params);
            $table->setLanguage($this->language);
            $table->setTranslator($this->Translator()->getTranslator());

            $table->setAdapter($this->getDbAdapter())
                  ->setSource($media_select)
                  ->setParamAdapter($this->getRequest()->getPost());

            $table = $this->htmlResponse($table->render().'<script type="text/javascript" src="'.$this->url()->fromRoute('movies').'js/movies/export.js"></script>');
            return $table;
        } 
        else{
            return $this->htmlResponse('');
        }
    }

    private function prepareForExport($id){
        $medium = $this->Tables()->medium()->get($id);
        $medium->setGenre($this->Tables()->genre()->fetchForMedium($id));
        $medium->setActors($this->Tables()->actor()->fetchForMedium($id));
        $medium->setDirector($this->Tables()->director()->fetchForMedium($id));
        $medium->setPublisher($this->Tables()->publisher()->fetchForMedium($id));
        $medium->setType($this->Tables()->type()->get($medium->type_id));
        return $medium->export();
    }

    public function createPdfAction()
    {
        $model = new PdfModel();
        $model->setOption('paperSize', 'a4');
        $model->setOption('paperOrientation', 'portrait');
        $model->setOption('basePath',__DIR__.'/../../../public/');
        $model->setVariables(array(
          'media' => $this->Tables()->medium()->fetchAllForTable($this->language),
          'language' => $this->language,
          'title' => $this->MoviesConfig()->get('title'),
        ));
 
        return $model;
    }

    public function listUserAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('user','edit')||$this->getAuthService()->getIdentity()->hasRight('user','delete')){
            return $this->view;
        }
        else{
            return $this->rightMissing();
        }
    }

    public function listUserAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('user','edit')||$this->getAuthService()->getIdentity()->hasRight('user','delete')){
            $user_select=$this->Tables()->user()->fetchAllForList_Select($this->MoviesConfig()->get('superadmin_id'),$this->getAuthService()->getIdentity()->id);

            $table = new UserTable();
            if($this->getAuthService()->getIdentity()->hasRight('user','edit')){
                $table->setEditUrl($this->url()->fromRoute('admin', array('lang'=>$this->language, 'action'=>'edit-user')));
            }
            if($this->getAuthService()->getIdentity()->hasRight('user','delete')){
                $table->setDeleteUrl($this->url()->fromRoute('admin', array('lang'=>$this->language, 'action'=>'delete-user')));
            }
            $table->setTranslator($this->Translator()->getTranslator());
            $table->setAdapter($this->getDbAdapter())
                  ->setSource($user_select)
                  ->setParamAdapter($this->getRequest()->getPost());

            $table = $this->htmlResponse($table->render());


            return $table;
        } 
        else{
            return $this->htmlResponse('');
        }
    }

    public function addUserAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('user','add')){
            $form = new UserForm();
            $form->get('submit')->setValue($this->translate('Add'));

            $request = $this->getRequest();
            if ($request->isPost()) {
                $user = new User();
                
                $form->setInputFilter($user->getInputFilter('add'));

                $post=$request->getPost();
                $form->setData($post);

                if ($form->isValid()) {
                    $user->exchangeArray($form->getData());

                    $rights=array('medium'=>$post['medium-rights'],
                                    'user'=>$post['user-rights'],
                                    'page'=>$post['page-rights']);

                    $user->setRightsFromSelect($rights);
                    $user->hashPassword();

                    $this->Tables()->user()->save($user);

                    $this->flashMessenger()->addSuccessMessage($this->translate('User added'));
                    return $this->redirect()->toRoute('admin', array('lang'=>$this->language, 'action'=>'list-user'));
                }
                else{
                    $this->showFormMessages($form);
                }
            }

            $this->view->form=$form;
            
            return $this->view;
        }
        else{
            return $this->rightMissing();
        }
    }

    public function deleteUserAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('user','delete')){
            $id=$this->params()->fromRoute('id',0);
            
            if($id>0){
                $request = $this->getRequest();
                if ($request->isPost()) {
                    $user=$this->Tables()->user()->delete($id);

                    $this->flashMessenger()->addSuccessMessage($this->translate('User deleted'));
                    return $this->redirect()->toRoute('admin', array('lang'=>$this->language, 'action'=>'list-user'));
                }
                else{
                    $user=$this->Tables()->user()->get($id);

                    $this->view->id=$id;
                    $this->view->user=$user;
                    
                    return $this->view;
                }
            }
        }
        else{
            return $this->rightMissing();
        }
    }

    public function editUserAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('user','edit')){
            $id=(int)$this->params()->fromRoute('id',0);
            
            if($id>0){
                $user=$this->Tables()->user()->get($id);

                $request = $this->getRequest();
                if ($request->isPost()) {
                    $oldData = $user;
                    $user = new User();
                    $form = new UserForm();
                    
                    $form->setInputFilter($user->getInputFilter('update'));

                    $post=$request->getPost();
                    $form->setData($post);

                    if ($form->isValid()) {
                        $this->flashMessenger()->addSuccessMessage($this->translate('User updated'));
                        $user->exchangeArray($form->getData());
                        $rights=array('medium'=>$post['medium-rights'],
                                        'user'=>$post['user-rights'],
                                        'page'=>$post['page-rights']);

                        $user->setRightsFromSelect($rights);
                        
                        if($post->toArray()['password']!=''){
                            $user->hashPassword();
                        }
                        else{
                            $user->password=$oldData->password;
                        }

                        $this->Tables()->user()->save($user);
                        
                        return $this->redirect()->toRoute('admin', array('lang'=>$this->language, 'action'=>'list-user'));
                    }
                    else{
                        $this->showFormMessages($form);
                    }
                }
                else{
                    $rights=$user->getRightsForSelect();
                    $user=$user->toArray();

                    unset($user['password']);
                    
                    $user['medium-rights']=$rights['medium'];
                    $user['user-rights']=$rights['user'];
                    $user['page-rights']=$rights['page'];

                    $form = new UserForm();
                    $form->get('submit')->setValue($this->translate('Update'));
                    $form->setData($user);
                }

                $this->view->form=$form;
                $this->view->id=$id;

                return $this->view;
            }
            else{
                $this->flashMessenger()->addErrorMessage($this->translate('User id not found'));
                
                return $this->redirect()->toRoute('admin', array('lang'=>$this->language, 'action'=>'list-user'));
            }
        }
        else{
            return $this->rightMissing();
        }
    }

    public function siteSetUpAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('page','config')){
            return $this->view;
        }
        else{
            return $this->rightMissing();
        }
    }

    public function updateConfigAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('page','config')){
            $param = $this->getRequest()->getPost();
            $trim = new StringTrim();
            $strip = new StripTags();

            $config = $this->Tables()->config()->get((int)$param['row']);
            $config->data = $strip->filter($trim->filter($param['value']));

            $this->Tables()->config()->save($config);
            return $this->jsonResponse(array('succes' => 1));
        }
        else{
            return $this->jsonResponse(array('succes' => 0));
        }
    }

    public function siteSetUpAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('page','config')){
            $config_select=$this->Tables()->config()->fetchAllForList_Select($this->language);

            $table = new ConfigTable();
            $table->setUpdateUrl($this->url()->fromRoute('admin', array('lang'=>$this->language, 'action'=>'update-config-ajax')));
            $table->setTranslator($this->Translator()->getTranslator());
            $table->setAdapter($this->getDbAdapter())
                  ->setSource($config_select)
                  ->setParamAdapter($this->getRequest()->getPost());

            $table = $this->htmlResponse($table->render());

            return $table;
        }
        else{
            return $this->jsonResponse(array('succes' => 0));
        }
    }

    public function addGenreAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','edit')||$this->getAuthService()->getIdentity()->hasRight('medium','add')){
            $post = $this->getRequest()->getPost()->toArray();
            $trim = new StringTrim();
            $strip = new StripTags();
          
            $name_de = $strip->filter($trim->filter($post['name_de']));
            $name_en = $strip->filter($trim->filter($post['name_en']));

            $genre = new Genre();
            $genre->name_de = $name_de;
            $genre->name_en = $name_en;

            $id = $this->Tables()->genre()->save($genre);

            return $this->jsonResponse(array('id'=>$id, 'name'=>$genre->getName($this->language)));
        }
        else{
            return $this->jsonResponse(array('succes' => 0));
        }
    }

    public function addPublisherAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','edit')||$this->getAuthService()->getIdentity()->hasRight('medium','add')){
            $post = $this->getRequest()->getPost()->toArray();
            $trim = new StringTrim();
            $strip = new StripTags();

            $name = $strip->filter($trim->filter($post['name']));

            $publisher = new Publisher();
            $publisher->name = $name;

            $id = $this->Tables()->publisher()->save($publisher);

            return $this->jsonResponse(array('id'=>$id, 'name'=>$publisher->name));
        }
        else{
            return $this->jsonResponse(array('succes' => 0));
        }
    }

    public function addDirectorAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','edit')||$this->getAuthService()->getIdentity()->hasRight('medium','add')){
            $post = $this->getRequest()->getPost()->toArray();
            $trim = new StringTrim();
            $strip = new StripTags();

            $name = $strip->filter($trim->filter($post['name']));

            $director = new Director();
            $director->name = $name;

            $id = $this->Tables()->director()->save($director);

            return $this->jsonResponse(array('id'=>$id, 'name'=>$director->name));
        }
        else{
            return $this->jsonResponse(array('succes' => 0));
        }
    }

    public function addTypeAjaxAction()
    {
        if($this->getAuthService()->getIdentity()->hasRight('medium','edit')||$this->getAuthService()->getIdentity()->hasRight('medium','add')){
            $post = $this->getRequest()->getPost()->toArray();
            $trim = new StringTrim();
            $strip = new StripTags();

            $name_de = $strip->filter($trim->filter($post['name_de']));
            $name_en = $strip->filter($trim->filter($post['name_en']));

            $type = new Type();
            $type->name_de = $name_de;
            $type->name_en = $name_en;

            $id = $this->Tables()->type()->save($type);

            return $this->jsonResponse(array('id'=>$id, 'name'=>$type->getName($this->language)));
        }
        else{
            return $this->jsonResponse(array('succes' => 0));
        }
    }

    private function rightMissing(){
        $this->flashMessenger()->addErrorMessage($this->translate('You don\'t have the right for this action!!'));
        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
    }
}
