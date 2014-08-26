<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Director;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DirectorController extends AbstractActionController
{
    protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\DirectorTable');
        }
        return $this->table;
    }
}