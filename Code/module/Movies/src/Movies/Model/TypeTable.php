<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;

class TypeTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Type') ? TRUE : FALSE;
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