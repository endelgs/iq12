<?php
error_reporting(0);
include 'lib/fpdf/fpdf.php';
// Funcao 'magica' que carrega automaticamente as classes necessarias
if(!function_exists('__autoload'))
{
	function __autoload($classname)
	{
		$classname = str_replace('_','/',$classname);
		$file = strtolower($classname).'.php';
		$path = dirname(__FILE__).'/lib/'.$file;
		if(file_exists($path))
			include_once($path);
	}
}

class Relatorio extends FPDF
{
	public function __construct()
	{
		parent::__construct();
		$this->SetFont('arial');
	}
	public function header()
	{
		//Logo
	    $this->Image('assets/logo_unicamp.gif',10,10,12*1.45,12);
	    
	    $this->SetDrawColor(0,0,0);
		// $this->SetDrawColor(159,0,0);
	    
	    //Thickness of frame (1 mm)
    	$this->SetLineWidth(0.1);
	    
	    //Calibri bold 15
	    //$this->SetFont('Calibri','B',10);
	    
	    //Move to the right
	    $this->Cell(15);
	    //Title
	    $this->Cell(145,10,'UNICAMP - Universidade Estadual de Campinas');
	    $this->Ln(5);
	    $this->Cell(15);
	    $this->Cell(145,10,'Coordenadoria de Pós Graduação do Instituto de Química');
		
	    $meses = array(
	    	'Janeiro',
	    	'Fevereiro',
	    	'Março',
	    	'Abril',
	    	'Maio',
	    	'Junho',
	    	'Julho',
	    	'Agosto',
	    	'Setembro',
	    	'Outubro',
	    	'Novembro',
	    	'Dezembro'
	    	);
	    	
	    $dia = date("d");
	    $mes = date("m");
	    $ano = date("Y");
	    $hora = date("H:i:s");
	    
	    $mes = $meses[$mes - 1];
	    
	    $this->Ln(10);
	    $this->Cell(15);
	    $this->Cell(30,10,"Campinas, $dia de $mes de $ano. Relatório gerado às $hora",0,0,'L');
	    
	    //Line break
	    $this->Ln(8);
	    $this->cell(180,0,'',1);
	    
	    $this->ln(0);   
	}
	public function footer()
	{
			
	}
	//Better table
	public function ImprovedTable($headers,$data)
	{
	    //Column widths
	    $w = 180/count($headers);
	    //Header
	    $i = 0;
	    //$this->setFont('Calibri','',12);
	    foreach($headers as $h)
	        $this->Cell($w,7,$h['value'],1,0,'C');
	    $this->Ln();
	    
	    //Data
	    //$this->setFont('Calibri','',12);
	    foreach($data as $row)
	    {
			foreach($headers as $h)
	    		$this->Cell($w,6,$row[$h['name']],'LR');
	        $this->Ln();
	    }
	    //Closure line
	    $this->Cell(180,0,'','T');
	}
}

$data 		= $_REQUEST['params']['data'];
$headers 	= $_POST['params']['headers'];
$titulo 	= $_REQUEST['title'];
$desc		= utf8_decode($_REQUEST['desc']);
//print_r($_REQUEST['params']);
$pdf = new Relatorio();
$pdf->SetMargins(15,10,15);
//$pdf->AddFont('Calibri');
//$pdf->AddFont('Calibri','B');
//$pdf->AddFont('Calibri','I');
//$pdf->AddFont('Calibri','Z');

//$pdf->setFont('Calibri','',12);
$pdf->addPage();

// Coloco o título e a descrição do relatório

$pdf->cell(180,10,$titulo);
//$pdf->setFont('Arial');
$pdf->ln(10);
$pdf->MultiCell(180,5,$desc);
$pdf->ln(10);

// Imprimo a tabela
$pdf->cell(90,10,'Tabela de dados',0,1);
$pdf->improvedTable($headers,$data);

// Imprimo o grafico
$pdf->addPage();
$pdf->Cell(40,10,'Representação gráfica',0,0);

$imgPath = 'upload/'.$_POST['image'];
//$pdf->setFont('Calibri','',12);
$size = getimagesize($imgPath);
//$size = getimagesize('upload/screenshot_20110103115331.jpg');

//print_r($size);

$k = $size[0]/$size[1];
$imgW = 120*$k;
$imgH = 120;

$pdf->Image($imgPath,20,50,$imgW,$imgH,'jpg');
unlink($imgPath);
$pdf->output();