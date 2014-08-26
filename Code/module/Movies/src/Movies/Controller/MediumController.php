<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Medium;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MediumController extends AbstractActionController
{
    protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\MediumTable');
        }
        return $this->table;
    }
}