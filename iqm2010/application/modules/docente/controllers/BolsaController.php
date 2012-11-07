<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Docente.php');
require_once(APPLICATION_PATH . '/models/BolsaProdutividade.php');

class Docente_BolsaController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $id_pessoa = $this->_request->getParam("id_pessoa");
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function indexAction() {
        $general = new Models_General();

        $this->view->agencias = $general->listView('v_agencias', array('id', 'agencia'));
        $this->view->niveis = $general->listView('v_nivelbolsa', array('id', 'nivelbolsa'));

        $id_pessoa = $this->_request->getParam("id_pessoa");

        $docente = new Application_Models_Docente();
        $dados_docente = $docente->getByIdPessoa($id_pessoa);

        if ($dados_docente != '') {
            $bolsa_produtividade = new Application_Models_BolsaProdutividade();
            $dados_bolsa = $bolsa_produtividade->CRUDreadByDocente($dados_docente['id_professor']);

            $this->view->bolsa_grid = $dados_bolsa;
        }
    }

    public function postAction() {
        $id_pessoa = $this->_request->getParam("id_pessoa");
        $docente = new Application_Models_Docente();
        $dados_docente = $docente->getByIdPessoa($id_pessoa);

        if ($dados_docente != '') {
            $strData = explode("/", ($_REQUEST['datainicio']));
            $data_aquisicao = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $strData = explode("/", ($_REQUEST['dataFim']));
            $data_fim = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $array = array(
                'id_agencia' => $_REQUEST['agencia'],
                'id_nivel' => $_REQUEST['nivelbolsa'],
                'data_aquisicao' => $data_aquisicao,
                'id_docente' => $dados_docente['id_professor'],
                'data_termino' => $data_fim,
            );

            $bolsa_produtividade = new Application_Models_BolsaProdutividade();
            $bolsa_produtividade->CRUDcreate($array);
            $this->_redirect('/docente/bolsa/index/id_pessoa/' . $id_pessoa);
        }
    }

    public function deleteAction() {
        $id = $this->_request->getParam("id");
        $id_pessoa = $this->_request->getParam("id_pessoa");
        if ($id != '') {
            $bolsa_produtividade = new Application_Models_BolsaProdutividade();
            $bolsa_produtividade->CRUDdelete($id);
        }
        $this->_redirect('/docente/bolsa/index/id_pessoa/' . $id_pessoa);
    }

    public function updateAction() {
        $id = $this->_request->getParam("id");
        $id_pessoa = $this->_request->getParam("id_pessoa");
        if ($id != '') {
            $strData = explode("/", ($_REQUEST['data_termino']));
            $data_termino = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $strData = explode("/", ($_REQUEST['data_aquisicao']));
            $data_aquisicao = ($strData[2] . "-" . $strData[1] . "-" . $strData[0]);

            $bolsa_produtividade = new Application_Models_BolsaProdutividade();
            $array = array(
                'id_agencia' => $_REQUEST['agenciaU'],
                'id_nivel' => $_REQUEST['nivelU'],
                'data_aquisicao' => $data_aquisicao,
                'data_termino' => $data_termino
            );
            $bolsa_produtividade->CRUDupdate($array, $id);
        }
        $this->_redirect('/docente/bolsa/index/id_pessoa/' . $id_pessoa);
    }

}