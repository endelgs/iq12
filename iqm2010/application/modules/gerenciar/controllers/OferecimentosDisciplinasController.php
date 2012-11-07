<?php
require_once(APPLICATION_PATH.'/models/OferecimentosDisciplinas.php');
class Gerenciar_OferecimentosDisciplinasController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$data = new Application_Models_OferecimentosDisciplinas();
    	
    	// UPDATE
    	
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
    	      'oferecimento_disciplina'    => $this->_request->getParam("dado"),
    		  'id_oferecimento_disciplina' => $this->_request->getParam("id")
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
    		// CREATE
            $params = array(
              'oferecimento_disciplina'   => $this->_request->getParam("data"),
              'deletado' => '0'
            );
            
            $data->CRUDcreate($params);
    	}
    	
        // READ
        $this->view->data = $data->CRUDread();
    }
    function deleteAction()
    {
 
    	$data = new Application_Models_OferecimentosDisciplinas();
    	$data->CRUDdelete($this->_request->getParam("id"));
    }
}