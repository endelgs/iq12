<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/models/inscricoes.php');
require_once(APPLICATION_PATH . '/models/exames.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/models/Relatorios.php');
require_once(APPLICATION_PATH . '/models/Coorientadores.php');
require_once(APPLICATION_PATH . '/models/Ingressos.php');
require_once(APPLICATION_PATH . '/models/AreasDeConcentracao.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/models/PosGraduacaoArea.php');

class Posgraduacao_InscricoesController extends Zend_Controller_Action {

    public function init() {

        if ((int) $this->_request->getParam("tipo") == 5) {
            $idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
            $idPessoa = $this->_request->getParam("id_pessoa");
            if (empty($idPessoa)) {

                $pChecker = new ParamChecker();
                $idPessoa = $pChecker->getParam(array(
                            'rel_table' => 'pos_graduacoes',
                            'need' => 'id_pessoa',
                            'value' => $idPosGraduacao,
                            'where' => "id_pos_graduacao = '$idPosGraduacao'"
                        ));
            }


            $clsPosGraduacao = new Application_Models_Pos_Graduacoes();
            // $arrMestrado = $clsPosGraduacao->atualizaPosGraduacao($id_pessoa, '3');
            $id_mestrado = $clsPosGraduacao->atualizaPosGraduacao($idPessoa, '3');
            $id_doutorado = $clsPosGraduacao->atualizaPosGraduacao($idPessoa, '5');
            $this->view->idMestrado = $id_mestrado;

            $clsIngresso = new Application_Models_Ingressos();
            $arrIngresso = $clsIngresso->getIngressosByIDPosGraduacao($id_mestrado);
            
            $passagemDireta = $arrIngresso[0]['passagem_direta'];
            $ingressoDireto = $arrIngresso[0]['ingresso_direto'];


            if ((int) $passagemDireta == 1 || (int) $ingressoDireto == 1) {
                $this->_redirect('/posgraduacao/ingressos/index/id_pessoa/' . $id_pessoa . '/id_pos_graduacao/' . $id_doutorado . '/tipo/5');
            } elseif ($idPosGraduacao != $id_doutorado) {

                $this->_redirect('/posgraduacao/inscricoes/index/id_pessoa/' . $id_pessoa . '/id_pos_graduacao/' . $id_doutorado . '/tipo/5');
            }
        }


        /* Initialize action controller here */
        //$p = new Models_General();
        //$result = $p->getById('id_pessoa', $this->_request->getParam("id_pessoa"));
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function enviaparadoutoradoAction() {
        $id_pessoa = $this->_request->getParam('id_pessoa');
        $idTipoCurso = "5";

        $id_inscricao = $this->_request->getParam('id_inscricao');

        $id_pos_graduacao = $this->_request->getParam('id_pos_graduacao');

        $inscri = new Application_Models_Inscricoes();
        $novoIdPOs = $inscri->duplicaInscricaoParaDoutorado($id_inscricao, $id_pessoa);

        $ingres = new Application_Models_Ingressos();
        $id_ingresso = $ingres->duplicaIngressoParaDoutorado($id_pos_graduacao, $id_pessoa, $novoIdPOs);
        // echo 'Coo';
        $orientadores = new Application_Models_Orientadores();
        $orientadores->replicaOrientadores($id_ingresso['id_ingres_antigo'], $id_ingresso['id_ingres_novo']);

        $coorientadores = new Application_Models_Coorientadores();
        $coorientadores->replicaCoorientadores($id_ingresso['id_ingres_antigo'], $id_ingresso['id_ingres_novo']);

        $rel = new Application_Models_Relatorios();
        $rel->replicaRel($id_ingresso['id_ingres_antigo'], $id_ingresso['id_ingres_novo']);

        $area = new Application_Models_PosGraduacaoArea();
        $area->replicaArea($id_pos_graduacao, $novoIdPOs);


        $this->_redirect('/posgraduacao/inscricoes/index/id_pessoa/' . $id_pessoa . '/tipo/' . $idTipoCurso);
    }

    public function indexAction() {
      
        $idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $idPessoa = $this->_request->getParam("id_pessoa");
        $idTipoCurso = $this->_request->getParam("tipo");

        $this->view->tipoPosGraduacao = $idTipoCurso;

        //var_dump($idTipoCurso);

        $antigoIdPos = $idPosGraduacao;
        //colocar para ver a qual pessoa é da pos, sem levar em consideração o curso
        if (empty($idPessoa)) {
//			$db = $general->getAdapter();
//		$sql = "SELECT `id_pessoa`
//				FROM `pos_graduacoes`
//				WHERE `id_pos_graduacao` = '5016'";
//		$result = $db->query($sql);
//		
//		var_dump($result);
//		exit;
            $pChecker = new ParamChecker();
            $idPessoa = $pChecker->getParam(array(
                        'rel_table' => 'pos_graduacoes',
                        'need' => 'id_pessoa',
                        'value' => $idPosGraduacao,
                        'where' => "id_pos_graduacao = '$idPosGraduacao'"
                    ));
        }

        $pChecker = new ParamChecker();
        $idPosGraduacao = $pChecker->getParam(array(
                    'rel_table' => 'pos_graduacoes',
                    'need' => 'id_pos_graduacao',
                    'value' => $idPessoa,
                    'where' => "id_pessoa = '$idPessoa' AND id_tipo_curso = $idTipoCurso",
                    'policy' => 'last'
                ));

        if ($antigoIdPos != $idPosGraduacao)
            $this->_redirect("/posgraduacao/inscricoes/index/id_pessoa/$idPessoa/id_pos_graduacao/$idPosGraduacao/tipo/$idTipoCurso");
        $pChecker = new ParamChecker();

        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);
        $general = new Models_General();
        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));
        $this->view->areasDeConcentracao = $general->listView('v_areas_de_concentracao', array('id', 'area_de_concentracao'));
        $this->view->relacoesIes = $general->listView('v_relacoes_instituicao_ies', array('id', 'relacao_instituicao_ies'));


        $this->view->cidadesProva = $general->listView('v_cidade_provas', array('id', 'cidade'));
        $this->view->curso = $general->listView('v_cursos', array('id', 'curso'));
        $this->view->professores = $general->listView('v_professores', array('id', 'nome'));
        $this->view->instituicoes = $general->listView('v_instituicoes', array('id', 'instituicao'));

        // Essa eh a parte onde eu cuido da cidade excluida
        // Se a cidade onde o cara fez prova for excluida, ainda assim ela deve aparecer no combobox
        $db = $general->getAdapter();
        $sql = "SELECT cp.id_cidade_prova AS id, c.cidade FROM cidade_provas cp,cidades c,v_inscricoes i WHERE cp.id_cidade = c.id_cidade AND i.id_cidade_prova = cp.id_cidade_prova AND cp.deletado = 1 AND i.id_pos_graduacao = '{$idPosGraduacao}'";
        $result = $db->query($sql);
        $cidadeExcluida = $result->fetch();
        if (!empty($cidadeExcluida))
            $this->view->cidadesProva[$cidadeExcluida['id']] = $cidadeExcluida['cidade'];

        // Essa eh a parte onde eu cuido da instituicao excluida
        $db = $general->getAdapter();
        $sql = "SELECT i.id_instituicao as id, i.instituicao FROM `instituicoes` i INNER JOIN inscricoes ON inscricoes.id_instituicao=i.id_instituicao WHERE i.deletado=1 AND inscricoes.id_pos_graduacao = '{$idPosGraduacao}'";
        $result = $db->query($sql);
        $instituicaoExcluida = $result->fetch();
        if (!empty($instituicaoExcluida))
            $this->view->instituicoes[$instituicaoExcluida['id']] = $instituicaoExcluida['instituicao'];

        // Essa eh a parte onde eu cuido do curso excluido
        $db = $general->getAdapter();
        $sql = "SELECT c.id_curso as id, c.curso FROM cursos c INNER JOIN inscricoes i ON c.id_curso=i.id_curso WHERE c.deletado=1 AND i.id_pos_graduacao = '{$idPosGraduacao}'";
        $result = $db->query($sql);
        $cursoExcluido = $result->fetch();
        if (!empty($cursoExcluido))
            $this->view->curso[$cursoExcluido['id']] = $cursoExcluido['curso'];


        $pos_graduacoes = new Application_Models_Pos_Graduacoes();
        $exames = new Application_Models_Exames();
        $ingresso = new Application_Models_Ingressos();
        $inscricoes = new Application_Models_Inscricoes();

        $this->view->outros = 'false';

        if ($this->_request->getParam("id_pessoa") != "") {
            $resultPos = $pos_graduacoes->getPosByPessoa($this->_request->getParam("id_pessoa"), $idTipoCurso);
            $id_pos_graduacao = '';
            foreach ($resultPos as $rowResult) {
                $id_pos_graduacao = $rowResult['id_pos_graduacao'];
            }
            if ($id_pos_graduacao != '') {
                $resultExm = $exames->getExames($id_pos_graduacao);
                foreach ($resultExm as $item2) {
                    if ($item2['matriculado'] == 1)
                        $this->view->outros = 'true';
                }

                /* $resultIng = $ingresso->getIngressosByIDPosGraduacao($id_pos_graduacao);
                  foreach($resultIng as $item3)
                  {
                  $this->view->outros = 'true';
                  } */
                $resultIns = $inscricoes->getInscricoesByPos($id_pos_graduacao);

                $this->view->resultado = $resultIns;
            }
        }else{
            /* TROCA `id_pos_graduacao` POR `id_pessoa` */
            if ($this->_request->getParam("id_pos_graduacao") != "") {

                $resultPos = $pos_graduacoes->getPessoaByPos($this->_request->getParam("id_pos_graduacao"), $idTipoCurso);
                $id_pessoa = '';
                foreach ($resultPos as $rowResult) {
                    $id_pessoa = $rowResult['id_pessoa'];
                }
            }
            $idTipoCurso = $this->_request->getParam("tipo");
            if ($id_pessoa != '')
                $this->_redirect("/posgraduacao/inscricoes/index/id_pessoa/$id_pessoa/id_pos_graduacao/$idPosGraduacao/tipo/$idTipoCurso");
            else
                $this->_redirect('/pessoas/geral/');
        }
    }

    public function postAction() {

        $inscricao  = new Application_Models_Inscricoes();
        $exames     = new Application_Models_Exames();
        $pos_graduacoes = new Application_Models_Pos_Graduacoes();

        $id_pessoa = $this->_request->getParam("id_pessoa");
        $idTipoCurso = $this->_request->getParam("tipo");

        $arrPos = $pos_graduacoes->getPosByPessoa($id_pessoa, $idTipoCurso);
        if ($arrPos) {
            foreach ($arrPos as $item) {
                $id_pos_graduacao = $item['id_pos_graduacao'];
            }
        } else {
            $data = array(
                'id_tipo_curso' => $idTipoCurso,
                'id_pessoa' => $id_pessoa
            );

            $id_pos_graduacao = $pos_graduacoes->addPosGraduacao($data);
        }

        $chkNum = 0;
        $arrNum = count($_REQUEST['data']);

        if ($arrNum >= 1) {
            for ($x = 0; $x < $arrNum; $x++) {

                $chkNum++;
                $aceita = 0;
                $bolsa = 0;
                $aproveitamento = 0;
                if ($_REQUEST['bolsa' . $chkNum])
                    $bolsa = 1;
                if ($_REQUEST['aceita' . $chkNum])
                    $aceita = 1;

                if ($_REQUEST['aproveitamento' . $chkNum])
                    $aproveitamento = 1;
                if (is_null($_REQUEST['data'][$x]))
                    $data_inscricao = 0;
                else {
                    $strData = explode("/", ($_REQUEST['data'][$x]));
                    $data_inscricao = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);
                }
                if (is_null($_REQUEST['dataConclusao'][$x]))
                    $data_conclusao = 0;
                else {
                    $strData = explode("/", ($_REQUEST['dataConclusao'][$x]));
                    $data_conclusao = ($strData[1] . "-" . $strData[0] . "-01");
                }
                $data = array(
                    'codigo_inscricao' => $_REQUEST['codigo'][$x],
                    'data_inscricao' => $data_inscricao,
                    'id_pos_graduacao' => $id_pos_graduacao,
                    'id_periodo' => $_REQUEST['periodos'][$x],
                    'id_professor' => $_REQUEST['professores'][$x],
                    'id_area_de_concentracao' => $_REQUEST['areaconcetracao'][$x],
                    'id_relacao_instituicao_ies' => $_REQUEST['relacao'][$x],
                    'id_cidade_prova' => $_REQUEST['cidade'][$x],
                    'id_instituicao' => $_REQUEST['instituicao'][$x],
                    'id_instituicao_mestrado' => $_REQUEST['instituicao_mestrado'][$x],
                    'id_curso' => $_REQUEST['curso'][$x],
                    'data_conclusao' => $data_conclusao,
                    'pretende_aproveitamento' => $aproveitamento,
                    'bolsa' => $bolsa,
                    'inscricao_aceita' => $aceita,
                    'deletado' => 0
                );
                $id_inscricao = $inscricao->addInscricoes($data);
                //print_r($id_inscricao);die();
                if ($aceita == 1) {
                    $data = array(
                        'id_inscricao' => $id_inscricao,
                        'id_pos_graduacao' => $id_pos_graduacao,
                        'id_cidade_prova' => 1,
                        'data_exame' => '0000-00-00 00:00:00'
                    );

                    $id_exame = $exames->addExames($data);
                    //print_r($id_exame);die();
                    $data = array(
                        'id_exame' => $id_exame
                    );
                    $inscricao->updateInscricoes($data, $id_inscricao);


                    if ($_REQUEST['areaconcetracao'][$x] != "1") {
                        $dataInsc = array(
                            'id_pos' => $id_pos_graduacao,
                            'id_area_de_concentracao' => $_REQUEST['areaconcetracao'][$x],
                            'data_de_mudanca' => date('Y-m-d H:i:s'),
                            'observacoes' => 'Inscrições'
                        );

                        $clsPosGraduacao = new Application_Models_PosGraduacaoArea();
                        $clsPosGraduacao->insert($dataInsc);
                    }
                }
            }
        }

        $chkNum = 0;
        $arrNum = count($_REQUEST['u_data']);
        if ($arrNum >= 1) {
            for ($x = 0; $x < $arrNum; $x++) {
                $aceita = 0;
                $bolsa = 0;
                $aproveitamento = 0;
                if ($_REQUEST['u_bolsa' . $chkNum])
                    $bolsa = 1;
                if ($_REQUEST['u_aceita' . $chkNum])
                    $aceita = 1;
                if ($_REQUEST['u_aproveitamento' . $chkNum])
                    $aproveitamento = 1;

                if (is_null($_REQUEST['u_data'][$x]))
                    $data_inscricao = 0;

                else {
                    $strData = explode("/", ($_REQUEST['u_data'][$x]));
                    $data_inscricao = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);
                }
                if (is_null($_REQUEST['u_dataConclusao'][$x]))
                    $data_conclusao = 0;
                else {
                    $strData = explode("/", ($_REQUEST['u_dataConclusao'][$x]));
                    $data_conclusao = ($strData[1] . "-" . $strData[0] . "-01");
                }

                //var_dump($_REQUEST);

                $data = array(
                    'codigo_inscricao' => $_REQUEST['codigo'][$x],
                    'data_inscricao' => $data_inscricao,
                    'id_pos_graduacao' => $id_pos_graduacao,
                    'id_periodo' => $_REQUEST['u_periodos'][$x],
                    'id_professor' => $_REQUEST['u_professores'][$x],
                    'id_area_de_concentracao' => $_REQUEST['u_areaconcetracao'][$x],
                    'id_relacao_instituicao_ies' => $_REQUEST['u_relacao'][$x],
                    'id_cidade_prova' => $_REQUEST['u_cidade'][$x],
                    'id_instituicao' => $_REQUEST['u_instituicao'][$x],
                    'id_instituicao_mestrado' => $_REQUEST['u_instituicao_mestrado'][$x],
                    'id_curso' => $_REQUEST['u_curso'][$x],
                    'data_conclusao' => $data_conclusao,
                    'pretende_aproveitamento' => $aproveitamento,
                    'bolsa' => $bolsa,
                    'inscricao_aceita' => $aceita,
                    'deletado' => $_REQUEST['u_deletado'][$x]
                );

                $arrInscricao[] = $_REQUEST['u_idInscricao'][$x];
                $id_inscricao = $_REQUEST['u_idInscricao'][$x];
                $inscricao->updateInscricoes($data, $id_inscricao);

                $arrPos2 = $pos_graduacoes->getPessoaByPos($id_pos_graduacao, 3);
                foreach ($arrPos2 as $item2) {
                    if ($item2['n_inscricao'] == $id_inscricao && $_REQUEST['u_deletado'][$x] == 1) {
                        $data = array(
                            'n_exame' => 0,
                            'n_inscricao' => 0
                        );
                        $pos_graduacoes->updatePosGraduacao($data, $id_pos_graduacao);
                    }
                }

                if ($aceita == 0 && $_REQUEST['u_id_exame' . $x] != '') {
                    $exames->deleteExames($_REQUEST['u_id_exame' . $x]);
                    $data = array(
                        'id_exame' => null
                    );
                    $inscricao->updateInscricoes($data, $id_inscricao);
                    $data = array(
                        'n_exame' => 0
                    );
                    $pos_graduacoes->updatePosGraduacao($data, $id_pos_graduacao);
                }
//                print_r($_REQUEST);die();
                if ($aceita == 1 && ($_REQUEST['u_id_exame' . $x] == '' || !isset($_REQUEST['u_id_exame'.$x]))) {
                    $data = array(
                        'id_inscricao' => $id_inscricao,
                        'id_pos_graduacao' => $id_pos_graduacao,
                        'id_cidade_prova' => 1,
                        'data_exame' => '0000-00-00 00:00:00'
                    );
                    $id_exame = $exames->addExames($data);
                    //print_r($id_exame);die();
                    $data = array(
                        'id_exame' => $id_exame
                    );
                    $inscricao->updateInscricoes($data, $id_inscricao);
                }
                $chkNum++;
            }
        }
        $inscricoesDeletadas = $inscricao->getInscricoesByPosDeletado();
        foreach ($inscricoesDeletadas as $item) {
            $exames->deleteExames($item['id_exame']);
        }
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/inscricoes/index/id_pessoa/' . $id_pessoa . '/tipo/' . $idTipoCurso . '?alert=Dados gravados com sucesso.');
    }

}