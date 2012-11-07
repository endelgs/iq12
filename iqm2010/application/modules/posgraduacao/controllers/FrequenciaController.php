<?php
// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH.'/models/Frequencia.php');
require_once(APPLICATION_PATH.'/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH.'/../library/relatorios/GridPdf.php');
require_once(APPLICATION_PATH.'/models/Pessoas.php');

class Posgraduacao_FrequenciaController extends Zend_Controller_Action
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
		
		
     	$idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
    	
		$clsFrequecia= new Application_Models_Frequencia();
		$arrFrequencia=$clsFrequecia->CRUDread($idPosGraduacao);

		
		foreach ($arrFrequencia as $index=>$arrF)
		{
			$mask = new mascara();
			$mes=$mask->datetimeSQLToMascaraMesAno($arrF['mes']);
			$arrFrequencia[$index]['data']=$mes;
		}
		
		$headers= array('data' =>'Data');
		
		$flexGrid = new GridPdf();
		$this->view->flex=$flexGrid->flexGridJS($arrFrequencia,$headers);
		
	  
		$pChecker = new ParamChecker();
		$tipoCurso = $pChecker->getParam(array(
				'rel_table' => 'tipo_cursos',
				'need'		=> 'slug',
				'value'		=> $idTipoCurso,
				'where'		=> "id_tipo_curso = '$idTipoCurso'"
			));
			
		$this->view->tipocurso=strtolower($tipoCurso);
	
		
			
	}

}