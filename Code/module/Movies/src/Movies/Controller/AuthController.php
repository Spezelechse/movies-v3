<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Zend\View\Model\ViewModel;
use Movies\Form\LoginForm;
use Movies\Model\Blacklist;

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
        $ip_adress = $this->getRequest()->getServer()->get('REMOTE_ADDR');
        $this->Tables()->blacklist()->cleanUp();

        if ($request->isPost()){
            $blacklist_entry = $this->Tables()->blacklist()->checkBlacklist($ip_adress, $request->getPost('username'));

            if($blacklist_entry->attempts<3&&$blacklist_entry->getTimeLeft()<=0){
                $form->setData($request->getPost());

                if($form->isValid()){
                    $this->getAuthService()->getAdapter()
                                           ->setIdentity($request->getPost('username'))
                                           ->setCredential($request->getPost('password'));
                                            
                    $result = $this->getAuthService()->authenticate();

                    if ($result->isValid()) {
                        $this->Tables()->blacklist()->deleteFromBlacklist($ip_adress, $request->getPost('username'));    
                        $this->flashMessenger()->addSuccessMessage($this->translate('login_success'));
                    
                        if ($request->getPost('remember') == 1 ) {
                            $this->getSessionStorage()
                                    ->setRememberMe(1);

                            $this->getAuthService()->setStorage($this->getSessionStorage());
                        }

                        $user = $this->Tables()->user()->getByUsername($result->getIdentity());
                        $this->getAuthService()->getStorage()->write($user);
                        
                        return $this->redirect()->toRoute('movies', array('action' => 'index', 'lang' => $this->language));
                    }
                    else{
                        $attempts = $this->Tables()->blacklist()->updateBlacklist($ip_adress, $request->getPost('username'));
                        $this->flashMessenger()->addErrorMessage(sprintf($this->translate('login_data_wrong'), ''.(3-$attempts)));

                        if($attempts>=3){
                            $this->flashMessenger()->addErrorMessage(sprintf($this->translate('login_blocked_now'),60));
                        }
                    }
                }
                else{
                    $this->flashMessenger()->addErrorMessage($this->translate('login_data_missing'));
                }
            }
            else{
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('login_blocked'),$blacklist_entry->getTimeLeft()));
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