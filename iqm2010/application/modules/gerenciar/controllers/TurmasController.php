<?php
require_once(APPLICATION_PATH.'/models/Turmas.php');
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/Periodos.php');
require_once(APPLICATION_PATH.'/models/Disciplinas.php');
require_once(APPLICATION_PATH.'/models/Orientadores.php');
require_once(APPLICATION_PATH.'/models/DocenteTurmas.php');
class Gerenciar_TurmasController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$general = new Models_General();
    	
    	$periodos=new Application_Models_Periodos();
		$this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));
		
		$materia=new Application_Models_Disciplinas();
		$this->view->materias = $general->listView('v_disciplinas', array('id', 'codigo'));
		
		$professores = new Application_Models_DocenteTurmas();
		$this->view->professores = $general->listView('v_professores', array('id', 'nome'));
		
		$docentes = new Application_Models_DocenteTurmas();
		$this->view->docentes = $general->listView('v_professores', array('id', 'nome'));
		
		$docenteTurmas = new Application_Models_DocenteTurmas();
		$this->view->docenteTurmas = $general->listViewHash('v_docente_turmas', array('id_turma','id_professor','nome'));
    }

    public function indexAction()
    {
    	$data = new Application_Models_Turmas();
    	$idTurma= $this->_request->getParam("id");
    	

    	if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) 
    	{
    		$this->verificaTurma($this->_request->getParam("materia"),$this->_request->getParam("ano"),$this->_request->getParam("turma"));
    		
    		$params = array(
    	      	'materia'	=> $this->_request->getParam("materia"), 
				'ano'		=> $this->_request->getParam("ano"),
				'periodo'	=> $this->_request->getParam("periodo"),
				'turma' 	=> $this->_request->getParam("turma"),
				'horario'	=> $this->_request->getParam("horario"),
				'min_alunos'=> $this->_request->getParam("min_alunos"),
				'max_alunos'=> $this->_request->getParam("max_alunos"),
				'coordenador'=> $this->_request->getParam("docente"),
				'id_turma' => $this->_request->getParam("id")
    		);
    	
 	  	   $data->CRUDupdate($params, $this->_request->getParam("id"));
    	  
    	   $docentes=$this->_request->getParam('professor');
           $docenteDisciplinas= new Application_Models_DocenteTurmas();
            
           $idTurma=$this->_request->getParam("id");
           
              if(is_array($docentes))
            {
	            foreach ($docentes as $docente)
	            {
	            	if($docente!=1)
	            	{
		            	$params=array(
		            	'id_turma'=>$idTurma,
		            	'id_docente'=>$docente
		            	);
		            	$docenteDisciplinas->CRUDcreate($params);
	            	}
	            }
	        }
    		
    		$this->view->alert = 'Dados alterados com sucesso';
    	}
    	else if($this->getRequest()->isPost() && $this->_request->getParam("turma")!="")
    	{
    		// CREATE
    		$existe=$this->verificaTurma($this->_request->getParam("auxMateria"),$this->_request->getParam("auxAno"),$this->_request->getParam("turma"));
    		
    		if($existe==false)
    		{
	            $params = array(
	      			'materia'	=> $this->_request->getParam("auxMateria"), 
					'ano'		=> $this->_request->getParam("auxAno"),
					'periodo'	=> $this->_request->getParam("auxPeriodo"),
					'turma' 	=> $this->_request->getParam("turma"),
					'horario'	=> $this->_request->getParam("horario"),
					'min_alunos'=> $this->_request->getParam("min_alunos"),
					'max_alunos'=> $this->_request->getParam("max_alunos"),
					'coordenador'=> $this->_request->getParam("docente"),
					'deletado'	=> '0'
	            );
	            
	              $idTurma=  $data->CRUDcreate($params);
	              $docentes=$this->_request->getParam('professor');
	          	   
	              $docenteDisciplinas= new Application_Models_DocenteTurmas();
			            
			            if(is_array($docentes))
			            {
				            foreach ($docentes as $docente)
				            {
				            	if($docente!=1)
				            	{
					            	$params=array(
					            	'id_turma'=>$idTurma,
					            	'id_docente'=>$docente
					            	);
					            	$docenteDisciplinas->CRUDcreate($params);
				            	}
				            }
				        }
				        
			$this->_redirect('/gerenciar/turmas');
    		}
    		else
    		{
    			$this->view->alert='Falha na inclusao. Essa turma já existe';
    		}
    	}
    	
       // READ
       	if(($this->_request->getParam("ano")!="") && ($this->_request->getParam("periodo")!="")&& ($this->_request->getParam("materia")!=""))
       	{
			$ano=$this->_request->getParam("ano");
			$materia=$this->_request->getParam("materia");
			$periodo=$_REQUEST["periodo"];
		}
		else if(($this->_request->getParam("auxAno")!="") && ($this->_request->getParam("auxPeriodo")!="")&& ($this->_request->getParam("auxMateria")!=""))
		{
			$ano=$this->_request->getParam("auxAno");
			$materia=$this->_request->getParam("auxMateria");
			$periodo=$_REQUEST["auxPeriodo"];
		}
		
		if($materia!="")
		{
			$turmas=$data->filtroTurma($ano,$materia,$periodo);
			
			$this->view->dropAno=$ano;
			$this->view->dropPeriodo=$periodo;
			$this->view->dropMateria=$materia;
			
			
			$clsPeriodo=new Application_Models_Periodos();
			$clsMateria=new Application_Models_Disciplinas();
		
			
			foreach ($turmas as $index=>$g)
			{
				$per=$clsPeriodo->getPeriodo($g['periodo']);;
				$turmas[$index]['nomePeriodo']=$per['periodo'];
				
				$per=$clsMateria->getDisciplina($g['materia']);;
				$turmas[$index]['codigoMateria']=$per['codigo'];
			}
			
			$this->view->data=$turmas;
			
			$ultimoDocente=$data->ultimoDocente($materia);
			$this->view->ultimoDocente= $ultimoDocente;
		}
    }
    
    function verificaTurma($idMateria, $ano, $turma)
    {	
		$data = new Application_Models_Turmas();
		$array=$data->CRUDread();

		$existe=false;
		
		foreach ($array as $arr)
		{
		 	if($arr['materia']==$idMateria && $arr['turma']==$turma && $arr['ano']==$ano)
		 	{
		 		$existe=true;
		 	}
		}
		return $existe;
    }    
    
    function deleteAction()
    {

    	$data = new Application_Models_Turmas();
    	$data->CRUDdelete($this->_request->getParam("id"));
    }
}