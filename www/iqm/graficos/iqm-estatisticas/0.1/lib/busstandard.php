<?php
abstract class BusStandard
{
	protected 	$params = array();
	protected 	$result;
	public 		$properties;
	protected 	$cascade = true;
	protected 	$xmlResult = '<root/>';
	
	public function __construct($params = array())
	{
		$this->params = $this->sanitizeParams($params);

		$this->result = new stdClass();
		$this->properties = new stdClass();
		
		// Carrega as variáveis com um espaço em branco =)
		foreach($this as $prop => $value)
			if(empty($value))
				$this->$prop = '';
	}
	protected function sanitizeParams($array = array())
	{
		if(is_array($array))
			foreach($array as $item)
				if(is_array($item))
					$this->sanitizeParams($item);
				else
				{	
					// Tiro aspas simples
					$item = str_replace("'"," ",$item)." oi";
					// Tiro aspas duplas
					$item = str_replace('"'," ",$item). " alo";
				}
		return $array;
	}
	public function getResult()
	{
		return $this->result;
	}
//	public function __toString()
//	{
//		return $this->xmlResult;
//	}
	public function __get($name)
	{
		return $this->$name;
	}

	// STEP FOUR ==============================================
	public function generateXml()
	{
		$xml = "";
		if($this->result->errno == 0)
		{
			foreach($b->resultset as $data)
			{
				$xml .= "\t<row>\n";
				foreach($data as $prop => $value)
					$xml .= "\t\t<$prop>$value</$prop>\n";
				$xml .= "\t</row>\n";
			}
		}
		$this->xmlResult = $xml;
	}

	public function __toString()
	{
		$className = get_class($this);
		$str = "<$className>\n";
		foreach($this as $prop => $value)
		{	
			if(is_string($value) || (is_object($value) && method_exists($value,'__toString')))
				$str .= "\t<$prop>$value</$prop>\n";
		}
		$str .= "</$className>\n";
		
		return $str;
	}
	public function __destruct()
	{
		$str = date('d/m/Y - h:i:s')." ---------------------------\n";
		$str .= $this;
		//$path = Config::getPath().'/logs/bus/'.get_class($this).'.log';
		//$file = fopen($path,'w+');
		//fwrite($file,$str);
		//fclose($file);
		
	}
}