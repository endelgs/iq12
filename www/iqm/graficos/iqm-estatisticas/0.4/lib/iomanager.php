<?php 
/**
 * @author endelguimaraes
 *
 */
class IOManager
{
	protected 	$entity = 'DEFAULT';
	protected 	$action = 'DEFAULT';
	public 		$return = false;
	
	protected 	$results;
	protected 	$series;
	public 		$properties;
	protected 	$xmlResult = '<root/>';
	protected 	$xmlError = '';
		
	public function __construct()
	{
		$this->result = new stdClass();
		$this->properties = new stdClass();
		
		// Carrega as variaveis com um espaco em branco =)
		foreach($this as $prop => $value)
			if(empty($value))
				$this->$prop = '';
		
		$this->result = new stdClass();
	}
	public function getResult()
	{
		return $this->result;
	}

	// FUNCAO QUE PROCESSA O REQUEST =========================
	public function run()
	{
		$obj = new IQ_Estatisticas();

		//foreach((array) $this->actions as $action)
		{
			// Verifica se o metodo realmente existe =============
			if(method_exists($obj,$this->action))
			{
				$action = $this->action;
				$r 				= $obj->$action();
				$result['meta'] = array(
					'titulo' 				=> $obj->titulo,
					'desc' 					=> $obj->descricao,
					'fields' 				=> $obj->headers,
					'numFields'				=> count($obj->headers),
					'mainAxisField' 		=> $obj->mainAxisField,
					'secondaryAxisFields' 	=> $obj->secondaryAxisFields,
					'affectedRows'			=> count($r)
				);
				$result['data'] = $r;
				$this->series = $result;
			}
		}
		$this->toXml();
		$result = $this->output();
		
		if($this->return)
			return $result;
		else
			echo $result;
		
	}
	protected function xmlNode($array = array(),$level = 0)
	{
		
		$xml = "\n";
		foreach($array as $key => $value)
		{
			for($i = 0; $i < $level; $i++)
				$xml .= "\t";
			$xml .= (is_numeric($key))?"<row>":"<$key>";
			if(is_array($value))
				$xml .= $this->xmlNode($value,$level + 1);
			else
				$xml .= $value;
			$xml .= (is_numeric($key))?"</row>\n":"</$key>\n";
			
			for($i = 0; $i < $level; $i++)
				$xml .= "\t";
			//$xml .= "\n";
		}
		return $xml;
	}
	protected function toXml()
	{
		$xml = "";
		//foreach((array)$this->series as $key => $series)
		{
			
			$xml .=  $this->xmlNode($this->series);
//			$xml .= "\n\t<series>";
//			$xml .= "\n\t\t<meta>";
//			foreach($series['meta'] as $prop => $value)
//				if(is_array($value))
//				{
//					$xml .= "\n\t\t\t<$prop>";
//						foreach($value as $p => $v)
//							$xml .="\n\t\t\t\t<$p>$v</$p>";
//					$xml .= "\n\t\t\t</$prop>";
//				}
//				else
//					$xml .="\n\t\t\t<$prop>$value</$prop>";
//			$xml .= "\n\t\t</meta>";
//			foreach((array)$series['data'] as $data)
//			{
//				$xml .= "\n\t\t<row> ";
//				foreach((array)$data as $prop => $value)
//					$xml .= "\n\t\t\t<$prop>$value</$prop>";
//				$xml .= "\n\t\t</row>";
//			}
//			$xml .= "\n\t</series>"; 

		}
		
		$this->xmlResult = $xml;
	}
	// VERIFICA OS PARAMETROS NECESSARIOS ====================
	protected function isReady()
	{
		return ($this->entities != 'DEFAULT' && $this->actions != 'DEFAULT');
	}

	// RETORNA O XML =========================================
	protected function output($options = array())
	{

		// E agora, geramos o XML Final, com estrutura padronizada
		$xml =  "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>";
		$xml .= "\n<response>";
		$xml .= $this->xmlResult;
		$xml .= "\n</response>";
		
		return $xml;
	}
	
	// SETTERS ===============================================
	public function setAction($value = null)
	{
		$this->action = $value;
	}
} // Fim da classe IOManager