<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Docente.php');
require_once(APPLICATION_PATH . '/models/ProjetosDocentes.php');

class Docente_ProjetosController extends Zend_Controller_Action {

    public $id_pessoa;

    public function init() {
        /* Initialize action controller here */
        $id_pessoa = $this->_request->getParam("id_pessoa");
        $this->id_pessoa = $id_pessoa;
        $clsPessoa = new Application_Models_Pessoas();

        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function indexAction() {

        $id_pessoa = $this->_request->getParam("id_pessoa");

        $projetos = new Application_Models_ProjetosDocente();
        $arrProjetos = $projetos->CRUDread($id_pessoa);

        $this->view->bolsa_grid = $arrProjetos;
    }

    public function postAction() {
        $id_pessoa = $this->_request->getParam("id_pessoa");
        $clsProjetos = new Application_Models_ProjetosDocente();

//        var_dump($_POST);
        $array = $_POST;
        $id = $_POST['id'];
        unset($array['id']);
        $array['id_pessoa_docente'] = $id_pessoa;

        if ($this->_request->getParam("id") != "") {
            $clsProjetos->CRUDupdate($array, $id);
        } else {
            $clsProjetos->CRUDcreate($array);
        }

        $this->redirect();
    }

    public function redirect() {
        $this->_redirect('/docente/projetos/index/id_pessoa/' . $this->id_pessoa);
    }

    public function deleteAction() {
        $id = $this->_request->getParam("id");
        $id_pessoa = $this->_request->getParam("id_pessoa");
        if ($id != '') {
            $clsProjetos = new Application_Models_ProjetosDocente();
            $clsProjetos->CRUDdelete($id);
        }
        $this->redirect();
    }

}