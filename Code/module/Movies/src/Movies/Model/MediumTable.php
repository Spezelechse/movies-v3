<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class MediumTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Medium') ? TRUE : FALSE;
    }

    public function fetchAll()
    {
    	$resultSet = $this->tableGateway->select();

        return $resultSet;
    }

    public function fetchAllForList_Select($language){
		$sql = new Sql($this->tableGateway->getAdapter());
        $select_genre = $sql->select();
        $select_genre   ->from('Medium_has_Genre')
                        ->columns(array('medium_id'))
                        ->join('Genre', 'Genre.id = Medium_has_Genre.genre_id', array('id','genres'=>new Expression("GROUP_CONCAT(DISTINCT Genre.name_".$language." ORDER BY Genre.name_".$language." SEPARATOR ', ')")), 'left')
                        ->group('Medium_has_Genre.medium_id');

		$select = $sql->select();
		$select ->from('Medium')
			    ->columns(array('*','title'=>'title_'.$language,'content'=>'content_'.$language))
                ->join(array('Genres'=>$select_genre), 'Genres.medium_id = Medium.id', array('genres'), 'left')
                ->join('Type', 'Type.id = Medium.type_id', array('type'=>'name_'.$language), 'left')
                ->order('title ASC');

		return $select;
	}

    public function fetchAllForTable($language){
        $select =  $this->fetchAllForList_Select($language);
        $select->columns(array('*','title'=>'title_'.$language,'dvd_or_bluray'));

        $sql = new Sql($this->tableGateway->getAdapter());

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return $results;
    }
}