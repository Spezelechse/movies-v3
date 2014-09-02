<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Medium;
use Zend\View\Model\ViewModel;
use Movies\Table\MediaTable;
use ZfTable\Params\AdapterDataTables;
use ZfTable\Options\ModuleOptions;
use ZfTable\AbstractTable;

class MoviesController extends BasisController
{
    public function init()
    {
        parent::init();

        if(!$this->MoviesConfig()->get('public')){
          if(!$this->getAuthService()->hasIdentity()){
            $this->redirect()->toRoute('auth', array('lang'=>$this->language, 'action'=>'login'));
          }
        }
    }

    public function indexAction()
    {
      return $this->view;
    }

    public function ajaxIndexAction()
    {
      $post = $this->getRequest()->getPost();
      
      $media_select=$this->Tables()->medium()->fetchAllForList_Select($this->language);

      $params=array(
        'translator' =>$this->Translator()->getTranslator(),
        'basicPath' =>$this->url()->fromRoute('movies'),
        'listStyle' =>$post['listStyle'],
        'path' =>$this->getRequest()->getUri()->getPath(),
        );

      $table = new MediaTable($params);
      $table->setShowUrl($this->url()->fromRoute('movies', array('lang'=>$this->language, 'action'=>'show')));
      $table->setLanguage($this->language);

      $table->setAdapter($this->getDbAdapter())
              ->setSource($media_select)
              ->setParamAdapter($post);

      $table = $this->htmlResponse($table->render());

      return $table;  
    }

  	public function showAction()
  	{
      $id = (int)$this->params()->fromRoute('id',0);
      
      $medium = $this->Tables()->medium()->get($id);
      $medium->setGenre($this->Tables()->genre()->fetchForMedium($id));
      $medium->setActors($this->Tables()->actor()->fetchForMedium($id));
      $medium->setDirector($this->Tables()->director()->fetchForMedium($id));
      $medium->setPublisher($this->Tables()->publisher()->fetchForMedium($id));
      $medium->setType($this->Tables()->type()->get($medium->type_id));
      $medium->setOwner($this->Tables()->user()->get($medium->owner_id));

      $this->view->medium=$medium;
        
      return $this->view;
  	}
}