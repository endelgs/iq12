<?php
require_once(APPLICATION_PATH.'/models/AreasDeConcentracao.php');
require_once(APPLICATION_PATH.'/models/inscricoes.php');
require_once(APPLICATION_PATH.'/models/Orientadores.php');

class Gerenciar_AreasDeConcentracaoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$data = new Application_Models_AreasDeConcentracao();
    	
    	// UPDATE
    	
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
    	      'area_de_concentracao'    => $this->_request->getParam("dado"),
    		  'id_area_de_concentracao' => $this->_request->getParam("id")
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		$this->view->alert = 'Dado alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
            $params = array(
              'area_de_concentracao'    => $this->_request->getParam("data"),
              'deletado' => '0'
            );
            
            $data->CRUDcreate($params);
            $this->view->alert = 'Dado inseridos com sucesso';
    	}
    	
        // READ
       $this->view->data =$data->CRUDread();
    }
    function deleteAction()
    {

    	$data = new Application_Models_AreasDeConcentracao();
    	$data->CRUDdelete($this->_request->getParam("id"));
    	$this->view->alert = 'Dado excluido com sucesso';
    }
}