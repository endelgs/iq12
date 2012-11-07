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
	public function recuperaSenha()
	{
		$qObject = new QueryObject();
		$this->params['login'] = trim($this->params['login']);
		$sql = "
			SELECT 
				pr.* 
			FROM 
				pessoas p, 
				professores pr 
			WHERE 
				p.id_pessoa = pr.id_pessoa AND 
				p.deletado = 0 AND 
				pr.deletado = 0 AND
				p.email_principal = '{$this->params['login']}'";
		
		$qObject->query($sql,false);
		$header = "Location: ".Config::HOMEURL;
		$numRows = $qObject->conexao->registrosAfetados();
		if($numRows == 0 || empty($this->params['login']))
		{
			$message = 'Não existe usuário cadastrado com esse e-mail no sistema.';
			$header .= 'esqueci.php';			
		}
		elseif($numRows == 1)
		{
			$random = substr(str_shuffle('abcçdefghijklmnopqrstuvwxyzABCÇDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),0,6);
			$sql = 
				"UPDATE 
					professor_credencial 
				SET 
					professor_pass = '".md5($random)."' 
				WHERE 
					professor_login = '{$this->params['login']}'";
			$qObject->query($sql,false);
			$message = 'Sua nova senha foi enviada para '.$this->params['login'];
		}
		else
		{
			$message = 'Este e-mail possivelmente está duplicado na base de dados. Contate o administrador do sistema';
			$header .= 'esqueci.php';
		}
			
		
		// Enviando o e-mail
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'To: '.$_SESSION['user']['nome'].'<'.$_SESSION['user']['email_principal'].'>' . "\r\n";
		$headers .= 'From: UNICAMP - Instituto de Química <naoresponda@iq.unicamp.br>' . "\r\n";
		
		
		$mail = "
			<p><strong>UNICAMP - Instituto de Química</strong></p>
			<p>Você solicitou redefinição de senha no sistema para professores da CPG</p>
			<p>Sua nova senha: $random </p>
		";
		
		mail($_SESSION['user']['nome'].'<'.$_SESSION['user']['email_principal'].'>','Recuperação de Senha',$mail,$headers);
		SessionManager::setIndex('message',$message);
		$this->result['header'] = $header;
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
			`i`.`instituicao` AS `universidade`,
			prd.unidade,
			d.departamento
		FROM
			`pessoas` `p`,
			`professores` `pr`,
			`cidades` `c`,
			paises pa,
			v_instituicoes i,
			professor_detalhes prd,
			departamento d
		WHERE
			`p`.`id_pessoa` = '{$professorID}' AND
			`c`.`id_cidade` = `p`.`id_local_nascimento` AND
			pr.id_professor = prd.id_professor AND
			pa.id_pais = p.id_pais AND
			i.id = prd.id_instituicao AND
			d.id_departamento = prd.id_departamento AND
			`p`.`id_pessoa` = `pr`.`id_pessoa` AND
			p.deletado = 0
		LIMIT 1";

		return $qObject->query($sql,true);
	}

	public static function getEstadoCivil($estadoID)
	{
		$qObject = new QueryObject();
	
		//Pegando todos os relatorios pendentes do usuario corrente
		$sql = "SELECT estado_civil FROM estados_civis WHERE id_estado_civil = '{$estadoID}' LIMIT 1";
		$out = $qObject->query($sql,true);
		return $out;
	}
	public function salvaFaltas()
	{
		if(is_array($this->params['id']))
		{
			$qObject = new QueryObject();
			$qBuilder = new SQL_QueryBuilder();

			$sql = "INSERT INTO frequencia (id_pos_graduacao,mes,nao_ok) VALUES ";
			
			$counter = 0;

			foreach($this->params['id'] as $id)
			{
				if($counter == 0)
					$counter++;
				else
					$sql .= ',';
					
				$naoOK = (isset($this->params['aluno'][$id]));
				$data = date('Y').'-'.str_pad(date('m')-1,'2','0',STR_PAD_LEFT).'-00';
				$qObject->query("DELETE FROM frequencia WHERE id_pos_graduacao = '$id' AND mes = '$data'");
				$sql .= "('$id','$data','$naoOK')";
			}
			
			$qObject->query($sql,false);
		}

		$this->result['header'] = "Location: ".Config::HOMEURL."?q=".Config::FREQUENCIA;
	}
}