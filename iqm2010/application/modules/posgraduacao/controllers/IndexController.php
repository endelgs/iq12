<?php
// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/Pessoas.php');

class Posgraduacao_IndexController extends Zend_Controller_Action
{

    public function init(){
      
      $p = new Models_General();
      $clsPessoa = new Application_Models_Pessoas();
  		
      if($this->_request->getParam("id_pessoa")!=""){
        $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
        $this->view->nomePessoa = $result['nome'];
      }
    }
	
    public function indexAction(){
      echo 'index';
    }    
    
    
}