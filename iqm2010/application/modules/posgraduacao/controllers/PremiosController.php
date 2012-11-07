<?php
// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/Premios.php'); 
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH.'/models/Pessoas.php');

class Posgraduacao_PremiosController extends Zend_Controller_Action
{

	public function init()
	{
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
		//--------
		$idTipoCurso	= $this->_request->getParam("tipo");
		$this->view->tipoPosGraduacao	=	$idTipoCurso;
		$idPessoa 		= $this->_request->getParam("id_pessoa");
		$idPosGraduacao  = $this->_request->getParam("id_pos_graduacao");
    	$general = new Models_General();
    	
    	$pChecker = new ParamChecker();
		$tipoCurso = $pChecker->getParam(array(
				'rel_table' => 'tipo_cursos',
				'need'		=> 'slug',
				'value'		=> $idTipoCurso,
				'where'		=> "id_tipo_curso = '$idTipoCurso'"
			));
		
		$tipoPessoa=0;

		if($idTipoCurso=="6")
		{
			$iddocente = $pChecker->getParam(array(
				'rel_table' => 'professores',
				'need'		=> 'id_professor',
				'value'		=> $idTipoCurso,
				'where'		=> "id_pessoa = '$idPessoa'"
			));

			$idPosGraduacao=$iddocente;
			$tipoPessoa=1;
		}
		
		$this->view->tipocurso=strtolower($tipoCurso);
		//-------------
		
		
		$data= new Application_Models_Premios();
		$arrPremios=$data->CRUDread($idPosGraduacao,$tipoPessoa);
		
		
		if ($this->getRequest()->isPost() && $this->_request->getParam("id_premio") != 0) 
		{
    	    $params = array(
              'premio'=> $this->_request->getParam("premio"),
    	      'descricao'=> $this->_request->getParam("descricao"),
              'data'=> $this->_request->getParam("data"),
	       );
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id_premio"));
    		$this->_redirect('/posgraduacao/premios/index/id_pessoa/'.$idPessoa.'/id_pos_graduacao/'.$idPosGraduacao.'/tipo/'.$idTipoCurso);
    	}
		else if($this->getRequest()->isPost())
		{
				print_r($tipoPessoa);
			
		        $params = array(
	              'premio'=>$this->_request->getParam("premio"),
	    	      'descricao'=> $this->_request->getParam("descricao"),
	              'data'=> $this->_request->getParam("data"),
		          'id_pos_graduacao'=> $idPosGraduacao,
		          'tipo'=>$tipoPessoa
	 	     	);
	 	     $data->CRUDcreate($params);
	 	     
	 		 $idTipoCurso	= $this->_request->getParam("tipo");
	 		 
	 		 $idTipoCurso	= $this->_request->getParam("tipo");
 	         $this->_redirect('/posgraduacao/premios/index/id_pessoa/'.$idPessoa.'/id_pos_graduacao/'.$idPosGraduacao.'/tipo/'.$idTipoCurso);
 	   }

		$clsPremios= new Application_Models_Premios();
		$this->view->grid=$arrPremios;
	}
	
    function deleteAction()
    {	
  		$pChecker = new ParamChecker();
  		$idPosGraduacao= $this->_request->getParam("id_pos_graduacao");
  		$idPessoa 		= $this->_request->getParam("id_pessoa");
  		$idTipoCurso=$this->_request->getParam("tipo");
  		
    	if($this->_request->getParam("tipo")=="6")
		{
			$iddocente = $pChecker->getParam(array(
				'rel_table' => 'professores',
				'need'		=> 'id_professor',
				'value'		=> $this->_request->getParam("tipo"),
				'where'		=> "id_pessoa = '$idPessoa'"
			));

			$idPosGraduacao=$iddocente;
		}
		
    	$this->view->idPosGraduacao = $idPosGraduacao;
		$this->view->idTipoCurso=$this->_request->getParam("tipo");
    
    	$data = new Application_Models_Premios();
    	$data->CRUDdelete($this->_request->getParam("id"));
    	$this->_redirect('/posgraduacao/premios/index/id_pessoa/'.$idPessoa.'/id_pos_graduacao/'.$idPosGraduacao.'/tipo/'.$idTipoCurso);
    }
}