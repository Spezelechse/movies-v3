<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

abstract class BasisController extends AbstractActionController
{
    protected $language;
    protected $authservice;
    protected $view;
    protected $dbAdapter;
    
    public function init()
    {
      $lang=$this->params()->fromRoute('lang','en');
      $this->language=$lang;
      
      if($lang=='de'){
        $lang='de_DE';
      }
      else{
        $lang='en_US';
      }
      
      $translator = $this->getServiceLocator()->get('translator');
      $translator->setLocale($lang);

      $this->layout()->moviesAction=$this->params()->fromRoute('action','index');
      $this->layout()->moviesId=$this->params()->fromRoute('id','0');
      $this->layout()->moviesLanguage=$this->params()->fromRoute('lang', 'en');

      $this->view = new ViewModel();
      $this->view->language = $this->language;
    }

    public function onDispatch(MvcEvent $e) {

      $this->init();

      return parent::onDispatch($e);
    }

    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                                      ->get('AuthService');
        }
         
        return $this->authservice;
    }

    public function getDbAdapter()
    {
        if (!$this->dbAdapter) {
            $sm = $this->getServiceLocator();
            $this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return $this->dbAdapter;
    }

    protected function showFormMessages($form){
        foreach ($form->getMessages() as $name => $messages) {
            if(is_array($messages)){
              foreach ($messages as $message) {
                  $this->flashMessenger()->addErrorMessage($name.': '.$message);   
              }
            }
            else{
              $this->flashMessenger()->addErrorMessage($name.': '.$messages); 
            }
        }
    }

    public function htmlResponse($html)
    {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($html);
        return $response;
    }

    public function jsonResponse($data)
    {
        if(!is_array($data)){
            throw new Exception('$data param must be array');
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($data));
        return $response;
    }

    protected function translate($string){
        return $string;
    }
}