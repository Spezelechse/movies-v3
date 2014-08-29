<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Zend\View\Model\ViewModel;
use Movies\Form\LoginForm;

class AuthController extends BasisController
{
    protected $form;
    protected $storage;
     
    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()
                                  ->get('Movies\Model\MoviesAuthStorage');
        }
         
        return $this->storage;
    }

    /**
     * Index - Admin
     * 
     * @return Zend\View\Model\ViewModel Zend View Model
     */
    public function indexAction()
    {
        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
    }

    public function loginAction()
    {   
        if(!$this->getAuthService()->hasIdentity()){

            $this->view->form=new LoginForm();
            
            return $this->view;
        }
        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
    }

    public function authAction()
    {
        $form = new LoginForm();
        $request = $this->getRequest();

        if ($request->isPost()){
            $form->setData($request->getPost());
            if($form->isValid()){
                $this->getAuthService()->getAdapter()
                                       ->setIdentity($request->getPost('username'))
                                       ->setCredential($request->getPost('password'));
                                        
                $result = $this->getAuthService()->authenticate();

                if ($result->isValid()) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('login_success'));
                
                    if ($request->getPost('remember') == 1 ) {
                        $this->getSessionStorage()
                                ->setRememberMe(1);

                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write($result->getIdentity());
                    
                    return $this->redirect()->toRoute('movies', array('action' => 'index', 'lang' => $this->language));
                }
                else{
                    $this->flashMessenger()->addErrorMessage($this->translate('login_data_wrong')); 
                    foreach($result->getMessages() as $message)
                    {
                        $this->flashmessenger()->addMessage($message);
                    }  
                }
            }
            else{
                $this->flashMessenger()->addErrorMessage($this->translate('login_data_missing'));
            }
        }

        return $this->redirect()->toRoute('auth', array('action' => 'login', 'lang' => $this->language));
    }

    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
         
        $this->flashmessenger()->addSuccessMessage($this->translate('logged_out'));
        return $this->redirect()->toRoute('auth',array('action' => 'login', 'lang' => $this->language));
    }
}