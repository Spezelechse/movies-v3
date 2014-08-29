<?php
namespace Movies\Model;

class Config extends BaseObject
{
	public $id;
	public $name;
	public $data;
	public $type;

	public function exchangeArray($data)
	{
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->data = (isset($data['data'])) ? $data['data'] : null;
		$this->type = (isset($data['type'])) ? $data['type'] : null;
	}

	public function getData()
	{
		if($this->type=='boolean')
		{
			if($this->data=='true'){
				return true;
			}
			else{
				return false;
			}
		}
		else if($this->type=='int')
		{
			return (int)$this->data;
		}
		else
		{
			return $this->data;
		}
	}
}