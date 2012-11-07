<?php
// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/exames.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/Ingressos.php');
require_once(APPLICATION_PATH . '/models/inscricoes.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/models/GerenciarGenerico.php');

class Posgraduacao_ExamesController extends Zend_Controller_Action {

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
            $arrMestrado = $clsPosGraduacao->getMestradoByPessoa($idPessoa);
            $id_mestrado = $arrMestrado['id_pos_graduacao'];
            $this->view->idMestrado = $id_mestrado;

            $clsIngresso = new Application_Models_Ingressos();
            $arrIngresso = $clsIngresso->getIngressosByIDPosGraduacao($id_mestrado);
            $passagemDireta = $arrIngresso[0]['passagem_direta'];
            $ingressoDireto = $arrIngresso[0]['ingresso_direto'];


            if ((int) $passagemDireta == 1 || (int) $ingressoDireto == 1) {
                $this->_redirect('/posgraduacao/ingressos/index/id_pessoa/' . $id_pessoa . '/id_pos_graduacao/' . $idPosGraduacao . '/tipo/5');
            }
        }


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
        $idPessoa = $this->_request->getParam("id_pessoa");


        $pChecker = new ParamChecker();
        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);

        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));
        $this->view->cidadesProva = $general->listView('v_cidade_provas', array('id', 'cidade'));

        $tabela = new Application_Models_GerenciarGenerico();
        $tabela->_primary('id_exame');
        $tabela->_name('v_nota_maxima_inscritos_ano_periodo');

        if ($this->_request->getParam("id_pos_graduacao") != "") {

            $objExame = new Application_Models_Exames();
            $exames = $objExame->getExames($this->_request->getParam("id_pos_graduacao"));

            $arrExame = array();
            foreach ($exames as $index => $auxExame) {
                $arrExame[$index] = $auxExame;
                if (strtolower($tipoCurso) == 'doutorado') {
                    $arrNotaMax = $tabela->CRUDreadByPrimary($auxExame['id_exame']);
                    //print_r($arrNotaMax);die();
//                    var_dump($arrNotaMax);
                    $arrExame[$index]['nota1'] = $arrNotaMax[0]['nota1'];
                    $arrExame[$index]['nota2'] = $arrNotaMax[0]['nota2'];
                }
            }
            $this->view->exame = $arrExame;

            foreach ($arrExame as $i) {
                $notas[] = $objExame->getNotasExame($i['id_exame']);
            }
            $this->view->notas = $notas;
        }
    }

    public function postAction() {
        $exames = new Application_Models_Exames();
        $exameQtd = count($_REQUEST['id_exame']);

        $num = 0;
        $n_exame = '';
        $n_inscricao = '';
        $matriculado_ok = false;
        for ($x = 0; $x < $exameQtd; $x++) {
            if (is_null($_REQUEST['data'][$x]))
                $data_exame = 0;
            else {
                $strData = explode("/", ($_REQUEST['data'][$x]));
                $data_exame = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);
            }
            if ($_REQUEST['aprovado' . $x] == 1

                )$aprovado = 1;
            else
                $aprovado=0;
            if ($_REQUEST['prova' . $x] == 1

                )$prova = 1;
            else
                $prova=0;
            if ($_REQUEST['matriculado' . $x] == 1

                )$matriculado = 1;
            else
                $matriculado=0;
            $data = array(
                'data_exame' => $data_exame,
                'id_cidade_prova' => ($_REQUEST['cidade'][$x]),
                'numero_questoes' => ($_REQUEST['numero_questoes'][$x]),
                'media' => ($_REQUEST['mediaaluno'][$x]),
                'media_turma' => ($_REQUEST['mediaturma'][$x]),
                'nota_corte' => ($_REQUEST['notacorte'][$x]),
                'classificacao' => ($_REQUEST['classificacao'][$x]),
                'classificacao_quantidade' => ($_REQUEST['classificacao_quantidade'][$x]),
                'aprovado' => $aprovado,
                'matriculado' => $matriculado,
                'fez_prova' => $prova,
                'nota_curriculo_1' => ($_REQUEST['nota1'][$x]),
            );
            if ($data_pos <= $data_exame && $matriculado == 1) {
                $data_pos = $data_exame;
                $n_exame = $_REQUEST['id_exame'][$x];
                $n_inscricao = $_REQUEST['id_inscricao'][$x];
                $matriculado_ok = true;
            }

            $exameId = $_REQUEST['id_exame'][$x];
            $exames->updateExame($data, $exameId);
            for ($y = 1; $y <= $_REQUEST['numero_questoes'][$x]; $y++) {
                $data = array(
                    'id_exame' => $exameId,
                    'questao' => $_REQUEST['questao'][$num],
                    'nota' => $_REQUEST['nota'][$num]
                );
                $exames->addNotasExames($data);
                $num++;
            }
        }
        if ($matriculado_ok) {

            $data = array(
                'n_inscricao' => $n_inscricao,
                'n_exame' => $n_exame
            );
            $exames->updatePosGraduacaoExame($data, $_REQUEST['id_pos_graduacao']);


            //ingresso
            $paramsIngresso = array
                (
                'id_pos_graduacao' => $_REQUEST['id_pos_graduacao'],
                'id_periodo' => "1",
                'id_linha_de_pesquisa' => "1",
                'data_ingresso' => "0",
                'data_integralizacao' => "0",
                'RA' => "",
                'passagem_direta' => "0",
                'data_passagem_direta' => "0",
                'ingresso_direto' => "0",
                'data_ingresso_direto' => "0",
                'aluno_desligado' => "0",
                'id_desligado_detalhe' => "1",
                'data_desligado' => "0",
                'observacoes_desligado' => "",
                'id_banca_de_defesa' => 1
            );


            $ingresso = new Application_Models_Ingressos();
            $selectIngresso = $ingresso->getIngressosByIDPosGraduacao($_REQUEST['id_pos_graduacao']);

            if ((int) $selectIngresso[0]['id_ingresso'] == 0) {
                $idIngresso = $ingresso->addIngressos($paramsIngresso);
            } else {
                $idIngresso = $selectIngresso[0]['id_ingresso'];
                $paramsIngresso = $selectIngresso[0];
                $ingresso->updateIngressos($paramsIngresso, $idIngresso);
            }

            //Recupera professor pela inscricao
            $inscricao = new Application_Models_Inscricoes();
            $resultInscricao = $inscricao->getInscricoesById($n_inscricao);

            $professor = $resultInscricao[0]['id_professor'];
            $areaDeConcentracao = $resultInscricao[0]['id_area_de_concentracao'];

            //professor
            $s_orientadores = new Application_Models_Orientadores();
            $resultOrientador = $s_orientadores->getByIngresso($idIngresso);

            $paramsOrientadores = array
                (
                'id_professor' => $professor,
                'id_area_de_concentracao' => $areaDeConcentracao,
                'id_ingresso' => $idIngresso,
                'data' => "0",
                'justificativa' => 'Orientador Previsto',
                'atual' => 1,
            );


            $orientadores = new Application_Models_Orientadores();

            if ((int) $resultOrientador[0]['id_ingresso'] == 0) {
                $orientadores->addOrientadores($paramsOrientadores);
            } else {
                $idProfessor = $resultOrientador[0]['id_ingresso'];
                $orientadores->updateOrientador($paramsOrientadores, $idProfessor);
            }
        }
        if (!$matriculado_ok) {
            $data = array(
                'n_inscricao' => $n_inscricao,
                'n_exame' => '0'
            );
            $exames->updatePosGraduacaoExame($data, $_REQUEST['id_pos_graduacao']);
        }

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/exames/index/id_pos_graduacao/' . $_REQUEST['id_pos_graduacao'] . '/tipo/' . $idTipoCurso . '?alert=Dados gravados com sucesso.');
    }

}