<?php
class IQ_Usuario extends IOQueryObject
{
	public function login()
	{
		$user = StringUtil::antiInjection($this->params['login']);
		$pass = md5(StringUtil::antiInjection($this->params['senha']));

		$sql = "SELECT `id_pessoa`,	`professor_pass` FROM `professor_credencial` WHERE `professor_login` = '{$user}' LIMIT 1";
		
		$pessoa = $this->query($sql,true);

		if(empty($pessoa))
		{
			SessionManager::setIndex('message',"<p class=\"center\">Usuário inválido. Tente novamente.</p>");
			$this->result['header'] = "Location: ".Config::HOMEURL;
		}
		else
		{
			if ($pessoa['professor_pass'] == $pass)
			{
				$sql = "SELECT
							`p`.`id_pessoa` AS `id_pessoa`,
							`p`.`nome` AS `nome`,
							`p`.`sexo` AS `sexo`,
							`p`.`nascimento` AS `nascimento`,
							`p`.`id_estado_civil` AS `estado_civil`,
							`p`.`id_pais` AS `nacionalidade`,
							`p`.`cpf` AS `cpf`,
							`p`.`rg` AS `rg`,
							`p`.`rne` AS `rne`,
							`p`.`passaporte` AS `passaporte`,
							`p`.`email_principal` AS `email_principal`,
							`p`.`email_secundario` AS `email_secundario`,
							`p`.`id_local_nascimento` AS `id_local_nascimento`,
							`c`.`cidade` AS `municipio`,
							`pr`.`id_professor` AS `id_professor`,
							`pr`.`id_instituicao` AS `universidade`
							
						FROM
							`pessoas` `p`,
							`professores` `pr`,
							cidades c
							
						WHERE
							`p`.`id_pessoa` = '{$pessoa['id_pessoa']}' AND
							`c`.`id_cidade` = `p`.`id_local_nascimento` AND
							`p`.`id_pessoa` = `pr`.`id_pessoa` AND
							p.deletado = 0";

				$pessoa = $this->query($sql,true);
				
				SessionManager::setIndex('loggedin',true);
				SessionManager::setIndex('time',time());
				SessionManager::setIndex('user',$pessoa);
				
				$this->result['header'] = "Location: ".Config::HOMEURL."?q=".Config::SYSTEMHOME;
			}
			else
			{
				SessionManager::setIndex('message',"<p class=\"center\">Senha inválida. Tente novamente.</p>");
				$this->result['header'] = "Location: ".Config::HOMEURL;
			}
		}
		
		/*
		$con = ldap_connect(LDAPHOST, LDAPPORT);
		if (!$con){
			echo "Não foi possivel conectar no servidor.";
			die();
		}
		
		$bind = ldap_bind($con,$user, $pass);
		if (!$bind){
			echo "Erro na autentificação com servidor.";
			die();
		}
		*/
	}
	public static function getUserName($echo = false)
	{
		if ($_SESSION['user'] != '')
			if ($echo)
				echo $_SESSION['user']['nome'];
			else		
				return $_SESSION['user']['nome'];
		else
			return "ERROR";
	}
		
	public static function profileLink($id_pessoa, $echo = false)
	{
		if ($echo)
			echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($id_pessoa);
		else
			return Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($id_pessoa);
	}
	
	// Aqui eu pego os dados do professor logado no sistema
	public static function getUserInfo($professorID = '')
	{
		$qObject = new QueryObject();
		
		//Pegando todos os relatorios pendentes do usuario corrente
		$sql = 
		"SELECT
			`p`.`id_pessoa` AS `id_pessoa`,
			`p`.`nome` AS `nome`,
			IF(`p`.`sexo` = 1,'M','F') AS `sexo`,
			DATE_FORMAT(`p`.`nascimento`,'%d/%m/%Y') AS `nascimento`,
			`p`.`id_estado_civil` AS `estado_civil`,
			`pa`.`pais` AS `nacionalidade`,
			`p`.`cpf` AS `cpf`,
			`p`.`rg` AS `rg`,
			`p`.`rne` AS `rne`,
			`p`.`passaporte` AS `passaporte`,
			`p`.`email_principal` AS `email_principal`,
			`p`.`email_secundario` AS `email_secundario`,
			`p`.`id_local_nascimento` AS `id_local_nascimento`,
			`c`.`cidade` AS `municipio`,
			`pr`.`id_professor` AS `id_professor`,
			`i`.`instituicao` AS `universidade`
		FROM
			`pessoas` `p`,
			`professores` `pr`,
			`cidades` `c`,
			paises pa,
			v_instituicoes i
		WHERE
			`p`.`id_pessoa` = '{$professorID}' AND
			`c`.`id_cidade` = `p`.`id_local_nascimento` AND
			pa.id_pais = p.id_pais AND
			i.id = pr.id_instituicao AND
			`p`.`id_pessoa` = `pr`.`id_pessoa` AND
			p.deletado = 0
		LIMIT 1";
		
		$out = $qObject->query($sql,true,array());

		return $out;
	}
	
	public static function getEstadoCivil($estadoID)
	{
		$qObject = new QueryObject();
	
		//Pegando todos os relatorios pendentes do usuario corrente
		$sql = "SELECT estado_civil FROM estados_civis WHERE id_estado_civil = '{$estadoID}' LIMIT 1";
		$out = $qObject->query($sql,true);
		return $out;
	}
	
	
	
}
?>