<?php
namespace Movies\Model;

class Blacklist extends BaseObject
{
	public $id;
	public $time;
	public $ip;
	public $try;

	public function exchangeArray($data)
	{
		$this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->time = (isset($data['time'])) ? $data['time'] : null;
		$this->ip = (isset($data['ip'])) ? $data['ip'] : null;
		$this->try = (isset($data['try'])) ? $data['try'] : null;
	}
}