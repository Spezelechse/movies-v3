<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

abstract class BaseTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($closure = null)
    {
        if($closure==null){
            $resultSet = $this->tableGateway->select();
        }
        else{
            $resultSet = $this->tableGateway->select($closure);
        }
        return $resultSet;
    }

    public function get($id)
    {
        $id  = (int) $id;
        if($id!=0){
            $rowset = $this->tableGateway->select(array('id' => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            return $row;
        }
    }

    public function save($object)
    {
        if($this->checkObjectType($object))
        {
            $data = $object->toArray();

            $id = (int)$object->id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
                return $this->tableGateway->lastInsertValue;
            } else {
                if ($this->get($id)) {
                    $this->tableGateway->update($data, array('id' => $id));
                } else {
                    throw new \Exception('Row id does not exist');
                }
            }
        }
        else{
            throw new \Exception('Object is of wrong class: '.get_class($object));
        }
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

    public function getSql()
    {
        return new Sql($this->tableGateway->getAdapter());
    }

    abstract protected function checkObjectType($object);
}