<?php 

/**
* conexao.lib.php
* Classe para conexão e execução de comandos de vários bancos de dados
* usando funções genéricas.
*
* @autor Alexandre I. Kopelevitch - alexandrekop@gmail.com
*
* @criado - 29/09/2008
* -------------------------------
* @(29/09/2008 | 10:00) até (29/09/2008 | 12:00) / Alexandre I. Kopelevitch
* @(30/09/2008 | 09:40) até (30/09/2008 | 16:00) / Alexandre I. Kopelevitch
* @(30/10/2008 | 09:00) até (30/10/2008 | 09:30) / Alexandre I. Kopelevitch
* @(18/11/2008 | 14:30) até (18/11/2008 | 15:00) / Alexandre I. Kopelevitch
* @tempo total - 10:40
* -------------------------------
* @deprecated - não
* @version 1.1
*/

class Conexao
{
	public  $log;    // log das ações realizadas
	protected $tipoBd; // 0 - MySQL || 1 - PostreSQL || 2 - MS SQLServer
	public  $con;    // Guarda a instância de conexão
	protected $host;   // Host onde o BD está localizado
	protected $usuario;// Usuário que irá acessar o BD
	protected $senha;  // Senha de acesso ao BD
	protected $bd;     // Nome do BD a ser conectado
	protected $rs;     // Recordset referente ao método consulta()
	protected $row;    // Linha de cada registro varrido de um Recordset (rs)
	protected $conectada = false;
	public $result;

	public static $instance; 

/* ------------------
 Construtor da classe.
 O banco padrão é setado para ser o MySQL caso nenhum parâmetro seja passado para especificar um outro tipo de BD.
------------------ */

	private function __construct($tipoBd = 0,$host='localhost',$usuario='root',$senha='root',$bd='loja')
	{

		$this->log     = "Objeto instanciado <br />";
		$this->host    = $host;
		$this->usuario = $usuario;
		$this->senha   = $senha;
		$this->bd      = $bd;
		
		$this->result = new stdClass();
	}// fim __construct

	public static function singleton($host='localhost',$usuario='root',$senha='root',$bd='loja')
	{ 
		if(!isset(self::$instance))
			self::$instance = new Conexao(0,$host,$usuario,$senha,$bd);
		return self::$instance;
	}
/* ------------------
 Destrutor da classe
 Fecha a conexão com o BD
------------------ */

	public function __destruct()
	{

		if ($this->con)
		{
			switch($this->tipoBd)
			{
				case 0:
					if ($this->rs)
					{
						//echo mysqli_error($this->con);
						mysqli_free_result($this->rs);
					}
					mysqli_close($this->con);
				break;
			}// fim SWITCH

		}// fim IF

	}// fim __destruct

/* ------------------
    Método conecta()
    Realiza a conexão propriamente dita. Ele é chamado por outras funções dentro da classe.
------------------ */

	public function conecta()
	{
		$this->log .= "M&eacute;todo conecta() chamado <br />";

		if(!$this->conectada)
			switch ($this->tipoBd)
			{
				case 0:
					if (!$this->con = mysqli_connect($this->host,$this->usuario,$this->senha,$this->bd))
					{
						$this->log .= "conex&atilde;o com o MySQL falhou. Veja o erro na linha de baixo: <br />";
						$this->log .= mysqli_connect_error()."<br />";
						return false;
					}// fim IF
					else $this->conectada = true;
				break;
			}// fim SWITCH

		return true;
	}// fim conectar()

/* ------------------
     Método consulta()
     consulta conteúdos do BD
------------------ */

	public function consulta($sql)
	{
		if (!$this->con) $this->conecta();

		switch ($this->tipoBd)
		{
			case 0:
				$this->log .= "M&eacute;todo consulta() chamado -> ".$sql."<br />";

				if (!$this->rs = mysqli_query($this->con,$sql))
				{
					$this->log .= "Houve uma falha em sua consulta <br />";
					$this->log .= mysqli_error($this->con)."<br />";
					
					$this->result->error 		= mysqli_error($this->con);
					$this->result->errno 		= mysqli_errno($this->con);
					$this->result->affectedRows = mysqli_affected_rows($this->con);
					$this->result->lastInsertId = mysqli_insert_id($this->con);
					$this->result->fieldCount 	= mysqli_field_count($this->con);
					
					return false;
				}// fim IF
				$this->result->error 		= mysqli_error($this->con);
				$this->result->errno 		= mysqli_errno($this->con);
				$this->result->affectedRows = mysqli_affected_rows($this->con);
				$this->result->lastInsertId = mysqli_insert_id($this->con);
				$this->result->fieldCount 	= mysqli_field_count($this->con);
			break;
		}// fim SWITCH

		return true;
	}
	public function multiConsulta($sql)
	{
		if (!$this->con) $this->conecta();

		switch ($this->tipoBd)
		{
			case 0:
				$this->log .= "M&eacute;todo consulta() chamado -> ".$sql."<br />";

				if (!$this->rs = mysqli_multi_query($this->con,$sql))
				{
					$this->log .= "Houve uma falha em sua consulta <br />";
					$this->log .= mysqli_error($this->con)."<br />";
					
					$this->result->error 		= mysqli_error($this->con);
					$this->result->errno 		= mysqli_errno($this->con);
					$this->result->affectedRows = mysqli_affected_rows($this->con);
					$this->result->lastInsertId = mysqli_insert_id($this->con);
					$this->result->fieldCount 	= mysqli_field_count($this->con);
					
					return false;
				}// fim IF
				$this->result->error 		= mysqli_error($this->con);
				$this->result->errno 		= mysqli_errno($this->con);
				$this->result->affectedRows = mysqli_affected_rows($this->con);
				$this->result->lastInsertId = mysqli_insert_id($this->con);
				$this->result->fieldCount 	= mysqli_field_count($this->con);
			break;
		}// fim SWITCH

		return true;
	}
/* ------------------
     Método fetch()
     varre os dados guardados no Recordset (rs) gerado pelo método consulta()
------------------ */

	public function fetch()
	{

		if (!$this->con) $this->conecta();

		switch ($this->tipoBd)
		{
			case 0:
				$this->log .= "M&eacute;todo fetch() chamado<br />";
				if(mysqli_affected_rows($this->con) === 0)
				{
					$this->log .= "Nenhum registro a ser varrido pelo m&eacute;todo fetch() <br />";
					return false; 
				}
				else
				{
					//if(mysqli_affected_rows($this->con) === 1)
					$this->row = mysqli_fetch_assoc($this->rs);
					return $this->row;
				}// fim IF
			break;
		}// fim SWITCH

		return true;
	}

	
	/**
	 * 
	 * @param $className nome da classe que será usada para retornar os resultados
	 * @return RecordSet
	 * @since 1.1
	 */
	public function fetchObject($className = 'stdClass')
	{

		if (!$this->con) $this->conecta();

		switch ($this->tipoBd)
		{
			case 0:
				$this->log .= "M&eacute;todo fetch() chamado<br />";
				
				if (mysqli_affected_rows($this->con) === 0)
				{
					$this->log .= "Nenhum registro a ser varrido pelo m&eacute;todo fetch() <br />";
					return false;
				}
				else
				{
					$this->row = mysqli_fetch_object($this->rs,$className);
					foreach((array)$this->row as $prop => $value)
					{
						// Tirando aspas simples
						$value = str_replace('\'',"",$value);
						// Tirando aspas duplas
						$value = str_replace('"',"",$value);
						
						$this->row->$prop = $value;
					}
					return $this->row;
				}// fim IF
			break;
		}// fim SWITCH

		return true;
	}
	

/* ------------------
    Método executa()
    Executa comandos como INSERT, UPDATE e DELETE
------------------ */

	public function executa($sql = 'SELECT true')
	{
		if (!$this->con) $this->conecta();

		switch($this->tipoBd)
		{
			case 0:
				$this->log .= "M&eacute;todo executa() chamado -> ".$sql."<br />";

				if (!mysqli_query($this->con,$sql))
				{
					$this->log .= "Houve um erro na execu&ccedil;&atilde;o do comando: ".$sql."<br />";
					$this->log .= mysqli_error($this->con)."<br />";
					
					$this->result->error 		= mysqli_error($this->con);
					$this->result->errno 		= mysqli_errno($this->con);
					$this->result->affectedRows = mysqli_affected_rows($this->con);
					$this->result->lastInsertId = mysqli_insert_id($this->con);
					$this->result->fieldCount 	= mysqli_field_count($this->con);
					
					return false;
				}// fim IF
				$this->result->error 		= mysqli_error($this->con);
				$this->result->errno 		= mysqli_errno($this->con);
				$this->result->affectedRows = mysqli_affected_rows($this->con);
				$this->result->lastInsertId = mysqli_insert_id($this->con);
				$this->result->fieldCount 	= mysqli_field_count($this->con);
			break;
		}// fim SWITCH
		
		return true;
	}

/* ------------------
    Método registrosAfetados()
    Verifica o número de registros afetados com o método executa()
------------------ */

	public function registrosAfetados()
	{
		$this->log .= "M&eacute;todo registrosAfetados() chamado <br />";

		if (!$this->con)
		{
			$this->log .= "A conex&atilde;o com o BD não existe<br />";
			return false;
		}// fim IF

		switch($this->tipoBd)
		{
			case 0:
				return mysqli_affected_rows($this->con);
			break;
		}// fim SWITCH

	}
	public function isConectada()
	{
		return $this->conectada;
	}
	public function getLastInsertId()
	{
		return $this->lastInsertId;
	}

}//fim classe Conexao

?>