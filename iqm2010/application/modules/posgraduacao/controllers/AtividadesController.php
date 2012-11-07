<?php
require_once(APPLICATION_PATH.'/models/Atividades.php');
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH.'/models/Pessoas.php');

class Posgraduacao_AtividadesController extends Zend_Controller_Action
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
    	$idTipoCurso	= $this->_request->getParam("tipo");
		$this->view->tipoPosGraduacao	=	$idTipoCurso;
		$idPessoa 		= $this->_request->getParam("id_pessoa");
		
    	$general = new Models_General();
    	
    	$pChecker = new ParamChecker();
		$tipoCurso = $pChecker->getParam(array(
				'rel_table' => 'tipo_cursos',
				'need'		=> 'slug',
				'value'		=> $idTipoCurso,
				'where'		=> "id_tipo_curso = '$idTipoCurso'"
			));
			
		$this->view->tipocurso=strtolower($tipoCurso);
		
		
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
//        if($id_pos_graduacao!='')$this->_redirect('/posgraduacao/atividades/index/id_pos_graduacao/'.$id_pos_graduacao.'/tipo/'.$idTipoCurso.'/id_pessoa/'.$idPessoa);
        /**/
    	$this->view->professor = $general->listView('v_professores', array('id', 'nome'));

   		$this->view->atividade = $general->listView('v_tipo_atividades', array('id', 'tipo_atividade'));

   		$this->view->disciplinas = $general->listView('v_disciplinas_atividades', array('id', 'nome'));
    	
//   		var_dump($general->listView('v_disciplinas_atividades', array('id', 'nome')));
//   		exit;
    	
   		$data = new Application_Models_Atividades();
    	
    	// UPDATE
    	

    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
    	      'id_atividade' => $this->_request->getParam("id"),
    		  'id_tipo_atividade' => $this->_request->getParam("atividadeU"),
              'inicio'=>$this->toMysqlDateFull($_REQUEST['datainicio']),
  			  'termino'=>$this->toMysqlDateFull($_REQUEST['datafim']),
  			  'observacoes'=> $this->_request->getParam("observacoesU"),
  			  'cancelado' =>$this->_request->getParam("cancelada")==1?1:0,
  			  'id_professor'  => $this->_request->getParam("professorU"),   
    		  'id_disciplina'  => $this->_request->getParam("disciplinasU"),  
    		  'data_cancelamento' =>$this->toMysqlDateFull($_REQUEST['data_cancelamento']), 
    		  'deletado' => '0',
            
             //modificar este valor
           	 'id_pos_graduacao'  => $this->_request->getParam("id_pos_graduacao"),
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		//$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
	        $params = array(
            
              'id_tipo_atividade' => $this->_request->getParam("atividade"),
              'inicio'=> $this->toMysqlDateFull($this->_request->getParam("datainicio")),
  			  'termino'=> $this->toMysqlDateFull($this->_request->getParam("datafim")),
  			  'observacoes'=> $this->_request->getParam("observacoes"),
  			  'cancelado' => (int)$this->_request->getParam("cancelada"),
  			  'id_professor'=> $this->_request->getParam("professor"), 
	       	  'id_disciplina'=> $this->_request->getParam("disciplinas"),
	          'data_cancelamento' =>$this->toMysqlDateFull($this->_request->getParam("data_cancelamento")),            
              'deletado' => '0',
            
            //modificar este valor
            'id_pos_graduacao'  => $this->_request->getParam("id_pos_graduacao"),
 			  
 	     );
         //print_r($params);
 	     $data->CRUDcreate($params);
 	     
 	     $idTipoCurso	= $this->_request->getParam("tipo");
 	     $this->_redirect('/posgraduacao/atividades/index/id_pos_graduacao/'.$this->_request->getParam("id_pos_graduacao").'/tipo/'.$idTipoCurso);
       }
       
    	
        // READ
        
       $this->view->data = $data->CRUDread( $this->_request->getParam("id_pos_graduacao"));       
    }
    function deleteAction()
    {

    	$data = new Application_Models_Atividades();
    	$data->CRUDdelete($this->_request->getParam("id"));
    	$this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
 	     $idTipoCurso	= $this->_request->getParam("tipo");
 	     $this->_redirect('/posgraduacao/atividades/index/id_pos_graduacao/'.$this->_request->getParam("id_pos_graduacao").'/tipo/'.$idTipoCurso);
    }
    public function toMysqlDate($string)
    {
        $a = explode('/', $string);
        $b = $a[1].'-'.$a[0].'-01 : 00:00:00';
        return $b;
    }
    public function toMysqlDateFull($string)
    {
        $a = explode('/', $string);
        $b = $a[2].'-'.$a[1].'-'.$a[0].' 00:00:00';
        return $b;
    }     
}