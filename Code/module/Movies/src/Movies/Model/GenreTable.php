<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;

class GenreTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Genre') ? TRUE : FALSE;
    }

    public function getByName($name){
        $id = 0;
        $trim = new StringTrim();
        $strip = new StripTags();

        $name = $strip->filter($trim->filter($name));

        $select = $this->tableGateway->getSql()->select();

        $select ->columns(array('id'))
                ->where(array('Genre.name_en'=>$name));

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
            $new = new Genre();
            $new->name_en = $strip->filter($trim->filter($data->en));
            $new->name_de = $strip->filter($trim->filter($data->de));
            $id = $this->save($new);
        }

        return (int)$id;
    }

    public function fetchForMedium($id)
    {
    	$sqlSelect = $this->tableGateway->getSql()->select();
		$sqlSelect->columns(array('*'));
		$sqlSelect->join('Medium_has_Genre', 'Medium_has_Genre.genre_id = Genre.id', array(), 'left');
		$sqlSelect->where(array('Medium_has_Genre.medium_id'=>$id));

		$resultSet = $this->tableGateway->selectWith($sqlSelect);

		return $resultSet;
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

    public function fetchForPreselect($id){
        $result_set = $this->fetchForMedium($id);
        $values = array();

        foreach ($result_set as $result) {
            array_push($values, $result->id);    
        }

        return $values;
    }

    public function connectToMedium($medium_id, $genre_ids){
        if(isset($medium_id)&&isset($genre_ids)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $insert = $sql->insert();
            $insert ->into('Medium_has_Genre')
                    ->columns(array('medium_id','genre_id'));

            foreach($genre_ids as $genre_id){
                $insert->values(array('medium_id'=>$medium_id, 'genre_id'=>$genre_id));

                $statement = $sql->prepareStatementForSqlObject($insert);
                $statement->execute();
            }
        }
    }

    public function updateMediumConnection($medium_id, $genre_ids){
        if(isset($medium_id)&&isset($genre_ids)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $delete = $sql->delete();

            $delete ->from('Medium_has_Genre')
                    ->where(array('medium_id'=>$medium_id));

            $statement = $sql->prepareStatementForSqlObject($delete);
            $statement->execute();

            $this->connectToMedium($medium_id, $genre_ids);
        }
    }
}