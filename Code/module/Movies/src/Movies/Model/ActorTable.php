<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Filter\StringTrim;

class ActorTable extends BaseTable
{
    protected function checkObjectType($object){
        return (get_class($object)=='Movies\Model\Actor') ? TRUE : FALSE;
    }

    public function fetchForMedium($id)
    {
    	$sqlSelect = $this->tableGateway->getSql()->select();
		$sqlSelect->columns(array('*'));
		$sqlSelect->join('Medium_has_Actor', 'Medium_has_Actor.actor_id = Actor.id', array('role'), 'left');
		$sqlSelect->where('Medium_has_Actor.medium_id = '.$id);
        $sqlSelect->order('name ASC');

		$resultSet = $this->tableGateway->selectWith($sqlSelect);

		return $resultSet;
    }

    public function fetchForEdit($id)
    {
        $resultSet = $this->fetchForMedium($id);

        $names='';
        $roles='';

        foreach ($resultSet as $result) {
            $names.=$result->name.PHP_EOL;
            $roles.=$result->role.PHP_EOL;
        }

        $names=substr($names,0,strlen($names)-1);
        $roles=substr($roles,0,strlen($roles)-1);

        return array('names'=>$names,'roles'=>$roles);
    }

    public function getActorIdsFromText($text=''){
        $actors = explode(PHP_EOL, $text);
        $ids = array();
        $trim = new StringTrim();

        foreach ($actors as $key => $actor) {
                $actor = $trim($actor);
                $actor_id = 0;

                $select = $this->tableGateway->getSql()->select();

                $select ->columns(array('id'))
                        ->where('Actor.name = \''.$actor.'\'');

                $result = $this->tableGateway->selectWith($select)->current();
                if($result){
                    array_push($ids, $result->id);
                }
        }

        return $ids;
    }

    public function connectToMedium($medium_id, $actors_text, $roles_text){
        if(isset($medium_id)&&isset($actors_text)&&isset($roles_text)){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);

            $insertConnect = $sql->insert();
            $insertConnect  ->into('Medium_has_Actor')
                            ->columns(array('medium_id','actor_id','role'));

            $trim = new StringTrim();

            $actors = explode(PHP_EOL, $actors_text);
            $roles = explode(PHP_EOL, $roles_text);

            foreach ($actors as $key => $actor) {
                $actor = $trim($actor);
                $actor_id = 0;

                $select = $this->tableGateway->getSql()->select();

                $select ->columns(array('*'))
                        ->where('Actor.name = \''.$actor.'\'');

                $result = $this->tableGateway->selectWith($select)->current();
                
                if($result){
                    $actor_id = $result->id;
                }
                else{
                    $new_actor = new Actor();
                    $new_actor->name = $actor;

                    $actor_id=$this->save($new_actor);
                }

                $insertConnect->values(array('medium_id'=>$medium_id, 'actor_id'=>$actor_id, 'role'=>$trim($roles[$key])));

                $statement = $sql->prepareStatementForSqlObject($insertConnect);
                $statement->execute();
            }
        }
    }

    public function updateMediumConnection($medium_id, $actors_text, $roles_text){
        if(isset($medium_id)&&isset($actors_text)&&isset($roles_text)){
            $sql = new Sql($this->tableGateway->getAdapter());

            $delete = $sql->delete();

            $delete ->from('Medium_has_Actor')
                    ->where(array('medium_id'=>$medium_id));

            $statement = $sql->prepareStatementForSqlObject($delete);
            $statement->execute();

            $this->connectToMedium($medium_id, $actors_text, $roles_text);
        }
    }
}