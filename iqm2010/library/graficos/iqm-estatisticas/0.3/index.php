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
 * Esse é o centralizador de requests do programa. 
 * TODOS os dados passam por esse arquivo.
 */

/*
 * Os erros estão desabilitados pra que, qualquer erro/alerta possa ser visto
 * de dentro do Flex e tratado posteriormente
 */
error_reporting(E_ALL);

$ioManager = new IOManager();
//$_REQUEST['action'] = 'alunosPorAno';
if(	isset($_REQUEST['4a9cef46394effc1a5922d63a7bae5a4']) && 
	$_REQUEST['4a9cef46394effc1a5922d63a7bae5a4'] == '57088fb4a102f279fbbdeefba13813e848e4b209')
{
	$ioManager->setAction($_REQUEST['action']);
	$ioManager->setParams((isset($_REQUEST['params']))?$_REQUEST['params']:array());
	$ioManager->return = true;
	
	echo $ioManager->run();
}
//echo md5('142536::br.unicamp.iq@eccen');
//echo sha1('142536::br.unicamp.iq@eccen');