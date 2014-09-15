<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class ConfigTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Config') ? TRUE : FALSE;
    }
    
    public function fetchAllForList_Select($language){
        $sql = new Sql($this->tableGateway->getAdapter());

        $select = $sql->select();
        $select ->from('Config')
                ->columns(array('*','description'=>'description_'.$language));

        return $select;
    }
}