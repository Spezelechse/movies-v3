<?php
namespace Movies\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Medium extends BaseObject
{
	public $id;
	public $title_de;
	public $title_en;
	public $type_id;
	public $owner_id;
	public $cover_file;
	public $cover_source;
	public $duration;
	public $fsk;
	public $premiere;
	public $num_disks;
	public $content_de;
	public $content_en;
	public $dvd_or_bluray;

	public $genre;
	public $actors;
	public $cover;
	public $director;
	public $publisher;
	public $type;
	public $owner;

	private $inputFilter;

	public function exchangeArray($data)
	{
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->title_de = (isset($data['title_de'])) ? $data['title_de'] : null;
		$this->title_en = (isset($data['title_en'])) ? $data['title_en'] : null;
		$this->type_id = (isset($data['type_id'])) ? $data['type_id'] : null;
		$this->owner_id = (isset($data['owner_id'])) ? $data['owner_id'] : null;
		$this->cover_file = (isset($data['cover_file'])) ? $data['cover_file'] : null;
		$this->cover_source = (isset($data['cover_source'])) ? $data['cover_source'] : null;
		$this->duration = (isset($data['duration'])) ? $data['duration'] : null;
		$this->fsk = (isset($data['fsk'])) ? $data['fsk'] : null;
		$this->premiere = (isset($data['premiere'])) ? $data['premiere'] : null;
		$this->num_disks = (isset($data['num_disks'])) ? $data['num_disks'] : null;
		$this->content_de = (isset($data['content_de'])) ? $data['content_de'] : null;
		$this->content_en = (isset($data['content_en'])) ? $data['content_en'] : null;
		$this->dvd_or_bluray = (isset($data['dvd_or_bluray'])) ? $data['dvd_or_bluray'] : null;
	}

	public function toArray(){
		$array = parent::toArray();

		for($i=0; $i<8; $i++){
			array_pop($array);
		}

		return $array;
	}

	public function setGenre($data){
		$this->genre=$data;
	}

	public function setActors($data){
		$this->actors=$data;
	}

	public function setDirector($data){
		$this->director=$data;
	}

	public function setPublisher($data){
		$this->publisher=$data;
	}

	public function setType($data){
		$this->type=$data;
	}

	public function setOwner($data){
		$this->owner=$data;
	}

	public function getTitle($language){
		if($language=='de'||!isset($this->title_en)){
			return $this->title_de;
		}
		else{
			return $this->title_en;
		}
	}

	public function getContent($language){
		if($language=='de'||!isset($this->content_en)){
			return $this->content_de;
		}
		else{
			return $this->content_en;
		}
	}

    public function getCover(){
        $cover='';

        if($this->cover_file){
            $cover=$this->cover_file;
        }
        else{
            $cover='placeholder.png';
        }

        return $cover;
    }
    public function getCoverSource(){
        if($this->cover_source!=''){
            $beginning = substr($this->cover_source, 0, 7);

            if($beginning!='http://'&&$beginning!='https:/'){
                return 'http://'.$this->cover_source;
            }
        }
        return $this->cover_source;
    }

	public function getCoverSourceDomain(){
		if($this->cover_source!=''){
			$source=explode('/', $this->cover_source);

			if($source[0]=='http:'){
                return $source[2];
            }
            else{
                return $source[0];
            }
		}
		else{
			return '-';
		}
	}

    public function getInputFilter($type, $cover_name='')
    {
        if($type=='add'){
            $data=array(
                'id_required'=>false,
                'required'=>true,
            );
        }
        else if($type=='none'){
            $data=array(
                'id_required'=>false,
                'required'=>false,
            );
        }
        else{
            $data=array(
                'id_required'=>true,
                'required'=>true,
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
                'name'     => 'title_de',
                'required' => $data['required'],
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
                            'max'      => 150,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'title_en',
                'required' => $data['required'],
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
                            'max'      => 150,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'type_id',
                'required' => $data['required'],
                'filters'  => array(
                    array('name' => 'Digits'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'owner_id',
                'required' => $data['required'],
                'filters'  => array(
                    array('name' => 'Digits'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            )));

            if($cover_name!=''){
                $inputFilter->add($factory->createInput(array(
                    'name'     => 'cover_file',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'Zend\Filter\File\RenameUpload',
                                'options' => array(
                                    'target' => './public/img/cover/'.$cover_name,
                                    'overwrite' => true,
                                ),
                            ),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'Zend\Validator\File\ImageSize',
                            'options' => array(
                                'minWidth' => 200,
                                'minHeight' => 320,
                            ),
                        ),
                    ),
                )));
            }

            $inputFilter->add($factory->createInput(array(
                'name'     => 'cover_source',
                'required' => false,
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
                            'max'      => 200,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'duration',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Digits'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'fsk',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Digits'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                        	'haystack' => array(0,6,12,16,18),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'premiere',
                'required' => $data['required'],
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Date',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'num_disks',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Digits'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Digits',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'content_de',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'content_en',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'actors_text',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'roles_text',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'dvd_or_bluray',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Digits'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                        	'haystack' => array(1,2),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'genre',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'director',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'publisher',
                'required' => false,
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
