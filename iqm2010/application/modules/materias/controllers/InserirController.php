<?php

require_once(APPLICATION_PATH . '/models/Turmas.php');
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Periodos.php');
require_once(APPLICATION_PATH . '/models/Disciplinas.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/models/DocenteTurmas.php');

class Materias_InserirController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $general = new Models_General();

        $periodos = new Application_Models_Periodos();
        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));

        $materia = new Application_Models_Disciplinas();
        $this->view->materias = $general->listView('v_disciplinas', array('id', 'codigo'));

        $professores = new Application_Models_DocenteTurmas();
        $this->view->professores = $general->listView('v_professores', array('id', 'nome'));

        $docentes = new Application_Models_DocenteTurmas();
        $this->view->docentes = $general->listView('v_professores', array('id', 'nome'));

        $docenteTurmas = new Application_Models_DocenteTurmas();
        $this->view->docenteTurmas = $general->listViewHash('v_docente_turmas', array('id_turma', 'id_professor', 'nome'));
    }

    public function indexAction() {
        $data = new Application_Models_Turmas();
        $idTurma = $_REQUEST['id'];


        if ($this->getRequest()->isPost() && $_REQUEST['id'] != "") {

            $this->verificaTurma($_REQUEST['materia'], $_REQUEST['ano'], $_REQUEST['turma']);


            $params = array(
                'materia' => $_REQUEST['materia'],
                'ano' => $_REQUEST['ano'],
                'periodo' => $_REQUEST['periodo'],
                'turma' => $_REQUEST['turma'],
                'horario' => $_REQUEST['horario'],
                'min_alunos' => $_REQUEST['min_alunos'],
                'max_alunos' => $_REQUEST['max_alunos'],
                'coordenador' => $_REQUEST['docente'],
                'id_turma' => $_REQUEST['id']
            );

            $data->CRUDupdate($params, $_REQUEST['id']);

            $docentes = $_REQUEST['professor'];
            $docenteDisciplinas = new Application_Models_DocenteTurmas();


            $idTurma = $_REQUEST['id'];

            if (is_array($docentes)) {
                foreach ($docentes as $docente) {
                    if ($docente != 1) {
                        $params = array(
                            'id_turma' => $idTurma,
                            'id_docente' => $docente
                        );
                        $docenteDisciplinas->CRUDcreate($params);
                    }
                }
            }



            $this->view->alert = 'Dados alterados com sucesso';
        } else if ($this->getRequest()->isPost() && $_REQUEST['turma'] != "") {
            // CREATE
            $existe = $this->verificaTurma($_REQUEST['auxMateria'], $_REQUEST['auxAno'], $_REQUEST['turma']);

            if ($existe == false) {
                $params = array(
                    'materia' => $_REQUEST['auxMateria'],
                    'ano' => $_REQUEST['auxAno'],
                    'periodo' => $_REQUEST['auxPeriodo'],
                    'turma' => $_REQUEST['turma'],
                    'horario' => $_REQUEST['horario'],
                    'min_alunos' => $_REQUEST['min_alunos'],
                    'max_alunos' => $_REQUEST['max_alunos'],
                    'coordenador' => $_REQUEST['docente'],
                    'dias' => $_REQUEST['dias'],
                    'sala' => $_REQUEST['sala'],
                    'deletado' => '0'
                );

                $idTurma = $data->CRUDcreate($params);
                $docentes = $_REQUEST['professor'];


                $docenteDisciplinas = new Application_Models_DocenteTurmas();

                if (is_array($docentes)) {
                    foreach ($docentes as $docente) {
                        if ($docente != 1) {
                            $params = array(
                                'id_turma' => $idTurma,
                                'id_docente' => $docente
                            );
                            $docenteDisciplinas->CRUDcreate($params);
                        }
                    }
                }

                $this->view->alert = 'Turma adicionada com sucesso.';
                //$this->_redirect('/materias/inserir');
            } else {
                $this->view->alert = 'Falha na inclusao. Essa turma já existe';
            }
        }

        // READ
        if (($_REQUEST['ano'] != "") && ($_REQUEST['periodo'] != "") && ($_REQUEST['materia'] != "")) {
            $ano = $_REQUEST['ano'];
            $materia = $_REQUEST['materia'];
            $periodo = $_REQUEST['periodo'];
        } else if (($_REQUEST['auxAno'] != "") && ($_REQUEST['auxPeriodo'] != "") && ($_REQUEST['auxMateria'] != "")) {
            $ano = $_REQUEST['auxAno'];
            $materia = $_REQUEST['auxMateria'];
            $periodo = $_REQUEST["auxPeriodo"];
        }

        if ($materia != "") {
            $turmas = $data->filtroTurma($ano, $materia, $periodo);

            $this->view->dropAno = $ano;
            $this->view->dropPeriodo = $periodo;
            $this->view->dropMateria = $materia;


            $clsPeriodo = new Application_Models_Periodos();
            $clsMateria = new Application_Models_Disciplinas();


            foreach ($turmas as $index => $g) {
                $per = $clsPeriodo->getPeriodo($g['periodo']);
                ;
                $turmas[$index]['nomePeriodo'] = $per['periodo'];

                $per = $clsMateria->getDisciplina($g['materia']);
                ;
                $turmas[$index]['codigoMateria'] = $per['codigo'];
            }

            $this->view->data = $turmas;

            $ultimoDocente = $data->ultimoDocente($materia);
            $this->view->ultimoDocente = $ultimoDocente;
        }
    }

    function verificaTurma($idMateria, $ano, $turma) {
        $data = new Application_Models_Turmas();
        $array = $data->CRUDread();

        $existe = false;

        foreach ($array as $arr) {
            if ($arr['materia'] == $idMateria 
                    && $arr['turma'] == $turma 
                    && $arr['ano'] == $ano
                    && $arr['periodo'] ==$_POST['auxPeriodo'] ) {
//                var_dump($arr['materia']);
//                var_dump($idMateria);
//                
//                var_dump($arr['turma']);
//                var_dump($turma);
//                
//                var_dump($arr['ano'] );
//                var_dump($ano);
                
                $existe = true;
            }
        }
        return $existe;
    }

    function deleteAction() {
        $materia = $_REQUEST['materia'];
        $ano = $_REQUEST['ano'];
        $periodo = $_REQUEST['periodo'];


        $data = new Application_Models_Turmas();
        $data->CRUDdelete($_REQUEST['id']);
        $this->_redirect("/materias/procurar/materia/$materia/ano/$ano/periodo/$periodo");
    }

}