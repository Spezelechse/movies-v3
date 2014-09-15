<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;

class BlacklistTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Blacklist') ? TRUE : FALSE;
    }

    public function getByAttempt($ip, $try){
        $select = $this->tableGateway->getSql()->select();
        $select ->columns(array('*'))
        		->where('Blacklist.ip = \''.$ip.'\'')
        		->where('Blacklist.try = \''.$try.'\'');

        return $this->tableGateway->selectWith($select)->current();
    }

    public function cleanUp(){
    	$delete = $this->tableGateway->getSql()->delete();

    	$delete ->where('Blacklist.time < TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 HOUR))');

    	$this->tableGateway->deleteWith($delete);
    }
}