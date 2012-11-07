<?php
require_once(APPLICATION_PATH.'/models/Ingressos.php');
class Script_IndexController extends Zend_Controller_Action
{
       public function init()
       {
               /* Initialize action controller here */
       }

       public function indexAction()
       {
       		$clsIngressos = new Application_Models_Ingressos();
       		$arrIngressos = $clsIngressos->getIngressosAll();
       		
       		foreach ($arrIngressos as $ingresso)
       		{
       			$dataIngresso=$ingresso['data_ingresso'];
       			if($dataIngresso!="")
       			{
	       			if($dataIngresso!="0000-00-00 00:00:00")
	       			{
	       				$dataIngresso= explode(" ", $dataIngresso);
	       				$dataIngresso= explode("-", $dataIngresso[0]);
	       				$ano=$dataIngresso[0];
	       				$mes=$dataIngresso[1];
	       				$dia=$dataIngresso[2];
	       				
	       				if($mes=="" || $mes=="00")
	       					$mes="01";
	       					
	       				if($dia=="" || $dia="00")
	       					$dia="01";
	       						
	       				$dataIngresso="$ano-$mes-$dia 00:00:00";
	       			}
	       			
       			}
       		
       		
       		}
       		
       }

}