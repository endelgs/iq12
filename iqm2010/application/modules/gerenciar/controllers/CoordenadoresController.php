<?php

require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/GerenciarGenerico.php');

class Gerenciar_CoordenadoresController extends Zend_Controller_Action {

    public function init() {

    }

    public function instanciaBD() {
        $data = new Application_Models_GerenciarGenerico();
        $data->_primary('id_coordenador');
        $data->_name('coordenador');

        return $data;
    }

    public function indexAction() {

        $data = $this->instanciaBD();
        $this->view->data = $data->CRUDread();

        if ($this->getRequest()->isPost()) {

            $id = $_POST['id'];
            unset($_POST['id']);

            $id="1";
            
            if ($id != "") {
                $data->CRUDupdate($_POST, $id);
            }

//            else {
//
//                $data->CRUDcreate($_POST);
//            }

            $this->redirect();
        }
    }

    public function redirect() {
        $this->_redirect('/gerenciar/coordenadores/');
    }

    function deleteAction() {

        $data = $this->instanciaBD();
        $data->CRUDdelete($this->_request->getParam("id"));
        $this->redirect();
    }

}