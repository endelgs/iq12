<?php

require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/models/inscricoes.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/Ingressos.php');
require_once(APPLICATION_PATH . '/models/PosGraduacaoArea.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/models/Coorientadores.php');
require_once(APPLICATION_PATH . '/models/Relatorios.php');
require_once(APPLICATION_PATH . '/models/Trancamentos.php');
require_once(APPLICATION_PATH . '/models/DesligadoDetalhes.php');
require_once(APPLICATION_PATH . '/models/PgQualificacoes.php');
require_once(APPLICATION_PATH . '/models/Reingresso.php');
require_once(APPLICATION_PATH . '/models/PgBancaQualificacoes.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');

class Posgraduacao_IngressosController extends Zend_Controller_Action {

    public function init() {
        $p = new Models_General();
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function indexAction() {
        $general = new Models_General();
        /* TROCA `id_pessoa` POR `id_pos_graduacao` */

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->view->tipoPosGraduacao = $idTipoCurso;
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


        $pChecker = new ParamChecker();
        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);



        $clsPosGraduacao = new Application_Models_Pos_Graduacoes();
        $arrMestrado = $clsPosGraduacao->getMestradoByPessoa($idPessoa);
        $id_mestrado = $arrMestrado['id_pos_graduacao'];
        $this->view->idMestrado = $id_mestrado;

        $clsIngresso = new Application_Models_Ingressos();
        $arrIngresso = $clsIngresso->getIngressosByIDPosGraduacao($id_mestrado);
        $this->view->passagemDireta = $arrIngresso[0]['passagem_direta'];
        $this->view->ingressoDireto = $arrIngresso[0]['ingresso_direto'];



        $idsData = $general->getById('id_pos_graduacao', $this->_request->getParam("id_pos_graduacao"));
        $this->view->idIngresso = (int) $idsData['id_ingresso'];
        if ($this->view->idIngresso != 0) {

            $ingresso = new Application_Models_Ingressos();
            $this->view->ingresso = $ingresso->getIngresso($this->view->idIngresso);


            $orientadores = new Application_Models_Orientadores();
            $this->view->orientadores = $orientadores->getByIngresso($this->view->idIngresso);


            $coorientadores = new Application_Models_Coorientadores();
            $arrCoori = $coorientadores->getByIngresso($this->view->idIngresso);
            
            foreach ($arrCoori as $index => $a) {
                if ((int)$a['atual'] == 0)
                    $arrCoori[$index]['eh_atual'] = "Não";
                else
                    $arrCoori[$index]['eh_atual'] = "Sim";
            }
            
            $this->view->coorientadores = $arrCoori;

            $pos_area_de_concentracao = new Application_Models_PosGraduacaoArea();
            $this->view->pos_area_de_concentracao = $pos_area_de_concentracao->CRUDread($this->_request->getParam("id_pos_graduacao"));

            $inscricoes = new Application_Models_Inscricoes();
            $arrInsc = $inscricoes->getInscricoesByPos($this->_request->getParam("id_pos_graduacao"));

            $reingresso = new Application_Models_Reingresso();
            $this->view->reingresso = $reingresso->CRUDreadByPos($this->_request->getParam("id_pos_graduacao"));


            $arr = array();
            $i = 0;

            foreach ($arrInsc as $strInsc) {
                $professor = $strInsc['id_professor'];
                $i++;
            }


            $relatorio = new Application_Models_Relatorios();
            $this->view->relatorios = $relatorio->getByIngresso($this->view->idIngresso);
            $trancamentos = new Application_Models_Trancamentos();
            $this->view->trancamentos = $trancamentos->getTrancamentosByIDIngresso($this->view->idIngresso);
        }
        $this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));
        $this->view->desligado = $general->listView('v_desligado_detalhes', array('id', 'desligado_detalhe'));
        $this->view->linhasDePesquisa = $general->listView('v_linhas_de_pesquisa', array('id', 'linha_de_pesquisa'));
        $this->view->areasDeConcentracao = $general->listView('v_areas_de_concentracao', array('id', 'area_de_concentracao'));
        $this->view->relacoesIes = $general->listView('v_relacoes_instituicao_ies', array('id', 'relacao_instituicao_ies'));
        $this->view->professores = $general->listView('v_professores', array('id', 'nome'));
        $this->view->instituicoes = $general->listView('v_instituicoes', array('id', 'instituicao'));
        $this->view->tiposDeTrancamento = $general->listView('v_tipo_trancamentos', array('id', 'tipo_trancamento'));
        $this->view->trancamentoPeriodo = $general->listView('v_periodos', array('id', 'periodo'));
        $this->view->cidadesProva = $general->listView('v_cidade_provas', array('id', 'cidade'));
        $this->view->atribuicoes = $general->listView('v_atribuicoes', array('id', 'atribuicao'));

        $qualificacoes_model = new Application_Models_PgQualificacoes();
        $this->view->qid = $qualificacoes_model->CRUDIDread($this->_request->getParam("id_pos_graduacao"));
        $this->view->qpd = $qualificacoes_model->CRUDPDread($this->_request->getParam("id_pos_graduacao"));

        $bancas_model = new Application_Models_PgBancaQualificacoes();
        $pg_bancas = $bancas_model->CRUDread();
        $this->view->pg_bancas = $pg_bancas;

        //ComeÃ§a a parte de situacao
        $ingresso = new Application_Models_Ingressos();
        $desligadoDetalhe = new Application_Models_Desligado_Detalhes();

        $result = $ingresso->getIngressosByIDPosGraduacao($this->_request->getParam("id_pos_graduacao"));

        $array = array();
        $i = 0;


        foreach ($result as $row) {
            $flag = false;

            if ($row['passagem_direta'] == 1) {
                $array[$i]['situacao'] = 'Passagem Direta';
                $array[$i]['data'] = ($row['data_passagem_direta'] == '0000-00-00 00:00:00' ? 'N&atilde;o definida' : date("d/m/Y", strtotime($row['data_passagem_direta'])));
                $array[$i]['detalhes'] = 'Sem detalhes';

                $flag = true;
                $i++;
            }
            if ($row['ingresso_direto'] == 1) {
                $array[$i]['situacao'] = 'Ingresso Direto';
                $array[$i]['data'] = ($row['data_passagem_direta'] == '0000-00-00 00:00:00' ? 'N&atilde;o definida' : date("d/m/Y", strtotime($row['data_ingresso_direto'])));
                $array[$i]['detalhes'] = 'Sem detalhes';

                $flag = true;
                $i++;
            }
            if ($row['aluno_desligado'] == 1) {
                $array[$i]['situacao'] = 'Desligado';
                $array[$i]['data'] = ($row['data_passagem_direta'] == '0000-00-00 00:00:00' ? 'N&atilde;o definida' : date("d/m/Y", strtotime($row['data_desligado'])));

                $resultadoDetalhe = $desligadoDetalhe->getDesligadoDetalhes($row['id_desligado_detalhe']);
                $array[$i]['detalhes'] = $resultadoDetalhe[0]['desligado_detalhe'];
                $flag = true;
                $i++;
            }



            $resultTrancamento = $trancamentos->getSituacoesTrancamentoByIDIngresso($row['id_ingresso']);
            if (!is_null($resultTrancamento)) {
                foreach ($resultTrancamento as $rowtran) {
                    $array[$i]['situacao'] = 'Trancamento';
                    $array[$i]['data'] = $rowtran['data_trancamento'];
                    $array[$i]['detalhes'] = $rowtran['detalhes'];

                    $flag = true;
                    $i++;
                }
            }

            if ($flag == false) {
                $i++;
            }
        }
        $this->view->situacao = $array;
    }

    public function postAction() {


        $qualificacoes_model = new Application_Models_PgQualificacoes();
        $bancas_model = new Application_Models_PgBancaQualificacoes();
        $reingresso = new Application_Models_Reingresso();

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

        if ($_REQUEST['ingresso'] == 1) {
            $dataIngresso = $this->toMysqlDateFull($_REQUEST['dataIngressoDireto']);
            $data = $dataIngresso;


            $params = array(
                'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
                'titulo' => $_REQUEST['qid_titulo'],
                'data' => $this->toMysqlDateFull($_REQUEST['qid_data']),
                'sala' => $_REQUEST['qid_sala'],
                'horario' => $_REQUEST['qid_horario'],
                'aprovado' => $_REQUEST['qid_aprovado'],
                'tipo' => '2',
                'observacoes' => $_REQUEST['qid_observacoes']
            );
            if ($_REQUEST['qid'] == '') {
                $id = $qualificacoes_model->CRUDcreate($params);
                $params = array(
                    'id_qualificacao' => $id,
                    'id_professor' => $_REQUEST['qid_professores'],
                    'id_atribuicao' => $_REQUEST['qid_atribuicoes'],
                );
                if ($_REQUEST['qid_professores'] != 1 && $_REQUEST['qid_atribuicoes'] != 1) {
                    $bancas_model->CRUDcreate($params);
                }
            } else {
                $qualificacoes_model->CRUDupdate($params, $_REQUEST['qid']);
                $params = array(
                    'id_qualificacao' => $_REQUEST['qid'],
                    'id_professor' => $_REQUEST['qid_professores'],
                    'id_atribuicao' => $_REQUEST['qid_atribuicoes'],
                );
                if ($_REQUEST['qid_professores'] != 1 && $_REQUEST['qid_atribuicoes'] != 1) {
                    $bancas_model->CRUDcreate($params);
                }
            }
        }


        if (($_REQUEST['id_reingresso'] == '' && $_REQUEST['reingressoPeriodo'] != 1) && ($_REQUEST['reingressoDataU'] != "" || $_REQUEST['reingressoData'] != "")) {
            $strData = explode("/", ($_REQUEST['reingressoData']));
            $reingressoData = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $strData = explode("/", ($_REQUEST['reingressoDataIntegralizacao']));
            $reingressoDataIntegralizacao = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $strData = explode("/", ($_REQUEST['reingressoDataDesligamento']));
            $reingressoDataDesligamento = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            if ($_REQUEST['reingressoAlunoDesligado'] == ''
            )
                $reingressoAlunoDesligado = 0;
            else
                $reingressoAlunoDesligado = 1;
            $array = array(
                'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
                'id_periodo' => $_REQUEST['reingressoPeriodo'],
                'id_linha_pesquisa' => $_REQUEST['reingressoLinhasDePesquisa'],
                'id_motivo_desligamento' => $_REQUEST['reingressoDesligamento'],
                'tipo' => $_REQUEST['reingressoTipo'],
                'data_reingresso' => $reingressoData,
                'data_integralizacao' => $reingressoDataIntegralizacao,
                'aluno_desligado' => $reingressoAlunoDesligado,
                'data_desligamento' => $reingressoDataDesligamento,
                'observacoes' => $_REQUEST['reingressoObservacoes'],
            );
            $reingresso->CRUDcreate($array);
        }

        /*         * PASSAGEM* */
        if ($_REQUEST['passagem'] == 1) {
          // VERIFICANDO SE JA EXISTE REGISTRO
          $conexao = new Application_Models_Inscricoes();
          $idPG = $this->_request->getParam("id_pos_graduacao");

          $sql = "SELECT TRUE AS 'existe' FROM inscricoes i, pos_graduacoes pg, pessoas p 
            WHERE 
            p.id_pessoa = (SELECT id_pessoa FROM pos_graduacoes ppg WHERE ppg.id_pos_graduacao = '$idPG') AND
            p.id_pessoa = pg.id_pessoa AND
            pg.id_pos_graduacao = i.id_pos_graduacao AND
            pg.id_tipo_curso = 5";
          $result = $conexao->getAdapter()->query($sql);
          $rows = $result->fetchAll();

          if(empty($rows)){

            $dataPassagem = $this->toMysqlDateFull($_REQUEST['dataPassagemDireta']);
            $data = $dataPassagem;

            $params = array(
            'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
            'titulo' => $_REQUEST['qpd_titulo'],
            'data' => $this->toMysqlDateFull($_REQUEST['qpd_data']),
            'sala' => $_REQUEST['qpd_sala'],
            'horario' => $_REQUEST['qpd_horario'],
            'aprovado' => $_REQUEST['qpd_aprovado'],
            'tipo' => '1',
            'observacoes' => $_REQUEST['qpd_observacoes']
            );

            //-----//
            $id_pessoa = $idPessoa;
            $idTipoCurso = "5";
            $id_pos_graduacao = $this->_request->getParam('id_pos_graduacao');

            $inscri = new Application_Models_Inscricoes();
            $genericTable = new Zend_Db_Table();

            $id_inscricao = $inscri->getInscricoesByPos($id_pos_graduacao);
            $id_inscricao=$id_inscricao[0]['id_inscricao'];

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

            ///---///
            if ($_REQUEST['qpd'] == '') {
              $id = $qualificacoes_model->CRUDcreate($params);
              $params = array(
                'id_qualificacao' => $id,
                'id_professor' => $_REQUEST['qpd_professores'],
                'id_atribuicao' => $_REQUEST['qpd_atribuicoes'],
              );
              if ($_REQUEST['qpd_professores'] != 1 && $_REQUEST['qpd_atribuicoes'] != 1) {
                $bancas_model->CRUDcreate($params);
              }
            } else {
              $qualificacoes_model->CRUDupdate($params, $_REQUEST['qpd']);
              $params = array(
                'id_qualificacao' => $_REQUEST['qpd'],
                'id_professor' => $_REQUEST['qpd_professores'],
                'id_atribuicao' => $_REQUEST['qpd_atribuicoes'],
              );
              if ($_REQUEST['qpd_professores'] != 1 && $_REQUEST['qpd_atribuicoes'] != 1) {
                $bancas_model->CRUDcreate($params);
              }
            }
          }// FIM DA VERIFICACAO SE JA EXISTE REGISTRO PARA ESSA PESSOA
        }


        if ($_REQUEST['desligado'] == 1) {
            $dataDesligado = $this->toMysqlDateFull($_REQUEST['desligadoData']);
            $motivoDesligamento = (int) $_REQUEST['idDesligadoDetalhe'];
            $observacoesDesligamento = $_REQUEST['desligadoObservacoes'];
        }
        else
            $motivoDesligamento=1;

        if ($_REQUEST['integralizacao'] == '') {

            $paramsIngresso = array
                (
                'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
                'id_periodo' => ((int) $_REQUEST['id_periodo'] ? (int) $_REQUEST['id_periodo'] : 1),
                'id_linha_de_pesquisa' => ((int) $_REQUEST['id_linha_de_pesquisa'] ? (int) $_REQUEST['id_linha_de_pesquisa'] : 1),
                'data_ingresso' => $this->toMysqlDate($_REQUEST['data']),
                'RA' => ($_REQUEST['ra'] ? $_REQUEST['ra'] : ''),
                'passagem_direta' => ((int) $_REQUEST['passagem'] == 1 ? 1 : 0),
                'data_passagem_direta' => $dataPassagem,
                'ingresso_direto' => ((int) $_REQUEST['ingresso'] == 1 ? 1 : 0),
                'data_ingresso_direto' => $dataIngresso,
                'aluno_desligado' => ((int) $_REQUEST['desligado'] == 1 ? 1 : 0),
                'id_desligado_detalhe' => $motivoDesligamento,
                'data_desligado' => $dataDesligado,
                'observacoes_desligado' => $observacoesDesligamento,
                'id_banca_de_defesa' => 1
            );
        } else {


            if ((int) $data == 0) {
                $data = '01/' . $_REQUEST['integralizacao'];
                $data = $this->toMysqlDateFull($data);
            }


            $paramsIngresso = array
                (
                'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
                'id_periodo' => ((int) $_REQUEST['id_periodo'] ? (int) $_REQUEST['id_periodo'] : 1),
                'id_linha_de_pesquisa' => ((int) $_REQUEST['id_linha_de_pesquisa'] ? (int) $_REQUEST['id_linha_de_pesquisa'] : 1),
                'data_ingresso' => $this->toMysqlDate($_REQUEST['data']),
                'data_integralizacao' => $data,
                'RA' => ($_REQUEST['ra'] ? $_REQUEST['ra'] : ''),
                'passagem_direta' => ((int) $_REQUEST['passagem'] == 1 ? 1 : 0),
                'data_passagem_direta' => $dataPassagem,
                'ingresso_direto' => ((int) $_REQUEST['ingresso'] == 1 ? 1 : 0),
                'data_ingresso_direto' => $dataIngresso,
                'aluno_desligado' => ((int) $_REQUEST['desligado'] == 1 ? 1 : 0),
                'id_desligado_detalhe' => $motivoDesligamento,
                'data_desligado' => $dataDesligado,
                'observacoes_desligado' => $observacoesDesligamento,
                'id_banca_de_defesa' => 1
            );
        }

        $ingresso = new Application_Models_Ingressos();
        if ((int) $_REQUEST['idIngresso'] == 0) {
            $idIngresso = $ingresso->addIngressos($paramsIngresso);
        } else {
            $idIngresso = $_REQUEST['idIngresso'];
            $ingresso->updateIngressos($paramsIngresso, $idIngresso);
        }

        if ((int) $_REQUEST['id_professor_orientador'] > 1) {

            $orientadores = new Application_Models_Orientadores();
            $paramsOrientadores = array
                (
                'data' => $this->toMysqlDateFull($_REQUEST['orientadorData']),
                'justificativa' => ($_REQUEST['justificativa'] ? $_REQUEST['justificativa'] : ''),
                'id_area_de_concentracao' => ((int) $_REQUEST['id_area_de_concentracao'] ? $_REQUEST['id_area_de_concentracao'] : 1),
                'id_ingresso' => (int) $idIngresso,
                'id_professor' => ((int) $_REQUEST['id_professor_orientador'] ? (int) $_REQUEST['id_professor_orientador'] : 1),
                'atual' => ($_REQUEST['atual'] ? $_REQUEST['atual'] : 0)
            );

            if ($_REQUEST['atual'] == 1) {
                $orientadores->removeAtuais($idIngresso);
                //echo "ENTROU";
                //var_dump($idIngresso);
               // exit;
            }
            
            $orientadores->addOrientadores($paramsOrientadores);
        }

        if ((int) $_REQUEST['id_coorientador_prof'] > 1) {
            $coorientadores = new Application_Models_Coorientadores();
            $paramsOrientadores = array
                (
                'data' => $this->toMysqlDateFull($_REQUEST['coorientadorData']),
                'justificativa' => ($_REQUEST['justificativa'] ? $_REQUEST['justificativa'] : ''),
                'id_area_de_concentracao' => (int) $_REQUEST['id_area_de_concentracao_coorientador'],
                'id_ingresso' => (int) $idIngresso,
                'id_orientador' => ((int) $_REQUEST['id_coorientador_prof'] ? (int) $_REQUEST['id_coorientador_prof'] : 1),
                'atual' => ($_REQUEST['atual_co'] ? $_REQUEST['atual_co'] : 0)
            );
			//exit;
			
            if ($_REQUEST['atual_co'] == 1) {
            	
            	//var_dump($idIngresso);
            	//exit;
                $coorientadores->removeAtuais($idIngresso);
            }
            $coorientadores->addOrientadores($paramsOrientadores);
        }


        if (strlen($_REQUEST['periodoInicio']) != 0) {


            $paramsRelatorios = array(
                'id_ingresso' => (int) $idIngresso, //$_REQUEST[''],
                'id_professor' => ((int) $_REQUEST['id_parecerista'] ? (int) $_REQUEST['id_parecerista'] : 1),
                'aluno_data_previsto' => $this->toMysqlDateFull($_REQUEST['dataPrevisto']),
                'aluno_data_inicio' => $this->toMysqlDate($_REQUEST['periodoInicio']),
                'aluno_data_entrega' => $this->toMysqlDateFull($_REQUEST['dataAlunoEntrega']),
                'aluno_data_fim' => $this->toMysqlDate($_REQUEST['periodoFim']),
                'parecerista_envio' => $this->toMysqlDateFull($_REQUEST['dataPareceristaEnvio']),
                'parecerista_termino_envio' => $this->toMysqlDateFull($_REQUEST['dataPareceristaTerminoEnvio']),
                'parecerista_data_devolucao' => $this->toMysqlDateFull($_REQUEST['dataPareceristaDevolucao']),
                'parecerista_data_inicio' => $this->toMysqlDateFull($_REQUEST['dataPareceristaInicio']),
                'parecerista_data_prevista' => $this->toMysqlDateFull($_REQUEST['dataPareceristaTermino']),
                'parecerista_data_entrega' => $this->toMysqlDateFull($_REQUEST['dataPareceristaEntrega']),
                'aprovado' => (int) $_REQUEST['parecerAprovado'],
                'parecer' => $_REQUEST['parecer'],//str_replace(":", '\:', $_REQUEST['parecer'] ? $_REQUEST['parecer'] : ''),
                'data_devolucao_prevista' => $this->toMysqlDateFull($_REQUEST['dataDevolucaoPrevista']),
                'data_devolucao_entrega' => $this->toMysqlDateFull($_REQUEST['dataDevolucaoEntrega'])
            );
            $relatorio = new Application_Models_Relatorios();

            //var_dump($paramsRelatorios);
            // exit;

            $relatorio->addRelatorio($paramsRelatorios);
        }

        if ((int) $_REQUEST['areaId'] > '1') {
            $paramsArea = array
                (
                'id_pos' => $this->_request->getParam("id_pos_graduacao"),
                'id_area_de_concentracao' => $_REQUEST['areaId'],
                'data_de_mudanca' => $this->toMysqlDateFull($_REQUEST['areaData']),
                'observacoes' => $_REQUEST['areaObservacoes']
            );
            $pos_area_de_concentracao = new Application_Models_PosGraduacaoArea();
            $pos_area_de_concentracao->CRUDcreate($paramsArea);
        }

        if ((int) $_REQUEST['id_tipo_de_trancamento'] > '1') {
            $trancamentos = new Application_Models_Trancamentos();
            $paramsTrancamento = array
                (
                'id_tipo_trancamento' => (int) $_REQUEST['id_tipo_de_trancamento'],
                'id_ingresso' => (int) $idIngresso,
                'id_periodo' => (int) $_REQUEST['id_trancamento_periodo'],
                'meses' => (int) $_REQUEST['trancamentoDuracao'],
                'observacoes' => ($_REQUEST['trancamentoObservacoes'] ? $_REQUEST['trancamentoObservacoes'] : ''),
                'data_trancamento' => $this->toMysqlDateFull($_REQUEST['início_trancamento'])
            );
            $trancamentos->addTrancamento($paramsTrancamento);
            $dataIntegralizacao = new Application_Models_Ingressos();
            $result = $dataIntegralizacao->getIngressosByIDPosGraduacao($this->_request->getParam("id_pos_graduacao"));
            $meses = (int) $_REQUEST['trancamentoDuracao'];
            $dataAtual = $result[0]['data_integralizacao'];
            $data = explode("-", $dataAtual);
            $newData = date("Y-m-d", mktime(0, 0, 0, $data[1] + $meses, $data[2], $data[0]));
            $params = array('data_integralizacao' => $newData);
            $dataIntegralizacao->updateIngressos($params, $idIngresso);
        }


        /* Passagem direta pra doutorado =>
         * Passa todos os dados de inscricao de mestrado para doutorado */
        $idTipoCurso = $this->_request->getParam("tipo");

        if (((int) $_REQUEST['passagem'] == 1 || (int) $_REQUEST['ingresso'] == 1) && $idTipoCurso == '3') {
          if(empty($rows)){
            if ($this->_request->getParam("id_pos_graduacao")) {
                $clsInscricao = new Application_Models_Inscricoes;
                $arrInscricoesF = $clsInscricao->getInscricoesByPos($this->_request->getParam("id_pos_graduacao"), "inscricao_aceita='1' AND deletado='0'");

                foreach ($arrInscricoesF as $arrInscricoes) {
                    if ($arrInscricoes != null) {
                        unset($arrInscricoes['id_inscricao']);
                        unset($arrInscricoes['id_pessoa']);
                        $clsPos = new Application_Models_Pos_Graduacoes();
                        $idPos = $clsPos->atualizaPosGraduacao($idPessoa, 5);
                        $arrInscricoes['id_pos_graduacao'] = $idPos;
                        $clsInscricao->addInscricoes($arrInscricoes);
                    }
                }
            }
          }
        }


        $this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");

        $this->view->alert = $result;
    }

    public function updaterelatorioAction() {
        $paramsRelatorios = array(
            'id_ingresso' => (int) $this->_request->getParam("id_ingresso"),
            'id_professor' => ((int) $_REQUEST['professorU'] ? (int) $_REQUEST['professorU'] : 1),
            'aluno_data_previsto' => $this->toMysqlDateFull($_REQUEST['dataPrevisto']),
            'aluno_data_inicio' => $this->toMysqlDate($_REQUEST['periodoInicio']),
            'aluno_data_entrega' => $this->toMysqlDateFull($_REQUEST['dataAlunoEntrega']),
            'aluno_data_fim' => $this->toMysqlDate($_REQUEST['periodoFim']),
            'parecerista_envio' => $this->toMysqlDateFull($_REQUEST['dataPareceristaEnvio']),
            'parecerista_termino_envio' => $this->toMysqlDateFull($_REQUEST['dataPareceristaTerminoEnvio']),
            'parecerista_data_devolucao' => $this->toMysqlDateFull($_REQUEST['dataPareceristaDevolucaos']),
            'parecerista_data_inicio' => $this->toMysqlDateFull($_REQUEST['pareceristadatainicio']),
            'parecerista_data_prevista' => $this->toMysqlDateFull($_REQUEST['pareceristadataprevisto']),
            'parecerista_data_entrega' => $this->toMysqlDateFull($_REQUEST['pareceristadataentrega']),
            'aprovado' => (int) ($_REQUEST['aprovado'] ? $_REQUEST['aprovado'] : ''),
            'parecer' => $_REQUEST['parecer'],//str_replace(":", '\:', $_REQUEST['parecer']),
            'data_devolucao_prevista' => $this->toMysqlDateFull($_REQUEST['datadevolucaoprevista']),
            'data_devolucao_entrega' => $this->toMysqlDateFull($_REQUEST['datadevolucaoentrega'])
        );

        $relatorio = new Application_Models_Relatorios();

        $relatorio->updateRelatorio($paramsRelatorios, (int) $_REQUEST['id']);

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

    public function updateorientadoresAction() {
        $paramsOrientadores = array(
            'id_professor' => ((int) $_REQUEST['professorU'] ? (int) $_REQUEST['professorU'] : 1),
            'justificativa' => $_REQUEST['justificativa'],
            'atual' => (int) ($_REQUEST['atual'] ? $_REQUEST['atual'] : ''),
            'id_ingresso' => (int) $this->_request->getParam("id_ingresso"),
            'data' => $this->toMysqlDateFull($_REQUEST['data']),
            'id_area_de_concentracao' => ((int) $_REQUEST['areaConcentracaoU'] ? (int) $_REQUEST['areaConcentracaoU'] : 1),
        );

        $orientadores = new Application_Models_Orientadores();
	$idIngresso=$this->_request->getParam("id_ingresso");
        if ($_REQUEST['atual'] == 1) {
        	
        	
        	
            $orientadores->removeAtuais($idIngresso);
           // exit;
        }

        $orientadores->updateOrientadores($paramsOrientadores, (int) $this->_request->getParam("id_orientador"));

        $idTipoCurso = $this->_request->getParam("tipo");
        //echo '/posgraduacao/ingressos/index/id_pos_graduacao/'.$this->_request->getParam("id_pos_graduacao").'/tipo/'.$idTipoCurso;
        //exit;
        $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

    public function updatecoorientadoresAction() {

        $coorientadores = new Application_Models_Coorientadores();

        $paramsOrientadores = array(
            'id_orientador' => ((int) $_REQUEST['professorU'] ? (int) $_REQUEST['professorU'] : 1),
            'justificativa' => $_REQUEST['justificativa'],
            'atual' => (int) ($_REQUEST['atual'] ? $_REQUEST['atual'] : ''),
            'id_ingresso' => (int) $this->_request->getParam("id_ingresso"),
            'data' => $this->toMysqlDateFull($_REQUEST['data']),
            'id_area_de_concentracao' => ((int) $_REQUEST['areaConcentracaoU'] ? (int) $_REQUEST['areaConcentracaoU'] : 1),
        );
			$idIngresso=$this->_request->getParam("id_ingresso");
        if ($_REQUEST['atual'] == 1) {
	
        	$coorientadores->removeAtuais($idIngresso);
        }

        $coorientadores->updateOrientadores($paramsOrientadores, (int) $this->_request->getParam("id_coorientador"));
        //  exit;
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

    public function updateareaAction() {
        $paramsArea = array(
            'observacoes' => $_REQUEST['observacoes'],
            'data_de_mudanca' => $this->toMysqlDateFull($_REQUEST['data']),
            'id_area_de_concentracao' => ((int) $_REQUEST['areaConcentracao'] ? (int) $_REQUEST['areaConcentracao'] : 1),
        );

        $area = new Application_Models_PosGraduacaoArea();

        $area->CRUDupdate($paramsArea, (int) $_REQUEST['id_pos_area_de_concentracao']);

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $_REQUEST['id_pos_graduacao'] . '/tipo/' . $idTipoCurso);
    }

    public function updatereingressoAction() {
        if ($_REQUEST['id'] != '') {
            $reingresso = new Application_Models_Reingresso();

            $strData = explode("/", ($_REQUEST['reingressoDataU']));
            $reingressoData = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $strData = explode("/", ($_REQUEST['reingressoDataIntegralizacaoU']));
            $reingressoDataIntegralizacao = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $strData = explode("/", ($_REQUEST['reingressoDataDesligamentoU']));
            $reingressoDataDesligamento = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            if ($_REQUEST['reingressoAlunoDesligadoU'] == ''
            )
                $reingressoAlunoDesligado = 0;
            else
                $reingressoAlunoDesligado = 1;
            $array = array(
                'id_periodo' => $_REQUEST['reingressoPeriodoU'],
                'id_linha_pesquisa' => $_REQUEST['reingressoLinhasDePesquisaU'],
                'id_motivo_desligamento' => $_REQUEST['reingressoDesligamentoU'],
                'tipo' => $_REQUEST['reingressoTipoU'],
                'data_reingresso' => $reingressoData,
                'data_integralizacao' => $reingressoDataIntegralizacao,
                'aluno_desligado' => $reingressoAlunoDesligado,
                'data_desligamento' => $reingressoDataDesligamento,
                'observacoes' => $_REQUEST['reingressoObservacoesU'],
            );
            $reingresso->CRUDupdate($array, $_REQUEST['id']);
        }

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $_REQUEST['id_pos_graduacao'] . '/tipo/' . $idTipoCurso);
    }

    public function updatetrancamentoAction() {
        $paramsTrancamento = array
            (
            //'id_pos_graduacao'=>(int)$_REQUEST['id_pos_graduacao'],
            'id_tipo_trancamento' => (int) $_REQUEST['tipo_trancamentoU'],
            'meses' => (int) $_REQUEST['meses'],
            'data_trancamento' => $this->toMysqlDateFull($_REQUEST['data_trancamento']),
            'observacoes' => $_REQUEST['observacoes'],
        );
        $getTrancamento = new Application_Models_Trancamentos();
        $getTrancamento->getTrancamentoByID((int) $_REQUEST['id']);

        $trancamentos = new Application_Models_Trancamentos();
        $trancamentos->updateTrancamento($paramsTrancamento, (int) $_REQUEST['id']);

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

    public function toMysqlDate($string) {
        $a = explode('/', $string);
        $b = $a[1] . '-' . $a[0] . '-01 00:00:00';
        return $b;
    }

    public function toMysqlDateFull($string) {
        $a = explode('/', $string);
        $b = $a[2] . '-' . $a[1] . '-' . $a[0] . ' 00:00:00';
        return $b;
    }

    public function deleteAction() {
        //die(__FUNCTION__);
        if ($this->_request->getParam("id_orientador") != '') {

            $dataOrientador = new Application_Models_Orientadores();
            $dataOrientador->deleteOrientador($this->_request->getParam("id_orientador"));

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
        }

        if ($this->_request->getParam("id_coorientador") != '') {

            $dataCoorientador = new Application_Models_Coorientadores();
            $dataCoorientador->deleteOrientador($this->_request->getParam("id_coorientador"));

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
        }

        if ($this->_request->getParam("id_trancamento") != '') {

            $model_t = new Application_Models_Trancamentos();
            $model_i = new Application_Models_Ingressos();

            $data_t = $model_t->getTrancamentoByID($this->_request->getParam('id_trancamento'));
            $meses = $data_t[0]['meses'];
            $id_ingresso = $data_t[0]['id_ingresso'];

            $data_i = $model_i->getIngresso($id_ingresso);
            $date = $data_i['data_integralizacao'];
            $date = explode("-", $date);
            $newDate = date("Y-m-d", mktime(0, 0, 0, $date[1] - $meses, $date[2], $date[0]));
            $params = array('data_integralizacao' => $newDate);

            $model_i->updateIngressos($params, $id_ingresso);
            $model_t->deleteTrancamento($this->_request->getParam("id_trancamento"));

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
        }
        if ($this->_request->getParam("id_pos_area_de_concentracao") != '') {

            $dataPosArea = new Application_Models_PosGraduacaoArea();
            $dataPosArea->CRUDdelete($this->_request->getParam("id_pos_area_de_concentracao"));
            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
        }

        if ($this->_request->getParam("id_relatorio") != '') {

            $dataRelatorio = new Application_Models_Relatorios();
            $dataRelatorio->deleteRelatorio($this->_request->getParam("id_relatorio"));

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/ingressos/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
        }
        if ($this->_request->getParam("id_banca_qualificacao") != '') {
            $id = $this->_request->getParam("id_banca_qualificacao");
            $bancas_model = new Application_Models_PgBancaQualificacoes();
            $bancas_model->CRUDdelete($id);
        }
        if ($this->_request->getParam("id_reingresso") != '') {
            $id = $this->_request->getParam("id_reingresso");
            $reingresso = new Application_Models_Reingresso();
            $reingresso->CRUDdelete($id);
        }
    }

}