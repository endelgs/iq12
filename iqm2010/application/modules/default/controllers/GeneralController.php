<?php
require_once(APPLICATION_PATH.'/models/General.php');
class GeneralController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function paisesAction()
    {
    	//print_r(get_declared_classes());
    	
    	$general = new Application_Models_General();
    	$this->view->paises = $general->listPaises();
    	/*print_r($this->view->paises);*/
    	//echo 'Paises';
    }

}

