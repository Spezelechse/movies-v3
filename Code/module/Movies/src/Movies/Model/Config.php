<?php
namespace Movies\Model;

class Config extends BaseObject
{
	public $id;
	public $name;
	public $data;

	public function exchangeArray($data)
	{
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->data = (isset($data['data'])) ? $data['data'] : null;
	}
}