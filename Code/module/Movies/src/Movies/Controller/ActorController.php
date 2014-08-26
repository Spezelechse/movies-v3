<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Actor;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ActorController extends AbstractActionController
{
    protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\ActorTable');
        }
        return $this->table;
    }
}