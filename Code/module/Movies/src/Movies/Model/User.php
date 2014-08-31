<?php
namespace Movies\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Crypt\Password\Bcrypt;

class User extends BaseObject
{
	public $id;
	public $name;
	public $surname;
	public $username;
    public $email;
	public $password;
	public $rights;

    private $rights_array;
	private $inputFilter;

	public function exchangeArray($data)
	{
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->surname = (isset($data['surname'])) ? $data['surname'] : null;
		$this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->password = (isset($data['password'])) ? $data['password'] : null;
		$this->rights = (isset($data['rights'])) ? $data['rights'] : null;
	}

    public function toArray(){
        $array = parent::toArray();

        array_pop($array);
        array_pop($array);
        
        return $array;
    }

    public function getRightsAssoc(){
        if(!isset($this->rights_array)){
            $values =  array('medium'=>array(), 'user'=>array(), 'page'=>array());
            $keys = array('add','edit','delete','import','export','add','edit','delete','config');
            $offset = 0;

            $user_rights = str_split(decbin($this->rights), 1);
            $offset+=9-count($user_rights);

            for($i=0; $i<count($user_rights)+$offset; $i++){
                $value=false;

                if($i>=$offset&&isset($user_rights[$i-$offset])&&$user_rights[$i-$offset]=='1'){
                    $value=true;
                }
                
                if($i<5){
                    $values['medium'][$keys[$i]]=$value;
                }
                else if($i>=5&&$i<8){
                    $values['user'][$keys[$i]]=$value;
                }
                else{
                    $values['page'][$keys[$i]]=$value;
                }            
            }

            $this->rights_array = $values;
        }

        return $this->rights_array;
    }

    public function hasRight($category, $name=''){
        $rights = $this->getRightsAssoc();

        if($name==''){
            foreach ($rights[$category] as $right) {
                if($right){
                    return true;
                }
            }
            return false;
        }
        else{        
            if(isset($rights[$category][$name])){
                return $rights[$category][$name];
            }
            else{
                return false;
            }
        }
    }

    public function hashPassword(){
        if($this->password!=''){
            $bcrypt = new Bcrypt();
            $this->password = $bcrypt->create($this->password);
        }
    }

    public function getRightsForSelect()
    {
        $values =  array('medium'=>array(), 'user'=>array(), 'page'=>array());
        $offset = 0;

        $user_rights = str_split(decbin($this->rights), 1);
        $offset+=9-count($user_rights);

        for($i=$offset; $i<count($user_rights)+$offset; $i++){
            if($i<5){
                if($user_rights[$i-$offset]=='1'){
                    array_push($values['medium'], $i);
                }
            }
            else if($i>=5&&$i<8){
                if($user_rights[$i-$offset]=='1'){
                    array_push($values['user'], $i-5);
                }
            }
            else{
                if($user_rights[$i-$offset]=='1'){
                    array_push($values['page'], $i-8);
                }
            }            
        }

        return $values;
    }

    public function setRightsFromSelect($data){
        $medium=array(0,0,0,0,0);
        $user=array(0,0,0);
        $page=array(0);

        if(isset($data['medium'])){
            foreach ($data['medium'] as $right) {
                $medium[$right]=1;
            }
        }

        if(isset($data['user'])){
            foreach ($data['user'] as $right) {
                $user[$right]=1;
            }
        }

        if(isset($data['page'])){
            foreach ($data['page'] as $right) {
                $page[$right]=1;
            }
        }

        $medium=implode('', $medium);
        $user=implode('', $user);
        $page=implode('', $page);

        $this->rights=bindec($medium.$user.$page);
    }

	public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter($type)
    {
        if($type=='add'){
            $data=array(
                'id_required'=>false,
                'password_required'=>true,
            );
        }
        else if($type=='update'){
            $data=array(
                'id_required'=>true,
                'password_required'=>false,
            );
        }
        else{
            $data=array(
                'id_required'=>true,
                'password_required'=>true,
            );
        }

        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => $data['id_required'],
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 30,
                        ),
                    ),
                    array(
                        'name' => 'Alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'surname',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 30,
                        ),
                    ),
                    array(
                        'name' => 'Alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'username',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 30,
                        ),
                    ),
                    array(
                        'name' => 'Alnum',
                    ),
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 60,
                        ),
                    ),
                    array('name' => 'EmailAddress'),
                ),
            )));

			$inputFilter->add($factory->createInput(array(
                'name'     => 'password',
                'required' => $data['password_required'],
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern' => '((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$?!:]).{7,20})',
                        ),
                    ),
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name'     => 'password-repeat',
                'required' => $data['password_required'],
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'identical',
                        'options' => array(
                            'token' => 'password'
                        )
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'medium-rights',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'user-rights',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'page-rights',
                'required' => false,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}