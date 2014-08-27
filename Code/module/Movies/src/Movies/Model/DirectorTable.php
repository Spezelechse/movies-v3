<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class DirectorTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Director') ? TRUE : FALSE;
    }

    public function fetchForMedium($id)
    {
    	$sqlSelect = $this->tableGateway->getSql()->select();
		$sqlSelect->columns(array('*'));
		$sqlSelect->join('Medium_has_Director', 'Medium_has_Director.director_id = Director.id', array(), 'left');
		$sqlSelect->where('Medium_has_Director.medium_id = '.$id);

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

    public function connectToMedium($medium_id, $director_ids){
        if(isset($medium_id)&&isset($director_ids)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $insert = $sql->insert();
            $insert ->into('Medium_has_Director')
                    ->columns(array('medium_id','director_id'));

            foreach($director_ids as $director_id){
                $insert->values(array('medium_id'=>$medium_id, 'director_id'=>$director_id));

                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
            }
        }
    }

    public function updateMediumConnection($medium_id, $director_ids){
        if(isset($medium_id)&&isset($director_ids)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $delete = $sql->delete();

            $delete ->from('Medium_has_Director')
                    ->where(array('medium_id'=>$medium_id));

            $statement = $sql->prepareStatementForSqlObject($delete);
            $statement->execute();

            $this->connectToMedium($medium_id, $director_ids);
        }
    }
}