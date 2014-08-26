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
			    ->columns(array('id','title'=>'title_'.$language))
                ->join(array('Genres'=>$select_genre), 'Genres.medium_id = Medium.id', array('genres'), 'left');

		return $select;
	}
}