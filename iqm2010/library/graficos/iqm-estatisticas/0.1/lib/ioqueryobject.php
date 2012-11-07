<?php
class IOQueryObject extends QueryObject
{
	protected $result;
	
	public function getResult()
	{
		return $this->result;
	}
}