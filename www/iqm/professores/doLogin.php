<?php
error_reporting(0);
define(ABSPATH, dirname(__FILE__)."/");
include(ABSPATH."functions.php");
define(LDAPHOST, "127.0.0.1");
define(LDAPPORT, "8080");

session_start();
if(empty($_POST))
	die();

//$invalid = array("'", "\"", "<", ">", "?");	

$user = StringUtil::antiInjection($_POST['txtLogin']);
$pass = md5(StringUtil::antiInjection($_POST['txtPass']));

$con = mysql_connect(DBHOST, DBUSER, DBPASS);
mysql_select_db(DBDB, $con);
$sql = "SELECT 
			`id_pessoa`, 
			`professor_pass` 
		FROM 
			`v_professor_credencial` 
		WHERE 
			`professor_login` = '{$user}' 
		LIMIT 1";
$res = mysql_query($sql, $con);
$pessoa = mysql_fetch_assoc($res);
/*
if (empty($pessoa)){
	$_SESSION['message'] = "<p class=\"center\">Usuário inválido. Tente novamente.</p>";
	header("location: ".HOMEURL);
}else{
*/
{
	
	//if ($pessoa['professor_pass'] == $pass)
	{
		$sql = "SELECT 
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
					`m`.`id_municipio` = `p`.`id_local_nascimento` and 
					`p`.`id_pessoa` = `pr`.`id_pessoa` and 
					`p`.`id_pessoa` = '{$pessoa['id_pessoa']}'";
		//$res = mysql_query($sql, $con) or die(mysql_error());
		//$pessoa = mysql_fetch_assoc($res);
		
		$pessoa = array(
			'id_pessoa' => 1,
			'nome'		=> 'José Teste',
			'sexo'		=> 'M',
			'nascimento'=> '1990-04-21'			
		);
		$_SESSION['loggedin'] = true;
		$_SESSION['time'] = time();
		$_SESSION['user']= $pessoa;
		
		mysql_close($con);
		header("location: ".HOMEURL."?q=".SYSTEMHOME);
	}
//	else
//	{
//		$_SESSION['message'] = "<p class=\"center\">Senha inválida. Tente novamente.</p>";
//		header("location: ".HOMEURL);
//	}
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