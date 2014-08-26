<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Publisher;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PublisherController extends AbstractActionController
{
    protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\PublisherTable');
        }
        return $this->table;
    }
}