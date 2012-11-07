<?php
require_once(APPLICATION_PATH.'/models/MotivosDeCancelamento.php');

class Gerenciar_MotivosDeCancelamentoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	// Instancio o Model
    	$data = new Application_Models_MotivosDeCancelamento();

    	// READ
    	$this->view->data = $data->CRUDread();
    }
    function postAction()
    {
    	$data = new Application_Models_MotivosDeCancelamento();
    	// Verifico os parametros.
    	// Caso haja um id, faco update, senao, faco insert
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
    	      'motivo_cancelamento'    => $this->_request->getParam("dado"),
    		  'id_motivo_cancelamento' => $this->_request->getParam("id")
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		$this->view->alert = 'Dado alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
            $params = array(
    	      'motivo_cancelamento'    => $this->_request->getParam("dado"),
    		  'deletado' => '0'
    		);
            
            $data->CRUDcreate($params);
            $this->view->alert = 'Dado inseridos com sucesso';
    	}
    	
    }
    function deleteAction()
    {
    	
    	$data = new Application_Models_MotivosDeCancelamento();
    	$data->CRUDdelete($this->_request->getParam("id"));
    	$this->view->alert = 'Dado excluido com sucesso';
    }
	function updateAction()
    {
    	
    	$data = new Application_Models_MotivosDeCancelamento();
    	$params = array('motivo_cancelamento' => $_REQUEST['dado']);
    	$data->CRUDupdate($params,$this->_request->getParam('id'));
    	$this->view->alert = 'Dados atualizados com sucesso';
    }
    function createAction()
    {
    	
    	$data = new Application_Models_MotivosDeCancelamento();
    	$data->CRUDcreate(array('motivo_cancelamento' => $this->_request->getParam("motivo_cancelamento")));
    	$this->view->alert = 'Dados cadastrados com sucesso';
    }
}