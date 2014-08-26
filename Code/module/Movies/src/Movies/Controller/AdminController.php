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
use Movies\Model\Config;
use Movies\Form\UserForm;
use Movies\Form\MediumForm;
use Movies\Table\UserTable;
use Movies\Table\ConfigTable;
use Zend\I18n\Filter\Alnum;
use Zend\Filter\Int;
use Zend\Validator\File\MimeType;
use SimpleImage;

class AdminController extends BasisController
{
    public function init()
    {
        parent::init();

        if(!$this->getAuthService()->hasIdentity()){
            $this->redirect()->toRoute('movies'); 
        }
    }
    public function indexAction()
    {
    }

    private function checkImage($post, $file){
        $filename = '';
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
                $filename .= $extension;
            }
            else{
                $this->showFormMessages($mime);
            }
        }

        return array('name'=>$filename,'result'=>$mime_check);
    }

    public function addMovieAction()
    {
        $form = new MediumForm();
        $form->get('submit')->setValue($this->translate('Add'));
        $form->get('genre')->setValueOptions($this->Tables()->genre()->fetchAllForSelect($this->language));
        $form->get('director')->setValueOptions($this->Tables()->director()->fetchAllForSelect());
        $form->get('publisher')->setValueOptions($this->Tables()->publisher()->fetchAllForSelect());
        $form->get('type_id')->setValueOptions($this->Tables()->type()->fetchAllForSelect($this->language));
        $form->get('owner_id')->setValueOptions($this->Tables()->user()->fetchAllForSelect());

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
                
                    $thumbnail = new SimpleImage();
                    $thumbnail->load('./public/img/cover/'.$file_check['name']);
                    $thumbnail->resizeToHeight(300);
                    $thumbnail->save('./public/img/thumb/'.$file_check['name']);

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

    public function deleteMovieAction()
    {
        $id=$this->params()->fromRoute('id',0);
        
        if($id>0){
            $request = $this->getRequest();
            if ($request->isPost()) {
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

    public function editMovieAction()
    {
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

            $form = new MediumForm();
            $form->get('submit')->setValue($this->translate('Update'));
            $form->get('genre')->setValueOptions($this->Tables()->genre()->fetchAllForSelect($this->language));
            $form->get('director')->setValueOptions($this->Tables()->director()->fetchAllForSelect());
            $form->get('publisher')->setValueOptions($this->Tables()->publisher()->fetchAllForSelect());
            $form->get('type_id')->setValueOptions($this->Tables()->type()->fetchAllForSelect($this->language));
            $form->get('owner_id')->setValueOptions($this->Tables()->user()->fetchAllForSelect());
            $form->setData($medium_data);

            $request = $this->getRequest();
            if ($request->isPost()) {
                $medium = new Medium();
                $post = $request->getPost()->toArray();
                $files = $request->getFiles()->toArray();
             
                $file_check = $this->checkImage($post, $files['cover_file']);   

                $form->setInputFilter($medium->getInputFilter('update',$file_check['name']));

                $form->setData(array_merge($post, $files));

                if ($form->isValid()) {
                    if($file_check['result']){
                        $formData = $form->getData();

                        $formData['cover_file']=$file_check['name'];

                        $thumbnail = new SimpleImage();
                        $thumbnail->load('./public/img/cover/'.$file_check['name']);
                        $thumbnail->resizeToHeight(300);
                        $thumbnail->save('./public/img/thumb/'.$file_check['name']);

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

    public function importMovieAction()
    {
        return;
    }

    public function exportMovieAction()
    {
        return;
    }


    public function listUserAction()
    {
        return $this->view;
    }

    public function listUserAjaxAction()
    {
        $user_select=$this->Tables()->user()->fetchAllForList_Select();

        $table = new UserTable();
        $table->setEditUrl($this->url()->fromRoute('admin', array('lang'=>$this->language, 'action'=>'edit-user')));
        $table->setDeleteUrl($this->url()->fromRoute('admin', array('lang'=>$this->language, 'action'=>'delete-user')));
        $table->setAdapter($this->getDbAdapter())
              ->setSource($user_select)
              ->setParamAdapter($this->getRequest()->getPost());

        $table = $this->htmlResponse($table->render());


        return $table; 
    }

    public function addUserAction()
    {
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

    public function deleteUserAction()
    {
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

    public function editUserAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        
        if($id>0){
            $request = $this->getRequest();
            if ($request->isPost()) {
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
                    $user->hashPassword();

                    $this->Tables()->user()->save($user);
                    
                    return $this->redirect()->toRoute('admin', array('lang'=>$this->language, 'action'=>'list-user'));
                }
                else{
                    $this->showFormMessages($form);
                }
            }
            else{
                $user=$this->Tables()->user()->get($id);
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

    public function siteSetUpAction()
    {
        return $this->view;
    }

    public function updateConfigAjaxAction()
    {
        $param = $this->getRequest()->getPost();

        $config = $this->Tables()->config()->get($param['row']);
        $config->data = $param['value'];

        $this->Tables()->config()->save($config);
        return $this->jsonResponse(array('succes' => 1));
    }

    public function siteSetUpAjaxAction()
    {
        $config_select=$this->Tables()->config()->fetchAllForList_Select();

        $table = new ConfigTable();
        $table->setUpdateUrl($this->url()->fromRoute('admin', array('lang'=>$this->language, 'action'=>'update-config-ajax')));
        $table->setAdapter($this->getDbAdapter())
              ->setSource($config_select)
              ->setParamAdapter($this->getRequest()->getPost());

        $table = $this->htmlResponse($table->render());

        return $table; 
    }
}