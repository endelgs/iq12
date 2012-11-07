<?php
define('ERR_404','http://localhost/iqmpro/iqmpro/system-404.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
if(!function_exists('__autoload'))
{
	function __autoload($classname)
	{
		$classname = str_replace('_','/',$classname);
		$file = strtolower($classname).'.php';
		$path = dirname(__FILE__).'/lib/'.$file;
		if(file_exists($path))
			include_once($path);
	}
}

if(isset($_POST['entity']) && isset($_POST['action']))
{
	$class 	= $_POST['entity'];
	$method = $_POST['action'];
	$obj = new $class($_POST['params']);
	$obj->$method();

	$result = $obj->getResult();

	// Aqui eu padronizo a saida
	if(isset($result['header']))
		header($result['header']);
}
else
	header('Location: '.ERR_404);