<?php
/**
 * 
 * @package Movies
 * 
 */
namespace Movies\Controller;

use Movies\Model\Type;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TypeController extends AbstractActionController
{  
  protected $table;

    public function getTable()
    {
        if (!isset($this->table)) {
            $sm = $this->getServiceLocator();
            $this->table = $sm->get('Movies\Model\TypeTable');
        }
        return $this->table;
    }
}