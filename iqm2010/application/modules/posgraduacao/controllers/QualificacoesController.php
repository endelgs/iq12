<?php
// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/models/PgQualificacoes.php');
require_once(APPLICATION_PATH.'/models/PgBancaQualificacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH.'/models/Pessoas.php');

class Posgraduacao_QualificacoesController extends Zend_Controller_Action
{

    public function init()
    {
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
    	
    	$id_pos_graduacao = $this->_request->getParam("id_pos_graduacao");
		$this->view->id_pos_graduacao = $id_pos_graduacao;
		
		$idPessoa 		= $this->_request->getParam("id_pessoa");
		
		$tipoQualificacao = $this->_request->getParam("qualificacao");
		if(empty($tipoQualificacao))
			$tipoQualificacao=0;
		
		$this->view->tipoQualificacao=$tipoQualificacao;
		
		$pChecker = new ParamChecker();
		$tipoCurso = $pChecker->getParam(array(
				'rel_table' => 'tipo_cursos',
				'need'		=> 'slug',
				'value'		=> $idTipoCurso,
				'where'		=> "id_tipo_curso = '$idTipoCurso'"
			));
			
		$this->view->tipocurso=strtolower($tipoCurso);
		
		
		$qualificacoes_model = new Application_Models_PgQualificacoes();
		$pg_qualificacoes = $qualificacoes_model->CRUDread($id_pos_graduacao, $tipoQualificacao);
		$this->view->pg_qualificacoes = $pg_qualificacoes;
		
		$bancas_model = new Application_Models_PgBancaQualificacoes();
		$pg_bancas = $bancas_model->CRUDread() ;
		$this->view->pg_bancas = $pg_bancas;
		
		$general = new Models_General();
		$this->view->atribuicoes = $general->listView('v_atribuicoes', array('id', 'atribuicao'));
		$this->view->professores 	= $general->listView('v_professores', array('id', 'nome'));
		
		//if($alert==1)$this->view->alert = 'Dados gravados com sucesso!';
	}

    public function postAction()
    {
    	
    	$qualificacoes_model = new Application_Models_PgQualificacoes();
    	$bancas_model = new Application_Models_PgBancaQualificacoes();
    	
    	$tipoQualificacao = $this->_request->getParam("qualificacao");
		if(empty($tipoQualificacao))
			$tipoQualificacao=0;
			
    	$count_u =  count($_REQUEST['count_u']);
    	for($x=1; $x<=$count_u; $x++)
    	{
    		$titulo= str_replace('"','',$_REQUEST['u_titulo'.$x]);
    		
    		
    		if($_REQUEST['deletado_u'.$x]==0)
    		{
    			
    			$params = array(
	    			'titulo' => $titulo ,
	    			'data' => $this->toMysqlDateFull($_REQUEST['u_data'.$x]),
    				'dataconv' => $this->toMysqlDateFull($_REQUEST['u_dataconv'.$x]),
	    			'sala' => $_REQUEST['u_sala'.$x],
	    			'horario' => $_REQUEST['u_horario'.$x],
	    			'aprovado' => $_REQUEST['u_aprovado'.$x],
    				'conv' => $_REQUEST['u_conv'.$x],
	    			'tipo' => '0',
	    			'observacoes' => $_REQUEST['u_observacoes'.$x]
	    		);
	    		$qualificacoes_model->CRUDupdate($params,$_REQUEST['id_pg_qualificacao'.$x]);
	    		$params = array(
	    			'id_qualificacao' => $_REQUEST['id_pg_qualificacao'.$x],
	    			'id_professor' => $_REQUEST['u_professores'.$x],
	    			'id_atribuicao' => $_REQUEST['u_atribuicoes'.$x],
	    		);
	    		if($bancas_model->CRUDreadOne($params)=='')
	    		{
	    			if($_REQUEST['u_professores'.$x]!=1&&$_REQUEST['u_atribuicoes'.$x]!=1)
	    			{
	    				$bancas_model->CRUDcreate($params);	
	    			}
	    		}	
	    	}	
    		else
    		{
    			$bancas_model->CRUDQLdelete($_REQUEST['id_pg_qualificacao'.$x]);
    			$qualificacoes_model->CRUDdelete($_REQUEST['id_pg_qualificacao'.$x]);
    		}
    	}
    	
    	$count_i =  count($_REQUEST['count_i']);
    	for($x=1; $x<=$count_i; $x++)
    	{
    		$titulo= str_replace("'","\'",$_REQUEST['i_titulo'.$x]);
    		$titulo.= str_replace('"','\"',$titulo);
    		
    		$params = array(
    			'titulo' => $titulo,
    			'data' => $this->toMysqlDateFull($_REQUEST['i_data'.$x]),
    			'dataconv' => $this->toMysqlDateFull($_REQUEST['i_data_conv'.$x]),
    			'sala' => $_REQUEST['i_sala'.$x],
    			'horario' => $_REQUEST['i_horario'.$x],
    			'aprovado' => $_REQUEST['i_aprovado'.$x],
    			'conv' => $_REQUEST['i_conv'.$x],
    			'observacoes' => $_REQUEST['i_observacoes'.$x],
    			'tipo' => '0',
    			'tipo_qualificacao' => $tipoQualificacao,
    			'id_pos_graduacao' => $_REQUEST['id_pos_graduacao']
    		);
    		$id = $qualificacoes_model->CRUDcreate($params);
    		$params = array(
    			'id_qualificacao' => $id,
    			'id_professor' => $_REQUEST['i_professores'.$x],
    			'id_atribuicao' => $_REQUEST['i_atribuicoes'.$x],
    		);
    		if($_REQUEST['i_professores'.$x]!=1&&$_REQUEST['i_atribuicoes'.$x]!=1)
    		{
    			$bancas_model->CRUDcreate($params);	
    		}
    	}
    	$this->view->id_pos_graduacao = $_REQUEST['id_pos_graduacao'];
    }
    
    public function deleteAction()
    {         
        $idPessoa 	= $this->_request->getParam("id_pessoa");
    	$idTipoCurso	= $this->_request->getParam("tipo");
   	$idPosGraducao  = $this->_request->getParam("id_pos_graduacao");
        
        
    	$id = $this->_request->getParam("id");
    	$bancas_model = new Application_Models_PgBancaQualificacoes();
    	$bancas_model->CRUDdelete($id);
        
        $this->_redirect('/posgraduacao/qualificacoes/index/id_pessoa/'.$idPessoa.'/id_pos_graduacao/'.$idPosGraducao.'/tipo/'.$idTipoCurso.'?alert=Excluído com sucesso.');
    
    }
    
	public function toMysqlDateFull($string)
    {
        $a = explode('/', $string);
        $b = $a[2].'-'.$a[1].'-'.$a[0].' 00:00:00';
        return $b;
    }
}