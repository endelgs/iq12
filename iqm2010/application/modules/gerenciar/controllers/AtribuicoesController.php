<?php
require_once(APPLICATION_PATH.'/models/Atribuicoes.php');
class Gerenciar_AtribuicoesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$data = new Application_Models_Atribuicoes();
    	
    	// UPDATE
    	
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
    	      'atribuicao'    => $this->_request->getParam("dado"),
    		  'id_atribuicao' => $this->_request->getParam("id")
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
    		// CREATE
            $params = array(
              'atribuicao'=> $this->_request->getParam("data"),
              'deletado' => '0'
            );
            
            $data->CRUDcreate($params);
    	}
    	
        // READ
        $this->view->data = $data->CRUDread();
    }
    function deleteAction()
    {

    	$data = new Application_Models_Atribuicoes();
    	$data->CRUDdelete($this->_request->getParam("id"));
    }
}