<?php
// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/Docente.php');
require_once(APPLICATION_PATH.'/models/DetalhesDocente.php');
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');

class Docente_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $p = new Models_General();
    	$clsPessoa = new Application_Models_Pessoas();
		if($this->_request->getParam("id_pessoa")!="")
		{
			$result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
			$this->view->nomePessoa = $result['nome'];
		}
    }
	
    public function indexAction()
    {
    	
    	$pos_graduacoes = new Application_Models_Pos_Graduacoes();
    	if($this->_request->getParam("id_pos_graduacao")!="")
		{
			$resultPos = $pos_graduacoes->getPessoaByPosSemCurso($this->_request->getParam("id_pos_graduacao"));
			$id_pessoa = '';
			foreach($resultPos as $rowResult)
        	{
        		$id_pessoa = $rowResult['id_pessoa'];
        	}
        }
	if($this->_request->getParam("id_pessoa")!="")
		$id_pessoa=$this->_request->getParam("id_pessoa");
		
        if(!((int)$id_pessoa)>0)
 //       	$this->_redirect('/docente/index/index/id_pessoa/'.$id_pessoa);
  //      else 
        	$this->_redirect('/pessoas/geral/');


       
        $idPessoa=$this->_request->getParam("id_pessoa");
        
        $idTipoCurso	= $this->_request->getParam("tipo");
		$this->view->tipoPosGraduacao	=	$idTipoCurso;
		
		$idPessoa 		= $this->_request->getParam("id_pessoa");
		
	   /*TROCA `id_pessoa` POR `id_pos_graduacao`*/
//        if($this->_request->getParam("id_pessoa")!="")
//		{
//			$id_pessoa = $this->_request->getParam("id_pessoa");
//			$pos_graduacoes = new Application_Models_Pos_Graduacoes();
//			$resultPos = $pos_graduacoes->getPosByPessoa($this->_request->getParam("id_pessoa"),$idTipoCurso);
//			foreach($resultPos as $rowResult)
//        	{
//        		$id_pos_graduacao = $rowResult['id_pos_graduacao'];
//        	}
//        }
//		if($id_pos_graduacao!='')$this->_redirect('/posgraduacao/atividades/index/id_pos_graduacao/'.$id_pos_graduacao.'/id_pessoa/'.$idPessoa);

        //......................................................//
        $detalhes = new Application_Models_DetalhesDocente();
        $dados=$detalhes->CRUDread($id_pessoa);
		$this->view->dados=$dados[0];
        
        $general = new Models_General();
		$this->view->instituicoes 	= $general->listView('v_instituicoes', array('id', 'instituicao'));
    	$this->view->departamentos 	= $general->listView('v_departamentos', array('id', 'departamento'));
		$this->view->tiposDocente 	= $general->listView('v_tipo_docente', array('id', 'tipo_docente'));
		$this->view->tiposAposendadorias 	= $general->listView('v_tipo_aposentadoria', array('id', 'aposentadoria'));  
		

	
	//testar senao retornar nadinha
		
		
		if ($this->getRequest()->isPost())
    	{
    		$docente = new Application_Models_Docente();
    		if($this->_request->getParam("ehDocente") == 1)
    		{
    			$params = array
    			(
    			 'id_pessoa' => $this->_request->getParam("id_pessoa"),
    			 'id_instituicao' => $this->_request->getParam("instituicao"),
    			);
    			
    			$idprofessor=$docente->addDocente($params);

    			$this->view->alert = 'Dados gravados com sucesso.';
    			
    			$params= array(
    			'id_professor' => $idprofessor,
    			'id_instituicao' => $this->_request->getParam("instituicao"),
    			'id_departamento' => $this->_request->getParam("departamento"),
    			'titulacao' => $this->_request->getParam("titulacao"),
    			'unidade' => $this->_request->getParam("unidade"),
    			'titulo' => $this->_request->getParam("titulo"),
    			'data' => $this->_request->getParam("data"),
    			'observacoes' => $this->_request->getParam("observacoes"),
    			'aposentado' => $this->_request->getParam("aposentado"),
    			'dataaposentadoria' => $this->_request->getParam("dataaposentadoria"),
    			'id_aposentadoria' => $this->_request->getParam("tipoaposentadoria"),
    			'status' => $this->_request->getParam("ativo"),
    			'id_tipodocente'=>$this->_request->getParam("tipodocente"),
    			);
    			
    			$detalhes->CRUDcreate($params, $idprofessor);
    			$this->_redirect('/docente/index/index/id_pessoa/'.$idPessoa);
    		}
    		else
    		{
    			$docente->CRUDdelete($this->_request->getParam("id_pessoa"));
    			//$this->view->alert = 'Dados gravados com sucesso.';
    			$this->_redirect('/docente/index/index/id_pessoa/'.$idPessoa);
    		}
    	}
        if($this->_request->getParam("id_pessoa")!="")
        {
            $this->view->idPessoa = $this->_request->getParam("id_pessoa");
            $docente = new Application_Models_Docente();
            $ehDocente = $docente->getByIdPessoa($this->_request->getParam("id_pessoa"));
            $this->view->check = (int)$ehDocente['id_professor'] != 0; 
          //  $this->_redirect('/docente/index/index/id_pessoa/'.$idPessoa.'/tipo/6');
        }
    	
    }
}