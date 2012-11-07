<?php
require_once(APPLICATION_PATH.'/models/CidadesDaProva.php');
require_once(APPLICATION_PATH.'/models/General.php');

class Gerenciar_CidadesDaProvaController extends Zend_Controller_Action
{

    public function init()
    {
    	$general = new Models_General();
		$this->view->cidades = $general->listView('v_cidades_uf', array('id', 'cidade_uf'));
    }

    public function indexAction()
    {

    	$data = new Application_Models_CidadesDaProva();
    	// UPDATE
    	
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) 
    	{
    		
    	}
    	else if($this->getRequest()->isPost())
    	{
    	    $params = array(
              'id_cidade' => $this->_request->getParam("data"),
            );
            
            $data->CRUDcreate($params);
            $this->_redirect('/gerenciar/cidades-da-prova/');
    	}
        // READ
        
    	$this->view->data = $data->CRUDread();
    	
        
    }
    function deleteAction()
    {
    	


    	$data = new Application_Models_CidadesDaProva();
    	$data->CRUDdelete($this->_request->getParam("id"));
    }
}