<?php
require_once(APPLICATION_PATH.'/../library/relatorios/GridPdf.php');
require_once(APPLICATION_PATH.'/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH.'/models/ListaRelatorioPDF.php');

class Logs_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{

		$db = new Application_Models_LogsDeAcesso();
		$arr=$db->CRUDread();
		$mask=  new mascara();
		 
		 
		forEach($arr as $index=>$a)
		{
			$arr[$index]['data_mask']=$mask->datetimeSQLToMascaraData($a['data']);
			$arr[$index]['hora_mask']=$mask->datetimeSQLToMascaraHora($a['data']);
		}
		 
		$headers = array( 'data_mask' => 'Data',
       					  'hora_mask' => 'Hora',
       					  'descricao' => 'Descrição',
       					  'user'      => 'Usuário',
		 
		);

		//       		 $w = array( 'data_mask' => '10',
		//       							'hora_mask' => '10',
		//       							'user' => '10',
		//       						'descricao' => '10',
		//       							);

		$gridPDF = new GridPdf();
		 
		$this->view->grid=$gridPDF->flexGridJS($arr, $headers,'','');
		 
	}

}