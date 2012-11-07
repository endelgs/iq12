<?php

require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/GerenciarGenerico.php');

class Gerenciar_NotasMaximasDoutoradoController extends Zend_Controller_Action {

    public function init() {

    }

    public function instanciaBD() {
        $data = new Application_Models_GerenciarGenerico();
        $data->_primary('id_nota_maxima');
        $data->_name('v_nota_maxima');

        return $data;
    }

    public function indexAction() {

        $general = new Models_General();
        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));

        $data = $this->instanciaBD();
        $this->view->data = $data->CRUDread();

        if ($this->getRequest()->isPost()) {

            $id = $_POST['id'];
            unset($_POST['id']);

            if ($id != "") {
                $data->CRUDupdate($_POST, $id);
            } else {

                $data->CRUDcreate($_POST);
            }

            $this->redirect();
        }
    }

    public function redirect() {
        $this->_redirect('/gerenciar/notas-maximas-doutorado/');
    }

    function deleteAction() {

        $data = $this->instanciaBD();
        $data->CRUDdelete($this->_request->getParam("id"));
        $this->redirect();
    }

}