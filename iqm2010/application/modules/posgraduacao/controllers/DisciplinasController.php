<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Disciplinas.php');
require_once(APPLICATION_PATH . '/models/DisciplinaAluno.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/OferecimentosDisciplinas.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');

class Posgraduacao_DisciplinasController extends Zend_Controller_Action {

    public function init() {
        $p = new Models_General();
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function indexAction() {

        $idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $idPessoa = $this->_request->getParam("id_pessoa");
        $idPessoa = $this->_request->getParam("id_pessoa");
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->view->tipoPosGraduacao = $idTipoCurso;

        $pChecker = new ParamChecker();
        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);
//		
//		if(empty($idPosGraduacao))
//		{
//			$pChecker = new ParamChecker();
//			$idPosGraduacao = $pChecker->getParam(array(
//				'rel_table' => 'pos_graduacoes',
//				'need'		=> 'id_pos_graduacao',
//				'value'		=> $idPessoa,
//				'where'		=> "id_pessoa = '$idPessoa' AND id_tipo_curso = $idTipoCurso"
//			));
//			$this->_redirect('/posgraduacao/bolsas/index/id_pos_graduacao/'.$idPosGraduacao.'/id_pessoa/'.$idPessoa);
//		}




        $data = new Application_Models_Disciplinas();

        $pgDisc = new Application_Models_DisciplinaAluno();

        $grid = $pgDisc->gridRead($this->_request->getParam("id_pos_graduacao"));
        
        $oferimento = new Application_Models_OferecimentosDisciplinas();
        foreach ($grid as $index => $g) {
            if ($g['cancelado'] == 1)
                $grid[$index]['msgCancelado'] = 'Sim';
            else
                $grid[$index]['msgCancelado'] = 'Não';

            if ($g['convalida'] == 1)
                $grid[$index]['msgConvalidado'] = 'Sim';
            else
                $grid[$index]['msgConvalidado'] = 'Não';

            $of = $oferimento->getOferecimentosDisciplinas($g['oferecimento']);
            $grid[$index]['msgOferecimento'] = $of['oferecimento_disciplina'];
        }
        $this->view->grid = $grid;

        if (($this->_request->getParam("ano") != "") && ($this->_request->getParam("periodo") != "")) {
            //			if($this->_request->getParam("anoG")=="" && $this->_request->getParam("periodoG")=="")
            //			{
            $anoPes = $this->_request->getParam("ano");
            $periPes = $_REQUEST["periodo"];
            //var_dump($_REQUEST);
            $disciplinas = $data->disciplinasByAnoPeriodo($anoPes, $periPes);

            $this->view->anoPes = $anoPes;
            $this->view->periPes = $periPes;
            $disView = array();
            forEach ($disciplinas as $disciplina) {
                $disView[$disciplina['id_disciplina']] = $disciplina['codigo'];
            }

            $this->view->disciplinas = $disView;



            //			}
        }

        $escolhaDisci = $this->_request->getParam("disciplinas");
        $escolhaPerido = $this->_request->getParam("periG");
        $escolhaAno = $this->_request->getParam("anoG");

        if (($escolhaPerido != "") && ($escolhaAno != "")) {
            $disTuma = $data->turmaByAnoPeriodoDisciplina($escolhaAno, $escolhaPerido, $escolhaDisci);

            $turView = array();
            forEach ($disTuma as $turma) {
                $turView[$turma['id']] = $turma['turma'];
            }
            $this->view->turmas = $turView;


            $anoPes = $this->_request->getParam("ano");
            $periPes = $_REQUEST["periodo"];
            //var_dump($_REQUEST);
            $disciplinas = $data->disciplinasByAnoPeriodo($escolhaAno, $escolhaPerido);

            $this->view->ano2 = $escolhaAno;
            $this->view->periodo2 = $escolhaPerido;
            $this->view->desciplina2 = $escolhaDisci;

            $disView = array();
            forEach ($disciplinas as $disciplina) {
                $disView[$disciplina['id_disciplina']] = $disciplina['codigo'];
            }

            $this->view->disciplinas = $disView;
        }

        // EDICAO OU INSERCAO SAO TRATADOS AQUI
        if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0 && $this->_request->getParam("instiuicaoU") != "") {

            $params = array(
                'conceito' => $this->_request->getParam("conceito"),
                'cancelado' => $this->_request->getParam("canceladaEd") == 'on' ? '1' : '0',
                'convalida' => $this->_request->getParam("convalidadoEd") == 'on' ? '1' : '0',
                'con_disciplina' => $this->_request->getParam("con_disciplina"),
                'con_codigo' => $this->_request->getParam("con_codigo"),
                'id_inst' => (int) $this->_request->getParam("instiuicaoU"),
                'con_credito' => $this->_request->getParam("con_credito"),
                'con_data' => $this->_request->getParam("con_data"),
                'con_conceito' => $this->_request->getParam("con_conceito"),
                'id_pos_graduacao' => (int) $this->_request->getParam("id_pos_graduacao")
            );


            $pgDisc->CRUDupdate($params, (int) $this->_request->getParam("id"));

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/disciplinas/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
        } elseif ($this->_request->getParam("turmas") != "") {


            $params = array(
                'id_turma' => (int) $this->_request->getParam("turmas"),
                'conceito' => $this->_request->getParam("conceito"),
                'cancelado' => $this->_request->getParam("cancelada") == 'on' ? '1' : '0',
                'convalida' => $this->_request->getParam("convalidado") == 'on' ? '1' : '0',
                'con_disciplina' => $this->_request->getParam("conDisciplina"),
                'con_codigo' => $this->_request->getParam("conCod"),
                'id_inst' => (int) $this->_request->getParam("id_instituicao"),
                'con_credito' => $this->_request->getParam("conCred"),
                'con_data' => $this->_request->getParam("condata"),
                'con_conceito' => $this->_request->getParam("conConceito"),
                'id_pos_graduacao' => (int) $this->_request->getParam("id_pos_graduacao")
            );
//			var_dump($params);
//			exit;
            $pgDisc->CRUDcreate($params);

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/disciplinas/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso . '?');
        }




        $general = new Models_General();


        //lista anos possíveis
        $anos = $data->anosPossiveis();
        $anosView = array();
        foreach ($anos as $index => $ano) {
            $anosView[$anos[$index]['ano']] = $anos[$index]['ano'];
        }

        $this->view->anos = $anosView;
        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));
        $this->view->instituicoes = $general->listView('v_instituicoes', array('id', 'instituicao'));
//
//		$docenteDisciplinas = new Application_Models_DocenteDisciplinas();
        $this->view->docenteDisciplinas = array();
    }

    function deleteAction() {

        $data = new Application_Models_DisciplinaAluno();
        $data->CRUDdelete($this->_request->getParam("id"));
        $this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/disciplinas/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

}