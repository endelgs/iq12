<?php
require_once(APPLICATION_PATH.'/models/TiposDeTrancamento.php');
class Gerenciar_TiposDeTrancamentoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$data = new Application_Models_TiposDeTrancamento();
    	
    	// UPDATE
    	
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		//echo 'UPDATE';
    		$params = array(
    	      'tipo_trancamento'    => $this->_request->getParam("dado"),
    		  'id_tipo_trancamento' => $this->_request->getParam("id"),
    		  'soma_integralizacao' =>$this->_request->getParam("ckconta")==1?1:0
    		);
    		

    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
    		//echo 'CREATE';
    		// CREATE
            $params = array(
              'tipo_trancamento'    => $this->_request->getParam("data"),
              'soma_integralizacao' => ($this->_request->getParam("conta")==1?1:0),
              'deletado' => '0'
            );
            
            $data->CRUDcreate($params);
    	}
    	
        // READ
        $this->view->data = $data->CRUDread();
    }
    function deleteAction()
    {
   
    	$data = new Application_Models_TiposDeTrancamento();
    	$data->CRUDdelete($this->_request->getParam("id"));
    }
}