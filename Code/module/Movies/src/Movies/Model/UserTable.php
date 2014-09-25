<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;

class UserTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\User') ? TRUE : FALSE;
    }

    public function getByUsername($username){
        $trim = new StringTrim();
        $strip = new StripTags();

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('*'));
        $sqlSelect->where(array('username'=>$strip->filter($trim->filter($username))));

        $resultSet = $this->tableGateway->selectWith($sqlSelect);

        return $resultSet->current();
    }

    public function fetchAllForSelect(){
        $result_set = $this->fetchAll(function ($select) {
             $select->order('username ASC');
        });
        $values=array();

        foreach($result_set as $result){                
            $values[$result->id] = $result->username;
        }

        return $values;
    }

    public function fetchAllForList_Select($admin_id, $user_id){
        $sql = new Sql($this->tableGateway->getAdapter());

        $select = $sql->select();
        $select ->from('User')
                ->columns(array('*'))
                ->where->notEqualTo('id',$admin_id)
                ->where->notEqualTo('id',$user_id);

        return $select;
    }
}