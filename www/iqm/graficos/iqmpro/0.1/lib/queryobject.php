<?php
class QueryObject
{
	/*
	 * Essa eh uma classe de acesso rapido a banco de dados
	 * 
	 * Ela faz todo o grosso do trabalho de se conectar no banco, fazer a query
	 * e retornar os dados num array
	 * 
	 */

	protected $conexao;
	protected $params = array();
	
	public function __construct($params = array())
	{
		$this->conexao = Conexao::singleton(Config::DBHOST,Config::DBUSER,Config::DBPASS,Config::DBDB);
		$this->params = (array) $params;
	}
	public function query($sql,$return = false, $options = array())
	{
		$this->conexao->consulta($sql);
		if($return)
			return $this->toArray($options);
	}
	// Metodo de retorno em forma de Array
	public function toArray($options = array())
	{
		$defaults = array(
			'force_array' => false
		);
		$intersection = array_intersect_key($options,$defaults);
		$options = $defaults;
		foreach($intersection as $key => $value)
			$options[$key] = $value;

		// Crio o array
		$array = array();

		if($this->conexao->result->affectedRows  === 1)
			if($options['force_array'])
				$array[] = $this->conexao->fetch();
			else
				$array = $this->conexao->fetch();
		elseif($this->conexao->result->affectedRows > 1)
			while($array[] = $this->conexao->fetch())
			{
				// deixa em branco mesmo
			}
		// Limpo possiveis posicoes em branco
		foreach($array as $key => $value)
		{
			if(empty($value))
				unset($array[$key]);
		}
		// Aqui, retorno o array
		return $array;
	}
}