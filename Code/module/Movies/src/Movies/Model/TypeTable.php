<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;

class TypeTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Type') ? TRUE : FALSE;
    }

    public function getByName($name){
        $id = 0;
        $trim = new StringTrim();
        $strip = new StripTags();

        $name = $strip->filter($trim->filter($name));

        $select = $this->tableGateway->getSql()->select();

        $select ->columns(array('id'))
                ->where(array('Type.name_en'=>$name));

        $result = $this->tableGateway->selectWith($select)->current();
        
        if($result){
            $id = $result->id;
        }

        return (int)$id;
    }

    public function import($data){
        $id = $this->getByName($data->en);
        $trim = new StringTrim();
        $strip = new StripTags();
        
        if($id==0){
            $new = new Type();
            $new->name_en = $strip->filter($trim->filter($data->en));
            $new->name_de = $strip->filter($trim->filter($data->de));
            $id = $this->save($new);
        }

        return (int)$id;
    }

    public function fetchAllForSelect($language='en'){
        $result_set = $this->fetchAll(function ($select) use ($language) {
             $select->order('name_'.$language.' ASC');
        });
        $values=array();

        foreach($result_set as $result){                
            $values[$result->id] = $result->getName($language);
        }

        return $values;
    }
}