<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Genre;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GenreController extends AbstractActionController
{
    protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\GenreTable');
        }
        return $this->table;
    }
}