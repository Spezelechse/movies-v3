<?php
namespace Movies\Model;

class Genre extends BaseObject
{
	public $id;
	public $name_de;
	public $name_en;

	public function exchangeArray($data)
	{
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->name_de = (isset($data['name_de'])) ? $data['name_de'] : null;
		$this->name_en = (isset($data['name_en'])) ? $data['name_en'] : null;
	}

	public function getName($language){
		if($language=='de'||!isset($this->name_en)){
			return $this->name_de;
		}
		else{
			return $this->name_en;
		}
	}
}