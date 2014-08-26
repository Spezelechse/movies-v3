<?php
namespace Movies\Model;

abstract class BaseObject
{
	public function __construct($data=array())
	{
		$this->exchangeArray($data);
	}

	public function toArray()
	{
		return (array) $this;
	}
}