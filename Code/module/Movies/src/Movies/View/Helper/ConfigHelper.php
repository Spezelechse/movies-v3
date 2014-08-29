<?php
namespace Movies\View\Helper;
use Zend\View\Helper\AbstractHelper;
 
class ConfigHelper extends AbstractHelper
{
    private $serviceLocator;
    private $config;

    public function setServiceLocator($sm)
    {
        $this->serviceLocator=$sm;
    }

    public function __invoke()
    {
        if(!isset($this->config)){
            $table = $this->serviceLocator->get('Movies\Model\ConfigTable');
            $config = array();

            foreach($table->fetchAll() as $conf){
                $config[$conf->name]=$conf;
            }

            $this->config=$config;
        }

        return $this;
    }

    public function get($name)
    {
        if(isset($this->config[$name])){
            return $this->config[$name]->getData();
        }

        return null;
    }
}