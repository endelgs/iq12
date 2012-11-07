<?php
require_once(APPLICATION_PATH.'/models/Cursos.php');
class Gerenciar_CursosController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$data = new Application_Models_Cursos();
    	
    	// UPDATE
    	
    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
    	      'curso'    => $this->_request->getParam("dado"),
    		  'id_curso' => $this->_request->getParam("id")
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
    		// CREATE
            $params = array(
              'curso'    => $this->_request->getParam("data"),
              'deletado' => '0'
            );
            
            $data->CRUDcreate($params);
    	}
    	
        // READ
        $this->view->data = $data->CRUDread();
    }
    function deleteAction()
    {
    
    	$data = new Application_Models_Cursos();
    	$data->CRUDdelete($this->_request->getParam("id"));
    }
}