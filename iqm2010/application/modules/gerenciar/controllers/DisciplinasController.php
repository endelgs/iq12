<?php
require_once(APPLICATION_PATH.'/models/Disciplinas.php');
require_once(APPLICATION_PATH.'/models/Orientadores.php');
require_once(APPLICATION_PATH.'/models/Periodos.php');
require_once(APPLICATION_PATH.'/models/OferecimentosDisciplinas.php');
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/General.php');
class Gerenciar_DisciplinasController extends Zend_Controller_Action
{

    public function init()
    {
        $general = new Models_General();
		
		$periodos=new Application_Models_Periodos();
		$this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));

		$oferecimentos = new Application_Models_OferecimentosDisciplinas();
		$this->view->oferecimento = $general->listView('v_oferecimentos_disciplinas', array('id', 'oferecimento_disciplina'));
    }
	public function duplicarAction()
	{
		$model = new Application_Models_Disciplinas();
		$model->duplicar($this->_request->getParam("id_disciplina"));
		$this->_redirect('/gerenciar/disciplinas/');
		
	}
    public function indexAction()
    {
    	$data = new Application_Models_Disciplinas(); 
    	// UPDATE

    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) 
    	{
    		$idDisciplina= $this->_request->getParam("id");
    		  
    		$exluidos=$this->_request->getParam('docenteExcluir');
    		if($exluidos!="")
    		{
				$arrExcluidos= explode("#",$exluidos);
				print_r($strExcluidos);
				foreach($arrExcluidos as $arrExcluido)
				{
					if($arrExcluido!="")
					{
						$docenteDisciplinasDelete= new Application_Models_DocenteDisciplinas();
						$docenteDisciplinasDelete->CRUDdeleteProfessor($arrExcluido,$idDisciplina);
					}
				}
    		}
    		$params = array(
    	      'titulo'          => $this->_request->getParam("titulo"),
              'subtitulo'     => $this->_request->getParam("subtitulo"),
              'codigo'        => $this->_request->getParam("codigo"),
              'pre_requisitos'=> $this->_request->getParam("pre_requisitos"),
              'ementa'        => $this->_request->getParam("ementa"),
              'bibliografia'  => $this->_request->getParam("bibliografia"),
              'creditos'      => $this->_request->getParam("creditos"),
              'carga_horaria' => $this->_request->getParam("carga_horaria"),
              'oferecimento'  => $this->_request->getParam("oferecimento"),
    	      'observacoes'     => $this->_request->getParam("observacoes"),
              'deletado' => '0'
    		);
    		
    		print_r($params);
    		    
    		$data->CRUDupdate($params, $this->_request->getParam("id"));
    		
    	   $docentes=$this->_request->getParam('professor');

    		$this->view->data = $data->CRUDread();
    		$this->view->alert = 'Dados alterados com sucesso';
    		$this->_redirect('/gerenciar/disciplinas/');
    	}
    	else if($this->getRequest()->isPost())
    	{
    		
    		$arrayAux=$data->CRUDread();
    		$repetido=false;
    		
    		foreach ($arrayAux as $result)
	    	{
	    		if($result['codigo']==$this->_request->getParam("codigo"))
					$repetido=true;
	    	}
	    	
	    	if($repetido==true)
	    	{
	    		$this->view->alert = 'Esse código já existe. Tente novamente.';
	    	}
	    	else
	    	{
		    		//echo 'CREATE';
		            $params = array(
		              'titulo'          => $this->_request->getParam("titulo"),
		              'subtitulo'       => $this->_request->getParam("subtitulo"),
		              'codigo'          => $this->_request->getParam("codigo"),
		              'pre_requisitos'  => $this->_request->getParam("pre_requisitos"),
                  'ementa'          => $this->_request->getParam("ementa"),
		              'bibliografia'    => $this->_request->getParam("bibliografia"),
		              'creditos'        => $this->_request->getParam("creditos"),
		              'carga_horaria'   => $this->_request->getParam("carga_horaria"),
		              'oferecimento'    => $this->_request->getParam("oferecimento"),
		              'observacoes'     => $this->_request->getParam("observacoes"),
		              'deletado' => '0'
		            );
		            
		           $idDisciplina= $data->CRUDcreate($params);
		    	}
      	}
    	// READ
        
    	$arrayDisciplina= array();
    	$i=0;
    	
    	if(is_array($results))
    	{
	    	foreach ($results as $result)
	    	{
	    		$periodo = new Application_Models_Periodos();
	    		$strPeriodo=$periodo->getPeriodo($result['periodo']);
	    		
	    		$oferecimento = new Application_Models_OferecimentosDisciplinas();
	    		$strOferecimento= $oferecimento->getOferecimentosDisciplinas($result['oferecimento']);
	    		
	    		$arrayDisciplina[$i]['id_disciplina']=$result['id_disciplina'];
	    		$arrayDisciplina[$i]['codigo']=$result['codigo'];
	    		$arrayDisciplina[$i]['titulo']=$result['titulo'];
	    		$arrayDisciplina[$i]['subtitulo']=$result['subtitulo'];
	    		$arrayDisciplina[$i]['pre_requisitos']=$result['pre_requisitos'];
                        $arrayDisciplina[$i]['ementa']=$result['ementa'];
	    		$arrayDisciplina[$i]['bibliografia']=$result['bibliografia'];
	    		$arrayDisciplina[$i]['creditos']=$result['creditos'];
	    		$arrayDisciplina[$i]['carga_horaria']=$result['carga_horaria'];
	    		$arrayDisciplina[$i]['oferecimento']=$result['oferecimento'];
	    		$arrayDisciplina[$i]['nome_oferecimento']=$strOferecimento['oferecimento_disciplina'];
	    		$arrayDisciplina[$i]['observacoes']=$result['observacoes'];
	    		$arrayDisciplina[$i]['deletado']=$result['deletado'];
	    		$i++;
	    	}
    	}
    	$this->view->data=$arrayDisciplina;
        $this->view->data = $data->CRUDread();
    }
    function deleteAction()
    {
    
    	$data = new Application_Models_Disciplinas();
    	$data->CRUDdelete($this->_request->getParam("id"));
    	print_r($this->_request->getParam("id"));
    } 
}