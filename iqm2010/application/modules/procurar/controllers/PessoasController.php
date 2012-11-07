<?php
require_once(APPLICATION_PATH.'/models/Procurar.php');

class Procurar_PessoasController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
    	
        if ($this->getRequest()->isPost()) {
        	$pessoa=  $_REQUEST['query'];
        	
        	$pessoa=str_replace('"','\"',$pessoa);
        	$pessoa=str_replace("'","\'",$pessoa);
        	
        	$procura = new Models_Procurar();
        	$this->view->res = $procura->buscaPessoa((int)$_REQUEST['tipo'], $pessoa);
        }
    }
}