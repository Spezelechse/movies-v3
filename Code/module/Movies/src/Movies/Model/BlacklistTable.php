<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;

class BlacklistTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Blacklist') ? TRUE : FALSE;
    }
}