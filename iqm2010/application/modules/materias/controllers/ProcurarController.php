<?php
require_once(APPLICATION_PATH.'/models/Turmas.php');
require_once(APPLICATION_PATH.'/models/General.php');
require_once(APPLICATION_PATH.'/models/Periodos.php');
require_once(APPLICATION_PATH.'/models/Disciplinas.php');
require_once(APPLICATION_PATH.'/models/Orientadores.php');
require_once(APPLICATION_PATH.'/models/DocenteTurmas.php');
class Materias_ProcurarController extends Zend_Controller_Action
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
			
		$materia=1;
		$ano='';
		$periodo=1;
			
		//POG para delte
		$this->view->paramPeriodo=$this->_request->getParam("pperiodo");
		$this->view->paramAno=$this->_request->getParam("pano");
		$this->view->paramMateria=$this->_request->getParam("pmateria");
			
			
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
					'dias'=> $this->_request->getParam("dias"),
					'sala'=> $this->_request->getParam("sala"),
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

			$docentesExcluir=$this->_request->getParam('docenteExcluir');
			$docentesExcluir=explode('#',$docentesExcluir);

			forEach($docentesExcluir as $de)
			{

				$docenteDisciplinas->CRUDdeleteProfessor($de, $idTurma);
			}

			$pmateria= $this->_request->getParam("materia");
			$pano= $this->_request->getParam("ano");
			$pperiodo= $this->_request->getParam("periodo");

			$this->_redirect("/materias/procurar/index/pmateria/$pmateria/pano/$pano/pperiodo/$pperiodo");
				
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
					'dias'=> $this->_request->getParam("dias"),
					'sala'=> $this->_request->getParam("sala"),
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

					$pmateria= $this->_request->getParam("materia");
					$pano= $this->_request->getParam("ano");
					$pperiodo= $this->_request->getParam("periodo");

					$this->_redirect("/materias/procurar/index/pmateria/$pmateria/pano/$pano/pperiodo/$pperiodo");


			}
			else
			{
				$this->view->alert='Falha na inclusao. Essa turma já existe';
			}
		}
			
		// READ
		if(($this->_request->getParam("ano")!="") || ($this->_request->getParam("periodo")!="")|| ($this->_request->getParam("materia")!=""))
		{
			$ano=$this->_request->getParam("ano");
			$materia=$this->_request->getParam("materia");
			$periodo=$this->_request->getParam("periodo");
		}
		else if(($this->_request->getParam("auxAno")!="") && ($this->_request->getParam("auxPeriodo")!="")&& ($this->_request->getParam("auxMateria")!=""))
		{
			$ano=$this->_request->getParam("auxAno");
			$materia=$this->_request->getParam("auxMateria");
			$periodo=$this->_request->getParam("auxPeriodo");
		}
		else if(($this->_request->getParam("pano")!="") || ($this->_request->getParam("pperiodo")!="")|| ($this->_request->getParam("pmateria")!=""))
		{
			$ano=$this->_request->getParam("pano");
			$materia=$this->_request->getParam("pmateria");
			$periodo=$this->_request->getParam("pperiodo");
		}

		//print_r($ano." ".$materia." ".$periodo);
			
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

		//print_r($turmas);
			
		$this->view->data=$turmas;
			
		if(!($this->_request->getParam("pmateria")!=""))
		$ultimoDocente=$data->ultimoDocente($materia);

		$this->view->ultimoDocente= $ultimoDocente;
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
		$pmateria= $this->_request->getParam("materia");
		$pano= $this->_request->getParam("ano");
		$pperiodo= $this->_request->getParam("periodo");


		$data = new Application_Models_Turmas();
		$data->CRUDdelete($this->_request->getParam("id"));
			
		//    	if($this->_request->getParam("pmateria")!="")
		//    	{
		//    		$pmateria= $this->_request->getParam("pmateria");
		//			$pano= $this->_request->getParam("pano");
		//			$pperiodo= $this->_request->getParam("pperiodo");
		//
		//			var_dump('ENTROU');
		//    	}
			
		$this->_redirect("/materias/procurar/index/pmateria/$pmateria/pano/$pano/pperiodo/$pperiodo");
	}
}