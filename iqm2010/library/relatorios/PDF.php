<?php
class PDF
{
	public function __construct()
	{

	}

	// retorna arquivo pdf a partir de html
	public function criaPDF($html)
	{
		switch(date('m'))
		{
			case 1:
				$mes = 'Janeiro';
				break;
			case 2:
				$mes = 'Fevereiro';
				break;
			case 3:
				$mes = 'Março';
				break;
			case 4:
				$mes = 'Abril';
				break;
			case 5:
				$mes = 'Maio';
				break;
			case 6:
				$mes = 'Junho';
				break;
			case 7:
				$mes = 'Julho';
				break;
			case 8:
				$mes = 'Agosto';
				break;
			case 9:
				$mes = 'Setembro';
				break;
			case 10:
				$mes = 'Outubro';
				break;
			case 11:
				$mes = 'Novembro';
				break;
			case 12:
				$mes = 'Dezembro';
				break;

		}
		$url = explode('relatorios',$_SERVER['REQUEST_URI']);
		$imagens = 'http://'.$_SERVER['SERVER_NAME'].$url[0].'assets/images/';

		$x = explode('<thead>',$html);
		$y = explode('</thead>',$x[1]);
		$header = $y[0];
		$table  = $y[1];

		$header = '
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" > 
		</head><body style="font-family: helvetica; font-size: 12px;">
		<table width="100%" border="0"><thead>
			<tr><th colspan="10" align="left">
			</th>
			</tr>
			<tr>
				<th colspan="10" align="left">
				<font style="margin-left: 48px;" size="2">Campinas, '.date('d').' de '.$mes.' de '.date('Y').'. Relat&oacute;rio gerado &agrave;s '.date('H:i:s').'</font>	
				</th>
			</tr>
			<tr>'.$header.'</tr>
			<tr><th colspan="10" width="100%"><hr></th></tr>
			</thead>';
		$content = $header.$table.'</body></html>';
		require_once(APPLICATION_PATH.'/../library/mpdf50/mpdf.php');
		//$content="VAZIO";
		ob_end_clean();
		//header("Content-Type: text/plain");
		//var_dump($content);
		//exit;
		$mpdf=new mPDF('c');
		$mpdf->WriteHTML($content);
		$arquivo = date("ymdhis").'_relatorio.pdf';
		$conteudo=$mpdf->Output($arquivo,'D');
		exit;
	}

	function criaPDFFormulario($content)
	{



		$url = explode('relatorios',$_SERVER['REQUEST_URI']);
		$pdf = 'http://'.$_SERVER['SERVER_NAME'].$url[0].'dompdf/relatorio.php';

		require_once(APPLICATION_PATH.'/../library/mpdf50/mpdf.php');
		$mpdf=new mPDF('c');
		
		$i=1;

		foreach ($content as $index=>$c) {
			
			$mpdf->WriteHTML($c);
			
			if($i!=(count($content))&& count($content)!=1)
				$mpdf->AddPage();
				
			$i++;
		}
		$arquivo = date("ymdhis").'_formulario.pdf';
		$conteudo=$mpdf->Output($arquivo,'D');
		exit;
		//		echo '
		//		<script>
		//		$(document).ready(function() {
		//			$("#pdfrelatorio").submit();
		//		});
		//		</script>
		//		<form style="visiblity: hidden; display:none; width: 0px; height: 0px; float: left;" id="pdfrelatorio" target="_blank" name="form" action="'.$pdf.'" method="post">
		//		<textarea name="relatorio" style="width: 0px; height: 0px;">'.$content.'</textarea>
		//		<input type="submit" />
		//		</form>
		//		';
	}
}
?>