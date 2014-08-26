<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class PublisherTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Publisher') ? TRUE : FALSE;
    }

    public function fetchForMedium($id)
    {
    	$sqlSelect = $this->tableGateway->getSql()->select();
		$sqlSelect->columns(array('*'));
		$sqlSelect->join('Medium_has_Publisher', 'Medium_has_Publisher.publisher_id = Publisher.id', array(), 'left');
		$sqlSelect->where('Medium_has_Publisher.medium_id = '.$id);

		$resultSet = $this->tableGateway->selectWith($sqlSelect);

		return $resultSet;
    }

    public function fetchAllForSelect(){
        $result_set = $this->fetchAll();
        $values=array();

        foreach($result_set as $result){                
            $values[$result->id] = $result->name;
        }

        return $values;
    }
    
    public function fetchForPreselect($id){
        $result_set = $this->fetchForMedium($id);
        $values = array();

        foreach ($result_set as $result) {
            array_push($values, $result->id);    
        }

        return $values;
    }

    public function connectToMedium($medium_id, $publisher_ids){
        if(isset($medium_id)&&isset($publisher_ids)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $insert = $sql->insert();
            $insert ->into('Medium_has_Publisher')
                    ->columns(array('medium_id','publisher_id'));

            foreach($publisher_ids as $publisher_id){
                $insert->values(array('medium_id'=>$medium_id, 'publisher_id'=>$publisher_id));

                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
            }
        }
    }

    public function updateMediumConnection($medium_id, $publisher_ids){
        if(isset($medium_id)&&isset($publisher_ids)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $delete = $sql->delete();

            $delete ->from('Medium_has_Publisher')
                    ->where(array('medium_id'=>$medium_id));

            $statement = $sql->prepareStatementForSqlObject($delete);
            $statement->execute();

            $this->connectToMedium($medium_id, $publisher_ids);
        }
    }
}