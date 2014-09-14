<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Filter\StringTrim;

class TypeTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Type') ? TRUE : FALSE;
    }

    public function getByName($name){
        $id = 0;
        $trim = new StringTrim();

        $name = $trim($name);

        $select = $this->tableGateway->getSql()->select();

        $select ->columns(array('id'))
                ->where('Type.name_en = \''.$name.'\'');

        $result = $this->tableGateway->selectWith($select)->current();
        
        if($result){
            $id = $result->id;
        }

        return $id;
    }

    public function fetchAllForSelect($language){
        $result_set = $this->fetchAll();
        $values=array();

        foreach($result_set as $result){                
            $values[$result->id] = $result->getName($language);
        }

        return $values;
    }
}