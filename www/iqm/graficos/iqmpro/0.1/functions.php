<?php
/*function __autoload($classname)
{
	require(dirname(__FILE__).'/lib/'.strtolower($classname).'.php');
}*/
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**********************************************************************
 * Altere estes valores para configurar o sistema em outro computador *
 **********************************************************************/
/*
//host do banco de dados
define(DBHOST, "localhost");
//usuario do banco de dados
define(DBUSER, "penumbra_usrchem");
//senha do banco de dados
define(DBPASS, "IQM@Unicamp-2009");
//banco do banco de dados
define(DBDB,   "penumbra_dbchem");
//Página inicial do sistema
define(HOMEURL, "http://iqm.eccen.com.br/");
*
//host do banco de dados
define(DBHOST, "localhost");
//usuario do banco de dados
define(DBUSER, "root");
//senha do banco de dados
define(DBPASS, "vertrigo");
//banco do banco de dados
define(DBDB,   "iqm2010");
//Página inicial do sistema
define(HOMEURL, "http://localhost/iqmpro/iqmpro/");


/***********************************************************************
 * Definições para cada página do sistema, não precisa ser alterado    *
 ***********************************************************************/
/*
define(SYSTEMHOME,    "0xFgJnhhhaN93j8J2ffMn41OppP47");
define(RELATORIOS,    "65s4eFA6d4a654D6ca8f4E68AWWcd");
define(ORIENTADOS,    "eRFsefs0ef4SE5fs4e5fS4e5OP6eS");
define(ORIENTADORES,  "6gyj84gy6j86G5h4J6g5h4JT1bdt6");
define(DISCIPLINAS,   "nt6yjn84t6yJNt84jn60tj546Y8NT");
define(BOLSAS,        "cv9mCV0m8cv9bNVc0mV0bMvc8Bv08");
define(BANCAS,        "GL6g4lk6ggH4444H6l5Gh6h6jK5hk");
define(PROFILES,      "0w48cn0aEcANspFovb8U2n3f8cUCP");
define(DADOSPESSOAIS, "q9Vt4HnCVasFJvqn3e9846y9RB3Ff");
define(UMRELATORIO,   "fFKu618KKu6YFuk6f8uFkf2MBn6Hg");
define(PERFILDEBOLSA, "2sAAsdv24890NAy9pn43dyARhjvb2");
*/
// ============================================================================
// FUNCOES DE STRING
/*
function anti_injection($sql)
{
	return StringUtil::antiInjection($sql);
}

function data($out = false)
{
	StringUtil::getDate($out);
}
function cpfHumanizado($cpf)
{
	return StringUtil::cpfHumanizado($cpf);
}
function dataHumanizada($data, $mostraHora = false)
{
	return StringUtil::dataHumanizada($data,$mostraHora);
}
// END FUNCOES DE STRING ======================================================
// ============================================================================

function getUserName($echo = false)
{
	if ($_SESSION['user'] != '')
	{
		if ($echo)
			echo $_SESSION['user']['nome'];
		else		
			return $_SESSION['user']['nome'];
	}
	else
		return "ERROR";
}

function getUserInfo($professorID = '')
{
	if($professorID == '')
		$professorID = $_SESSION['user']['id_pessoa'];
		
	//Pegando todos os relatorios pendentes do usuario corrente
	$query = 
	"SELECT 
		`p`.`id_pessoa` AS `id_pessoa`, 
		`p`.`nome` AS `nome`, 
		`p`.`sexo` AS `sexo`, 
		`p`.`nascimento` AS `nascimento`, 
		`p`.`estado_civil` AS `estado_civil`, 
		`p`.`nacionalidade` AS `nacionalidade`, 
		`p`.`cpf` AS `cpf`, 
		`p`.`rg` AS `rg`, 
		`p`.`rne` AS `rne`, 
		`p`.`passaporte` AS `passaporte`, 
		`p`.`email_principal` AS `email_principal`, 
		`p`.`email_secundario` AS `email_secundario`, 
		`p`.`id_local_nascimento` AS `id_local_nascimento`, 
		`m`.`municipio` AS `municipio`, 
		`pr`.`id_professor` AS `id_professor`, 
		`pr`.`universidade` AS `universidade`, 
		`pr`.`unidade` AS `unidade`, 
		`pr`.`departamento` AS `departamento`, 
		`pr`.`externo` AS `externo` 
	FROM 
		`v_pessoas` `p`, 
		`v_professores` `pr`, 
		`municipios` `m` 
	WHERE 
		`m`.`id_municipio` = `p`.`id_local_nascimento` AND 
		`p`.`id_pessoa` = `pr`.`id_pessoa` AND 
		`p`.`id_pessoa` = '{$professorID}'";
	$res = mysql_query($query, $con);
	$out = array();
	//Colocando os dados em um array associativo
	while($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['nascimento'] = dataHumanizada($row['nascimento']);
		//$row['municipio'] = utf8_decode($row['municipio']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getEstadoCivil($estadoID)
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);

	//Pegando todos os relatorios pendentes do usuario corrente
	$res = mysql_query(
	"SELECT 
		estado_civil 
	FROM 
		estados_civis 
	WHERE 
		id_estado_civil = '{$estadoID}' 
	LIMIT 1"
	);
	$row = mysql_fetch_assoc($res);
	
	return $row['estado_civil'];
}

function getRelatorioPendente()
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);

	//Pegando todos os relatorios pendentes do usuario corrente
	$res = mysql_query("SELECT * FROM v_relatorios_pendentes WHERE id_professor = '{$_SESSION['user']['id_professor']}'");
	$out = array();
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['data_prevista'] = dataHumanizada($row['data_prevista']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getParecerPendente()
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando todos os pareceres pendentes do usuario corrente
	$res = mysql_query("SELECT * FROM v_pareceres_pendentes WHERE id_professor = '{$_SESSION['user']['id_professor']}'");
	$out = array();
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['data_entrega'] = dataHumanizada($row['data_entrega']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getRelatoriosEntregues()
{
	return getRelatorios('', "AND pgr.data_entrega != '0000-00-00 00:00:00' ORDER BY pgr.data_entrega DESC");
}

function getRelatoriosNaoEntregues()
{
	return getRelatorios('', "AND pgr.data_entrega = '0000-00-00 00:00:00'");
}

function getRelatorios($relatorioID = '', $restrictMore = '')
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando os relatórios do usuário corrente de acordo com os parametros recebidos
	$query = "SELECT pgr.*,p.id_pessoa,p.nome FROM `v_pg_relatorios` pgr, v_pessoas p, v_alunos val, v_pos_graduacoes pg WHERE pgr.id_pos_graduacao = pg.id_pos_graduacao AND pg.id_aluno = val.id_aluno AND p.id_pessoa = val.id_pessoa AND pgr.id_professor = '{$_SESSION['user']['id_professor']}' ";
	//Se foi pedido apenas 1 relatorio em especifico
	if ($relatorioID)
		$query .= "AND pgr.id_pg_relatorio = '$relatorioID' ";
	//Caso haja mais alguma restricao
	if ($restrictMore)
		$query .= $restrictMore;

	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		// Convertendo a data para uma forma mais humana
		$row['data_inicio'] = dataHumanizada($row['data_inicio']);
		$row['data_termino'] = dataHumanizada($row['data_termino']);
		$row['data_entrega'] = dataHumanizada($row['data_entrega']);
		$row['data_prevista'] = dataHumanizada($row['data_prevista']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getRelatorio($relatorioID, $restrictMore = '')
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando as informações sobre o relatório especificado 
	$query = "SELECT pgr.*,p.id_pessoa,p.nome FROM `v_pg_relatorios` pgr, v_pessoas p, v_alunos val, v_pos_graduacoes pg WHERE pgr.id_pos_graduacao = pg.id_pos_graduacao AND pg.id_aluno = val.id_aluno AND p.id_pessoa = val.id_pessoa AND pgr.id_pg_relatorio = '$relatorioID' ";
	//Caso haja mais alguma restricao
	if ($restrictMore)
		$query .= $restrictMore;

	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['data_inicio'] 	= dataHumanizada($row['data_inicio']);
		$row['data_termino'] 	= dataHumanizada($row['data_termino']);
		$row['data_entrega'] 	= dataHumanizada($row['data_entrega']);
		$row['data_prevista'] 	= dataHumanizada($row['data_prevista']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getRelatorioPendenteOrientador($professorID = '', $restrictMore = '')
{
	if ($professorID == '')
		$professorID = $_SESSION['user']['id_professor'];
		
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando todos os relatórios pendentes dos orientandos do professor corrente
	$query = "SELECT rp.* FROM v_relatorios_pendentes rp, v_rel_alunos_professores ap WHERE rp.id_pessoa = ap.id_pessoa AND ap.id_professor = '{$professorID}' ";
	$query .= $restrictMore;
	
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		$row['data_prevista'] = dataHumanizada($row['data_prevista']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getPareceresPendenteOrientador($professorID = '', $restrictMore = '')
{
	if ($professorID == '')
		$professorID = $_SESSION['user']['id_professor'];

	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando todos os pareceres pendentes dos orientandos do professor corrente
	$query = "SELECT pp.* FROM v_pareceres_pendentes pp, v_rel_alunos_professores ap WHERE pp.id_pessoa = ap.id_pessoa AND ap.id_professor = '{$professorID}' ";
	$query .= $restrictMore;
	
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		$row['data_entrega'] = dataHumanizada($row['data_entrega']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getAlunos($alunoID = '')
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando a pessoa requisitada, ou caso $alunoID não seja especificado, pega todo mundo
	$query = "select `p`.`id_pessoa` AS `id_pessoa`,`p`.`nome` AS `nome`,`p`.`sexo` AS `sexo`,`p`.`nascimento` AS `nascimento`,`p`.`estado_civil` AS `estado_civil`,`p`.`nacionalidade` AS `nacionalidade`,`p`.`cpf` AS `cpf`,`p`.`rg` AS `rg`,`p`.`rne` AS `rne`,`p`.`passaporte` AS `passaporte`,`p`.`email_principal` AS `email_principal`,`p`.`email_secundario` AS `email_secundario`,`p`.`id_local_nascimento` AS `id_local_nascimento`,`m`.`municipio` AS `municipio`,`a`.`ra` AS `ra`,`a`.`id_aluno` AS `id_aluno` from ((`v_alunos` `a` join `v_pessoas` `p`) join `municipios` `m`) where ((`a`.`id_pessoa` = `p`.`id_pessoa`) and (`p`.`id_local_nascimento` = `m`.`id_municipio`)) ";
	//Especificando apenas 1 pessoa
	if ($alunoID != '')
		$query .= "AND `p`.`id_pessoa` = '{$alunoID}' ";

	//ordenando
	$query .= "order by `p`.`id_pessoa`"; 
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['nascimento'] = dataHumanizada($row['nascimento']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getAlunosDoProfessor($restrictMore = '')
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando todos os orientandos do professor corrente 
	$query = "SELECT * FROM v_rel_alunos_professores WHERE id_professor = '{$_SESSION['user']['id_professor']}' ";
	$query .= $restrictMore;
	
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;

}

function getOrientadoresDoProfessor($restrictMore = '')
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando a pessoa requisitada, ou caso $alunoID não seja especificado, pega todo mundo 
	$query = "SELECT rap.*, p.id_pessoa AS id_professor_pessoa, p.nome AS nome_professor FROM v_rel_alunos_professores rap, v_pessoas p, v_professores vp WHERE rap.id_pessoa = '{$_SESSION['user']['id_pessoa']}' AND rap.id_professor = vp.id_professor AND vp.id_pessoa = p.id_pessoa";
	$query .= $restrictMore;
	
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;

}

function getDisciplinasProfessoresOrdenados($professorID = '')
{
	return getDisciplinasProfessores($professorID, "ORDER BY ano DESC, periodo ASC, turma ASC, codigo ASC");
}

function getDisciplinasProfessores($professorID = '', $restrictMore = '')
{
	if ($professorID == '')
		$professorID = $_SESSION['user']['id_professor'];
		
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando as disciplinas do professor corrente
	$query = "SELECT * FROM v_rel_disciplinas_professores WHERE id_professor = '{$professorID}'";
	$query = $query.$restrictMore;
	
	$res = mysql_query($query, $con) or die(mysql_error());
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		$row['disciplina'] = utf8_decode($row['disciplina']);
		$row['periodo'] = utf8_decode($row['periodo']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getAlunosDisciplina($id_disciplina, $periodo = '', $turma = '', $ano = '')
{
	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando os alunos da disciplina requisitada
	$query = "SELECT id_pessoa, nome, especial, conceito FROM v_rel_posgraduandos_disciplinas WHERE id_disciplina = '{$id_disciplina}' ";
	if ($periodo) $query .= "AND periodo = '{$periodo}' ";
	if ($turma) $query .= "AND turma = '{$turma}' ";
	if ($ano) $query .= "AND ano = '{$ano}' ";
	
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
	
}

function getBolsasProfessorSemParecer($professorID = '', $bolsaID = '')
{
	return getBolsasProfessor($professorID, $bolsaID, "AND b.processo = '' ");
}

function getBolsasProfessorComParecer($professorID = '', $bolsaID = '')
{
	return getBolsasProfessor($professorID, $bolsaID, "AND b.processo != '' ");
}

function getBolsasProfessor($professorID = '', $bolsaID = '', $restrictMore = '')
{
	if ($professorID == '')
		$professorID = $_SESSION['user']['id_professor'];

	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando as bolsas do professor corrente
	$query = "SELECT b.id_bolsa, b.nome_agencia, b.data_inicio, b.data_fim, b.processo, b.observacao, p.id_pessoa, p.nome FROM v_bolsas b, v_pessoas p, v_alunos val, v_pos_graduacoes vpg WHERE vpg.id_pos_graduacao = b.id_posgraduacao AND vpg.id_aluno = val.id_aluno AND val.id_pessoa = p.id_pessoa AND b.id_professor = '{$professorID}' ";
	if ($bolsaID != '') $query .= "AND b.id_bolsa = '{$bolsaID}' ";
	$query .= $restrictMore;

	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['data_inicio'] = dataHumanizada($row['data_inicio']);
		$row['data_fim'] = dataHumanizada($row['data_fim']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function getBancasProfessor($professorID = '')
{
	if ($professorID == '')
		$professorID = $_SESSION['user']['id_professor'];

	$con = mysql_connect(Config::DBHOST, Config::DBUSER, Config::DBPASS);
	mysql_select_db(Config::DBDB, $con);
	
	//Pegando as bancas do professor corrente
	$query = "SELECT b.*,atr.atribuicao as atribuicao_professor, vp.nome as nome_pessoa, vp.id_pessoa as id_pessoa FROM v_bancas b, v_pos_graduacoes vpg, v_alunos val, v_pessoas vp, atribuicoes atr WHERE (b.id_professor = '{$professorID}' and vpg.id_pos_graduacao = b.id_posgraduacao and val.id_aluno = vpg.id_aluno and vp.id_pessoa = val.id_pessoa and b.id_atribuicao = atr.id_atribuicao) ORDER BY `data` DESC, nome_pessoa ASC";
	
	$res = mysql_query($query, $con);
	$out = array();
	
	//Colocando os dados em um array associativo
	while ($row = mysql_fetch_assoc($res))
	{
		//Convertendo a data para uma forma mais humana
		$row['data'] = dataHumanizada($row['data']);
		$out[] = $row;
	}
	
	mysql_close($con);
	return $out;
}

function ano($data)
{
	return StringUtil::ano($data);
}

function ordenaBancas($bancas)
{
	$out = array();
	$prevAno = ano($bancas[0]['data']);
	
	//percorre o vetor de bancas agrupando as bancas de mesmo ano
	foreach($bancas as $banca)
	{
		if ($prevAno != ano($banca['data']))
		{
			$out[] = array('bancas' => $aux, 'ano' => $prevAno);
			$prevAno = ano($banca['data']);
			$aux = array();
		}
		$aux[] = $banca;
	}
	
	if (!empty($aux))
		$out[] = array('bancas' => $aux, 'ano' => $prevAno);
	
	return $out;
}

function profileLink($id_pessoa, $echo = false)
{
	if ($echo)
		echo HOMEURL."?q=".PROFILES."&amp;u=".base64_encode($id_pessoa);
	else
		return HOMEURL."?q=".PROFILES."&amp;u=".base64_encode($id_pessoa);
}
*/