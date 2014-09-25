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
        		->where(array('Blacklist.ip'=>$ip))
        		->where(array('Blacklist.try'=>$try));

        return $this->tableGateway->selectWith($select)->current();
    }

    public function cleanUp(){
    	$delete = $this->tableGateway->getSql()->delete();

    	$delete ->where('Blacklist.time < TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 HOUR))');

    	$this->tableGateway->deleteWith($delete);
    }

    public function updateBlacklist($ip,$try){
        $entry = $this->getByAttempt($ip, $try);

        if($entry){
            $entry->attempts++;
        }
        else{
            $entry = new Blacklist();
            $entry->try = $try;
            $entry->ip = $ip;
            $entry->attempts = 1;
        }

        $this->save($entry);

        return $entry->attempts;
    }

    public function deleteFromBlacklist($ip, $try){
        $entry = $this->getByAttempt($ip, $try);

        if($entry){
            $this->delete($entry->id);
        }
    }

    public function checkBlacklist($ip, $try){
        $entry = $this->getByAttempt($ip, $try);

        if(!$entry){
            $entry = new Blacklist();
            $entry->time = time();
            $entry->attempts = 0;
        }

        return $entry;
    }
}