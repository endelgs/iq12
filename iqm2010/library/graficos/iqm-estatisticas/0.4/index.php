<?php
// Função 'mágica' que carrega automaticamente as classes necessárias
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
/*
 * Esse eh o centralizador de requests do programa. 
 * TODOS os dados passam por esse arquivo.
 */

/*
 * Os erros estão desabilitados pra que, qualquer erro/alerta possa ser visto
 * de dentro do Flex e tratado posteriormente
 */
//error_reporting(E_ALL);
error_reporting(0);

$ioManager = new IOManager();
//$_REQUEST['action'] = 'adpa';

if(	isset($_REQUEST['4a9cef46394effc1a5922d63a7bae5a4']) && 
	$_REQUEST['4a9cef46394effc1a5922d63a7bae5a4'] == '57088fb4a102f279fbbdeefba13813e848e4b209')
{
	$ioManager->setAction($_REQUEST['action']);
	$ioManager->return = true;
	
	echo $ioManager->run();
}
//else
//	header("HTTP/1.0 404 Not Found");
//echo md5('142536::br.unicamp.iq@eccen');
//echo sha1('142536::br.unicamp.iq@eccen');