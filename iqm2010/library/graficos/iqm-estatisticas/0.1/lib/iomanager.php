<?php 
/**
 * @author endelguimaraes
 *
 */
class IOManager
{
	protected $entity = 'DEFAULT';
	protected $action = 'DEFAULT';
	public $return = false;
	
	protected 	$params = array();
	protected 	$result;
	protected 	$resultset;
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
		
		$xml =  "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
		$xml .= "<response>";
		$xml .= "<metadata>";
		$xml .= "<error>Falta de parametros</error>";
		$xml .= "<errno>1</errno>";
		$xml .= "<lastInsertId>-1</lastInsertId>";
		$xml .= "<affectedRows>-1</affectedRows>";
		$xml .= "</metadata>";
		$xml .= "</response>";
		
		$this->xmlError = $xml;
		
		$this->result = new stdClass();
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
					$item = str_replace("'"," ",$item);
					// Tiro aspas duplas
					$item = str_replace('"'," ",$item);
				}
		return $array;
	}
	public function getResult()
	{
		return $this->result;
	}

	protected $xmlError = '';
	

	// FUNCAO QUE PROCESSA O REQUEST =========================
	public function run()
	{
		$action = $this->action;
		
		// Verifica se a classe realmente existe =============
		$obj = new IQ_Estatisticas($this->params);
	
		// Verifica se o mÃ©todo realmente existe =============
		if(method_exists($obj,$action))
		{
			$this->resultset 	= $obj->$action();
			$this->result 		= $obj->lastResultMeta;
		}
		else
		{
			// Se nÃ£o existir, lanÃ§a o erro e pÃ¡ra a execuÃ§Ã£o
			$this->output("Metodo $this->action nao existe",'IO03');
			return;
		}
		
		$this->toXml();
		
		$options = array(
			'titulo' 				=> $obj->titulo,
			'desc' 					=> $obj->descricao,
			'fields' 				=> $obj->headers,
			'mainAxisField' 		=> $obj->mainAxisField,
			'secondaryAxisField' 	=> $obj->secondaryAxisField
		);
		
		$result = $this->output($options);
		
		if($this->return)
			return $result;
		else
			echo $result;
		
	}
	protected function toXml()
	{
		$xml = "";
		foreach($this->resultset as $data)
		{
			$xml .= "\n\t<row ";
			foreach($data as $prop => $value)
				$xml .= "$prop='$value' ";
			$xml .= "/>";
		}
		$this->xmlResult = $xml;
	}
	// VERIFICA OS PARAMETROS NECESSARIOS ====================
	protected function isReady()
	{
		return ($this->entity != 'DEFAULT' && $this->action != 'DEFAULT');
	}
	
	// RETORNA O XML =========================================
	protected function output($options = array())
	{
		$mainAxisLabel = '';
		foreach($options['fields'] as $key => $value)
			if($key == $options['mainAxisField'])
			{
				$mainAxisLabel = $value;
				break;
			}

		// E agora, geramos o XML Final, com estrutura padronizada
		$xml =  "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
		$xml .= "<response>";
			$xml .= "<metadata affectedRows='{$this->result->affectedRows}' numFields='".count($options['fields'])."'>";
				$xml .= "<relatorio name='{$options['titulo']}' mainAxisField='{$options['mainAxisField']}' mainAxisLabel='{$mainAxisLabel}' secondaryAxisField='{$options['secondaryAxisField']}'>";
					$xml .= "<desc><![CDATA[ {$options['desc']} ]]></desc>";
				$xml .= "</relatorio>";
			$xml .= "</metadata>";
			$xml .= "<fMeta>";
			foreach($options['fields'] as $key => $value)
				$xml .= "<field name='$key' displayName='$value' />";
			$xml .= "</fMeta>";
			$xml .= "<data>";
				$xml .= $this->xmlResult; //esses sÃ£o os dados de retorno do objeto;
			$xml .= "</data>";
		$xml .= "</response>";
		
		return $xml;
	}
	
	// SETTERS ===============================================
	public function setAction($value = null)
	{
		if(is_string($value) && !empty($value))
			$this->action = $value;
	}
	public function setParams($value = null)
	{
		if(is_array($value) && !empty($value))
			$this->params = parent::sanitizeParams($value);
	}

} // Fim da classe IOManager