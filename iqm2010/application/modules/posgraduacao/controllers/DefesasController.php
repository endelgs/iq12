<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/TiposProjetos.php');
require_once(APPLICATION_PATH . '/models/ProjetosDocentes.php');
require_once(APPLICATION_PATH . '/models/Defesa.php');
require_once(APPLICATION_PATH . '/models/DefesaBanca.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/PgQualificacoes.php');
require_once(APPLICATION_PATH . '/models/PgBancaQualificacoes.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/../library/componentes/Mascara.php');

class Posgraduacao_DefesasController extends Zend_Controller_Action {

    public function init() {
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function indexAction() {

        $id_pos_graduacao = $this->_request->getParam("id_pos_graduacao");
        $this->view->id_pos_graduacao = $id_pos_graduacao;

        $idTipoCurso = $this->_request->getParam("tipo");
        $this->view->tipoPosGraduacao = $idTipoCurso;
        $Mask = new mascara();
        $pChecker = new ParamChecker();
        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);


        $defesa_model = new Application_Models_Defesa();
        $pg_defesa = $defesa_model->CRUDread($id_pos_graduacao);
        $tipoProj = new Application_Models_ProjetosDocente();
        $array = $tipoProj->allByPos($id_pos_graduacao);
        $result = array('0' => '');
        foreach ($array as $tipo) {

            $result[$tipo['id_projeto']] = $tipo['titulo'];
        }
        $this->view->tipoProj = $result;
        $result = array();
        forEach ($pg_defesa as $index => $p) {
            $pg_defesa[$index]['data_defesa'] = $Mask->datetimeSQLToMascaraData($p['data_defesa']);
            $pg_defesa[$index]['con_data_entrega'] = $Mask->datetimeSQLToMascaraData($p['con_data_entrega']);


            $array = array();
            $array = $tipoProj->allByPos($id_pos_graduacao, $p['id_tipo_projeto']);
            $result[$index][0] = '';
            foreach ($array as $tipo) {

                $result[$index][$tipo['id_projeto']] = $tipo['titulo'];
            }
        }

        $this->view->tipoProjEdicao = $result;

        $this->view->pg_defesa = $pg_defesa;

        $bancas_model = new Application_Models_DefesaBanca();
        $pg_bancas = $bancas_model->viewNomeAtribuicao();
        $this->view->pg_bancas = $pg_bancas;



        $general = new Models_General();
        $this->view->atribuicoes = $general->listView('v_atribuicoes', array('id', 'atribuicao'));

        $this->view->professores = $general->listView('v_professores', array('id', 'nome'));


//        if ($alert == 1
//
//            )$this->view->alert = 'Dados gravados com sucesso!';
    }

    public function deletebancaAction() {

        echo 'Oi';
        print_r($id_defesa_banca);
        exit;

        $id_defesa_banca = $this->_request->getParam("id");
        $bancas_model = new Application_Models_DefesaBanca();
        $bancas_model->CRUDQLdelete($id_defesa_banca);
    }

    public function postAction() {

//		var_dump($_REQUEST);
//		echo '<br>';echo '<br>';echo '<br>';


        $defesa_model = new Application_Models_Defesa();
        $bancas_model = new Application_Models_DefesaBanca();

        $Mask = new mascara();

        $count_u = $_REQUEST['count_u'];
        foreach ($count_u as $x => $countt) {
            if ($_REQUEST['deletado_u' . $x] == 0) {

                $titulo = str_replace('"', '', $_REQUEST['u_titulo' . $x]);


                $params = array(
                    'titulo' => $titulo,
                    'data_defesa' => $Mask->MascaraDataTodatetimeSQL($_REQUEST['u_data' . $x]),
                    'sala' => $_REQUEST['u_sala' . $x],
                    'horario' => $_REQUEST['u_horario' . $x],
                    'aprovado' => $_REQUEST['u_aprovado' . $x],
                    'observacoes' => $_REQUEST['u_observacoes' . $x],
                    'id_tipo_projeto' => $_REQUEST['u_tipoproj' . $x],
                    'con_aprovado' => $_REQUEST['u_con_aprovado' . $x],
                    'con_data_entrega' => $Mask->MascaraDataTodatetimeSQL($_REQUEST['u_con_data' . $x]),
                    'resumo_dissertacao' => $_REQUEST['u_resumo' . $x],
                    'palavras_chaves' => $_REQUEST['u_palavras' . $x]
                );


                $defesa_model->CRUDupdate($params, $_REQUEST['u_id_defesa' . $x]);



                $params = array(
                    'id_defesa' => $_REQUEST['u_id_defesa' . $x],
                    'id_professor' => $_REQUEST['u_professores' . $x],
                    'id_atribuicao' => $_REQUEST['u_atribuicoes' . $x],
                );
//				var_dump($params);
//				exit;
                $existe = $bancas_model->CRUDreadOne($params);
                //var_dump($existe);
                if ($existe == '') {
                    if ($_REQUEST['u_professores' . $x] != 1 && $_REQUEST['u_atribuicoes' . $x] != 1) {
                        $bancas_model->CRUDcreate($params);
                    }
                }
            } else {


                $bancas_model->CRUDQLdelete($_REQUEST['u_id_defesa' . $x]);
                $defesa_model->CRUDdelete($_REQUEST['u_id_defesa' . $x]);
            }
        }

        $cont = $_REQUEST['count_i'];
        foreach ($cont as $x => $count) {


            $titulo = str_replace('"', '', $_REQUEST['i_titulo' . $x]);

            $params = array(
                'titulo' => $titulo,
                'data_defesa' => $Mask->MascaraDataTodatetimeSQL($_REQUEST['i_data' . $x]),
                'sala' => $_REQUEST['i_sala' . $x],
                'horario' => $_REQUEST['i_horario' . $x],
                'aprovado' => $_REQUEST['i_aprovado' . $x],
                'observacoes' => $_REQUEST['i_observacoes' . $x],
                'id_tipo_projeto' => $_REQUEST['i_tipoproj' . $x],
                'con_aprovado' => $_REQUEST['i_con_aprovado' . $x],
                'con_data_entrega' => $Mask->MascaraDataTodatetimeSQL($_REQUEST['i_con_data' . $x]),
                'resumo_dissertacao' => $_REQUEST['i_resumo' . $x],
                'palavras_chaves' => $_REQUEST['i_palavras' . $x],
                'id_pos_graduacao' => $_REQUEST['id_pos_graduacao']
            );
            $id = $defesa_model->CRUDcreate($params);

            $params = array(
                'id_defesa' => $id,
                'id_professor' => $_REQUEST['i_professores' . $x],
                'id_atribuicao' => $_REQUEST['i_atribuicoes' . $x],
            );
            if ($_REQUEST['i_professores' . $x] != 1 && $_REQUEST['i_atribuicoes' . $x] != 1) {
                $bancas_model->CRUDcreate($params);
            }
        }
        $this->view->id_pos_graduacao = $_REQUEST['id_pos_graduacao'];
    }

    public function deleteAction() {
        
        $idPessoa = $this->_request->getParam("id_pessoa");
        $idTipoCurso = $this->_request->getParam("tipo");
        $idPosGraducao = $this->_request->getParam("id_pos_graduacao");
        $id = $this->_request->getParam("id");

        $bancas_model = new Application_Models_DefesaBanca();
        $bancas_model->CRUDdelete($id);

        $this->_redirect('/posgraduacao/defesas/index/id_pessoa/' . $idPessoa . '/id_pos_graduacao/' . $idPosGraducao . '/tipo/' . $idTipoCurso . '?alert=Excluído com sucesso.');
    }

    public function toMysqlDateFull($string) {
        $a = explode('/', $string);
        $b = $a[2] . '-' . $a[1] . '-' . $a[0] . ' 00:00:00';
        return $b;
    }

}