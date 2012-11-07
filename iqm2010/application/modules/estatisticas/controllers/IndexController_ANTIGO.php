<?php
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/Docente.php');
require_once(APPLICATION_PATH.'/models/DetalhesDocente.php');
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');

class  Estatisticas_IndexController extends Zend_Controller_Action
{

    public function init()
    {

    }
	
    public function indexAction()
    {
    	$this->view->action = $this->_request->getParam("acao");

    }
}