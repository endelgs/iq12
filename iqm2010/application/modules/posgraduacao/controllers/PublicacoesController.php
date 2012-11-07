<?php
require_once(APPLICATION_PATH.'/models/PgPublicacoes.php');
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/pos_graduacoes.php');
require_once(APPLICATION_PATH.'/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH.'/models/Pessoas.php');
class Posgraduacao_PublicacoesController extends Zend_Controller_Action
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
		$idPosGraducao  = $this->_request->getParam("id_pos_graduacao");
    	$general = new Models_General();
    	
    	$pChecker = new ParamChecker();
		$tipoCurso = $pChecker->getParam(array(
				'rel_table' => 'tipo_cursos',
				'need'		=> 'slug',
				'value'		=> $idTipoCurso,
				'where'		=> "id_tipo_curso = '$idTipoCurso'"
			));
			
		$this->view->tipocurso=strtolower($tipoCurso);
	
		$tipoPessoa=0;
		
		//se for docente buscar id_docente
		if($idTipoCurso=="6")
		{

			$iddocente = $pChecker->getParam(array(
				'rel_table' => 'professores',
				'need'		=> 'id_professor',
				'value'		=> $idTipoCurso,
				'where'		=> "id_pessoa = '$idPessoa'"
			));
			
//		var_dump($iddocente);
//		exit;
			$idPosGraducao=$iddocente;
			
			$tipoPessoa=1;
		}
		//---------------------------------------
		
    	$this->view->tipopublicacao = $general->listView('v_tipo_publicacoes', array('id', 'publicacao')); 	
   		
    	$data = new Application_Models_PgPublicacoes();
    
    	// UPDATE
    	

    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
    		$params = array(
			  'qualicapes' => $this->_request->getParam("qualicapes"),
  			  'id_publicacao'=> $this->_request->getParam("tipopublicacaoU"),
  			  'artigo' =>$this->_request->getParam("artigo"),
  			  'data'  => $this->toMysqlDate('01/'.$this->_request->getParam("data")),   
    		  'patente'  => $this->_request->getParam("patente"),  
    		  'congresso' =>$this->_request->getParam("congresso"),  
    		);
    		
    		$data->CRUDupdate($params, $this->_request->getParam("id"),$tipoPessoa);
    		//$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost())
    	{
	        $params = array(         
    		  'qualicapes' => $this->_request->getParam("qualicapes"),
  			  'id_publicacao'=> $this->_request->getParam("tipopublicacao"),
  			  'artigo' =>$this->_request->getParam("artigo"),
  			  'data'  => $this->toMysqlDate('01/'.$this->_request->getParam("data")),   
    		  'patente'  => $this->_request->getParam("patente"),  
    		  'congresso' =>$this->_request->getParam("congresso"), 
	          'tipo' => $tipoPessoa,      
            
            //modificar este valor
            'id_pos_ou_docente'  => $idPosGraducao,
 			  
 	     );
         //print_r($params);
 	     $data->CRUDcreate($params);
 	     
 	     $idTipoCurso	= $this->_request->getParam("tipo");
 	     $this->_redirect('/posgraduacao/publicacoes/index/id_pessoa/'.$idPessoa.'/id_pos_graduacao/'.$idPosGraducao.'/tipo/'.$idTipoCurso.'?alert=Dados gravados com sucesso.');
       }
       
 
       
        $results=$data->CRUDread($idPosGraducao,$tipoPessoa);
        forEach ($results as $index=>$r){	
        	$results[$index]['text_congresso']= $r['congresso']==1? 'Internacional' : 'Nacional';
        	$results[$index]['texto_qualicapes']= $r['qualicapes']==1? 'Sim' : 'Não';
        	$results[$index]['texto_data']= $this->exibicaoDate($r['data']);
        }
        $this->view->data = $results; 


    }
   public function exibicaoDate($string)
   {
    	$a = explode('-', $string);
    	$b = $a[2].'/'.$a[1].'/'.$a[0];
    	return $b;
   }

   
   public function toMysqlDate($string)
   {
    	$a = explode('/', $string);
    	$b = $a[2].'-'.$a[1].'-'.$a[0];
    	return $b;
   }
       
    function deleteAction()
    {
    	
    	$data = new Application_Models_PgPublicacoes();
    	$data->CRUDdelete($this->_request->getParam("id"));
    	$this->view->idPessoa = $this->_request->getParam("id_pessoa");
    	$idPessoa 		= $this->_request->getParam("id_pessoa");
    	$idTipoCurso	= $this->_request->getParam("tipo");
   		$idPosGraducao  = $this->_request->getParam("id_pos_graduacao");

   		if($idTipoCurso=="6")
		{
			$pChecker = new ParamChecker();
			$iddocente = $pChecker->getParam(array(
				'rel_table' => 'professores',
				'need'		=> 'id_professor',
				'value'		=> $idTipoCurso,
				'where'		=> "id_pessoa = '$idPessoa'"
			));
    	
			$idPosGraduca=$iddocente;
		}
    	
    	$this->_redirect('/posgraduacao/publicacoes/index/id_pessoa/'.$idPessoa.'/id_pos_graduacao/'.$idPosGraducao.'/tipo/'.$idTipoCurso.'?alert=Excluído com sucesso.');
    }

}