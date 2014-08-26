<?php
namespace Movies\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
 
class TablesPlugin extends AbstractPlugin{
	private $serviceLocator;

	private function getServiceLocator()
	{
		if(!$this->serviceLocator){
			$this->serviceLocator = $this->getController()->getServiceLocator();
		}

		return $this->serviceLocator;
	}

    public function actor(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\ActorTable');
    }

    public function blacklist(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\BlacklistTable');
    }

    public function config(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\ConfigTable');
    }

    public function director(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\DirectorTable');
    }

    public function genre(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\GenreTable');
    }

    public function medium(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\MediumTable');
    }

    public function publisher(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\PublisherTable');
    }

    public function type(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\TypeTable');
    }

    public function user(){
        $sm = $this->getServiceLocator();
        return $sm->get('Movies\Model\UserTable');
    }
}