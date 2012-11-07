<?php
class SQL_QueryBuilder
{
	protected 	$fields,
				$tables,
				$conditions,
				$append;
				
	public function __construct($params = array())
	{
		foreach($params as $key => $value)
			if(property_exists(SQL_QueryBuilder,$key))
				$this->$key = $value;
	}
	
	public function addFields($value)
	{
		if(!empty($value))
			if(empty($this->fields))
				$this->fields = ' '.$value.' ';
			else
				$this->fields .= ", $value ";
	}
	
	public function addTables($value)
	{
		if(!empty($value))
			if(empty($this->tables))
				$this->tables = " $value ";
			else
				$this->tables .= ", $value ";
	}
	
	public function addCondition($value,$connective = 'AND')
	{
		if(!empty($value))
			if(empty($this->conditions))
				$this->conditions = " $value ";
			else
				$this->conditions .= " $connective $value ";
	}
	
	public function append($value = '')
	{
		if(!empty($value))
			$this->append = " $value ";
	}
	public function getQuery()
	{
		$sql = '';
		if(!empty($this->fields))
		{
			$sql .= "SELECT {$this->fields} ";
			if(!empty($this->tables))
				$sql .= "FROM {$this->tables} ";
			if(!empty($this->conditions))
				$sql .= "WHERE {$this->conditions} ";
			$sql .= " {$this->append} ";
		}
		return $sql;
	}
	
}
?>