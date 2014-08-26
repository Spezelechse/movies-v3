<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Config;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ConfigController extends AbstractActionController
{
    protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\ConfigTable');
        }
        return $this->table;
    }
}