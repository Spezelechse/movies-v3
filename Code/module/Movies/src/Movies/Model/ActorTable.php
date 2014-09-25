<?php
namespace Movies\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;

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
		$sqlSelect->where(array('Medium_has_Actor.medium_id'=>$id));
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
            if($result->year_of_birth>0){
                $names.=$result->name.'#'.$result->year_of_birth.PHP_EOL;
            }
            else{
                $names.=$result->name.PHP_EOL;
            }
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
        $strip = new StripTags();

        foreach ($actors as $key => $actor) {
                $actor = $strip->filter($trim->filter($actor));

                $actor = explode('#',$actor);
                $year = (isset($actor[1])) ? (int)$actor[1] : 0;
                $actor = $actor[0];

                $actor_id = 0;

                $select = $this->tableGateway->getSql()->select();

                $select ->columns(array('id'))
                        ->where(array('Actor.name'=>$actor))
                        ->where(array('Actor.year_of_birth'=>$year));

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
            $strip = new StripTags();

            $actors = explode(PHP_EOL, $actors_text);
            $roles = explode(PHP_EOL, $roles_text);

            foreach ($actors as $key => $actor) {
                $actor = $strip->filter($trim->filter($actor));
                
                $actor = explode('#',$actor);
                $year = (isset($actor[1])) ? (int)$actor[1] : 0;
                $actor = $actor[0];

                $actor_id = 0;

                $select = $this->tableGateway->getSql()->select();

                $select ->columns(array('*'))
                        ->where(array('Actor.name'=>$actor))
                        ->where('Actor.year_of_birth = \'0\'');

                $result_zero = $this->tableGateway->selectWith($select)->current();

                $select = $this->tableGateway->getSql()->select();
                
                $select ->columns(array('*'))
                        ->where(array('Actor.name'=>$actor))
                        ->where(array('Actor.year_of_birth'=>$year));

                $result_year = $this->tableGateway->selectWith($select)->current();
                
                if($result_year){
                    $actor_id = $result_year->id;
                    if($result_year->year_of_birth==0&&$year>0){
                        $result_year->year_of_birth=$year;

                        $this->save($result_year);
                    }
                }
                else if($result_zero){
                    $actor_id = $result_zero->id;
                    if($year>0){
                        $result_zero->year_of_birth=$year;

                        $this->save($result_zero);
                    }
                }
                else{
                    $new_actor = new Actor();
                    $new_actor->name = $actor;
                    $new_actor->year_of_birth = $year;

                    $actor_id=$this->save($new_actor);
                }

                $insertConnect->values(array('medium_id'=>$medium_id, 'actor_id'=>$actor_id, 'role'=>$strip->filter($trim->filter($roles[$key]))));

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
