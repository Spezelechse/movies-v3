<?php
namespace Movies\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
 
class ConfigPlugin extends AbstractPlugin{
	private $serviceLocator;
    private $config;

	private function getServiceLocator()
	{
		if(!$this->serviceLocator){
			$this->serviceLocator = $this->getController()->getServiceLocator();
		}

		return $this->serviceLocator;
	}

    public function get($name)
    {
        if(!isset($this->config)){
            $table = $this->getServiceLocator()->get('Movies\Model\ConfigTable');
            $config = array();

            foreach($table->fetchAll() as $conf){
                $config[$conf->name]=$conf;
            }

            $this->config=$config;
        }

        if(isset($this->config[$name])){
            return $this->config[$name]->getData();
        }

        return null;
    }
}