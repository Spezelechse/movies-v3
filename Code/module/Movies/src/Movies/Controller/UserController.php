<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\User;
use Movies\Form\UserForm;
use Zend\View\Model\ViewModel;

class UserController extends BasisController
{
    public function init()
    {
        parent::init();

        if(!$this->MoviesConfig()->get('public')){
            if(!$this->getAuthService()->hasIdentity()){
                return $this->redirect()->toRoute('auth', array('lang'=>$this->language, 'action'=>'login'));
            }
        }

        if(!$this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('movies', array('lang'=>$this->language)); 
        }
    }

    public function indexAction(){
        return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
    }

	public function changeDataAction(){
        $id=0;
        $username = $this->getAuthService()->getIdentity()->username;

        $oldData = $this->Tables()->user()->getByUsername($username);
        $id=$oldData->id;

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

                    if($post->toArray()['password']!=''){
                    	$user->hashPassword();
                	}
                	else{
                		$user->password=$oldData->password;
                	}
                	$user->rights=$oldData->rights;

                    $this->Tables()->user()->save($user);
                    
                    //return $this->redirect()->toRoute('movies', array('lang'=>$this->language, 'action'=>'index'));
                }
                else{
                    $this->showFormMessages($form);
                }
            }
            else{
                $user=$this->Tables()->user()->get($id);
                $user=$user->toArray();

                unset($user['password']);
                unset($user['rights']);

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
            
            return $this->redirect()->toRoute('movies', array('lang'=>$this->language));
        }
	}
}