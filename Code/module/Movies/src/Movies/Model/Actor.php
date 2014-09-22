<?php
namespace Movies\Model;

class Actor extends BaseObject
{
	public $id;
	public $name;
	public $year_of_birth;
	public $role;

	public function exchangeArray($data)
	{
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->role = (isset($data['role'])) ? $data['role'] : null;
		$this->year_of_birth = (isset($data['year_of_birth'])) ? $data['year_of_birth'] : null;
	}

	public function toArray()
	{
		$array = parent::toArray();

		array_pop($array);

		return $array;
	}
}