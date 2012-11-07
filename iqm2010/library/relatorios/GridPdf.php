<?php
require_once(APPLICATION_PATH.'/../library/relatorios/PDF.php');
class GridPdf{

	private $_pdf;
	public $htmlParaPDF;

	public function __construct()
	{
		$this->_pdf = new PDF();
	}

	public function geraHtmlPadraoListagem()
	{

	}
	private function getMes()
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

		return $mes;
	}

	public function exibicaoPDF($headers, $arrayDados,$htmlPDF,$titulo, $campoLink='')
	{

		$url = explode('relatorios',$_SERVER['REQUEST_URI']);
		$imagens = 'http://'.$_SERVER['SERVER_NAME'].$url[0].'assets/images/';
		$GridPdf = new GridPdf();
		$PDF = new PDF();
		$array=$arrayDados;
		$dia=date('d');
		if(substr($dia,0,1)=='0')
		$dia=str_replace('0','',$dia);
		$semana = array("Sun" => "Domingo", "Mon" => "Segunda-Feira", "Tue" => "Terça-Feira",
"Wed" => "Quarta-Feira", "Thu" => "Quinta-Feira", "Fri" => "Sexta-feira" ,"Sat" => "Sábado");
		
		
		$content2.='<div style="text-align: center;"><img  src="'.$imagens.'logo-unicamp.jpg"/ height="100"><br><br>';
		$content2.='<h2>'.$titulo.'</h2></div><p>';
		$contentFinal2.='</p>';
		$contentFinal2.= '<br><br><br><p><div style="text-align: center;">Cidade Universitária "Zeferino Vaz"</p>';
		$contentFinal2.= '<br>'.$semana[date('D')].', '.$dia.' de '.$this->getMes().' de '.date('Y').'</div>';
		
		
		$link=$_SERVER['REQUEST_URI'];
		if(substr_count($_SERVER['REQUEST_URI'], '?')==0)
		{
			$link.='?';
		}else
		$link.='&';
		if($campoLink!="")
		{
			forEach($arrayDados as $index=>$l)
			{
				$texto=urlencode($l[$campoLink]);
				$link=str_replace('imprimir=pdf&todos=sim','',$link);
				$link=str_replace('&todos=sim','',$link);
				$linkAtual=$link.'imprimir=pdf&filtro='.$texto;

				foreach ($l as $index2=>$value) {
					
					$arrayDados[$index][$index2]='<a href="'.$linkAtual.'">'.$value.'</a>';
				}
			}

		}

		$flex=$GridPdf->flexGrid($arrayDados, $headers,'',true);

		if($_REQUEST['todos']=='sim')
		{
			foreach($htmlPDF as $index=>$h){
				$final[$index]=$content2.$h.$contentFinal2;
			}

		}elseif($_REQUEST['filtro']){

			foreach($htmlPDF as $index=>$h){
				if($_REQUEST['filtro']==$index )
				{
				$final[$_REQUEST['filtro']]=$content2.$h.$contentFinal2;
				}
				
			}
		}


		if($_REQUEST['imprimir']!="")
		{
			$PDF->criaPDFFormulario($final);
		}
			
			
		return $flex;

	}

	public function exibicaoFinal($tabela="", $primarykey="",$where="", $headers, $arrayBD=array(),$teste=false,$orderby='')
	{
		$GridPdf = new GridPdf();
		$PDF = new PDF();

		if($tabela!="") {
			$banco = new Application_Models_ListaRelatiorPDF();
			$banco->table($tabela);
			$banco->primary($primarykey);
			$banco->orderby=$orderby;
			$banco->where=$where;
			$array=$banco->CRUDread();
		}


		if($arrayBD!=array())
		$array=$arrayBD;

		if($teste)
		var_dump($array);


		$flex=$GridPdf->flexGrid($array, $headers);
		$html=$GridPdf->htmlParaPDF;
			
		//echo $html;
		if($_REQUEST['imprimir']!="")
		{
			$PDF->criaPDF($html);
		}
		return $flex;
	}


	public function flexGrid($array, $headers=array(), $htmlpdf="",$todos=false)
	{
		if($htmlpdf=="")
		$htmlpdf = $this->geraListaPadrao($array, $headers);
			
		$flex=$this->flexGridJS($array, $headers);
		$this->htmlParaPDF=$htmlpdf;

		$layout=$this->layout($flex,$todos);

		return $layout;
	}
	private function layout($flex,$todos=false)
	{
		$url=$_SERVER['REQUEST_URI'];
		if(substr_count($_SERVER['REQUEST_URI'], '?')==0)
		{
			$url.='?';
		}else
		$url.='&';

		if(substr_count($_SERVER['REQUEST_URI'], 'imprimir')==0){

			$url.='imprimir=pdf';


		}
		if($todos && substr_count($_SERVER['REQUEST_URI'], 'todos')==0)
		$url.='&todos=sim';


		$html.='<div style="width: 800px; margin-top: 10px;">
			'.$flex.'
			<p align="right">
			<a href="'.$url.'"> Gerar PDF</a>
			</p>
			</div>';

		return $html;

	}
    public function flexGridJS($array, $headers=array(), $width="700", $height="300") {

        require_once(APPLICATION_PATH . '/../library/grid_google/grid.php');

        $exclude = array();
        if (is_array($array) && is_array($array[0]))
            foreach ($array[0] as $index => $a)
                if (!array_key_exists($index, $headers))
                    $exclude[] = $index;

        $arrDados = $array;
        $isCrud = false;
        $headers = $headers;
        $exclude = $exclude;
        $doubleClick = '';
        $functionDelete = '';
        $height = $height;
  
        return grid($arrDados, $headers, $isCrud, $exclude, $doubleClick, $functionDelete, $height);
    }
	public function geraListaPadrao($records, $header, $idTable="", $class ="", $exibirHeader=true)
	{

		if(empty($records)) return '';

		$html = '<table border="0" class="'.$class.'" id="'.$idTable.'">';

		if(!is_array($records))
		$records = $records->toArray();

		$inicial=$records;
		$records=array();
		forEach($inicial as $indexI=>$r)
		{
			foreach($header as $indexH=>$h)
			{
				$records[$indexI][$indexH]=$r[$indexH];
			}
		}
		//var_dump($records);
		//exit;
		//		if($exibirHeader)
		//		{
		//			$html .= '<thead>';
		//			foreach($records[0] as $index=>$v)
		//			{
		//				if (array_key_exists($index, $header))
		//				$html.='<th align="left"><font face="helvetica" size="1">'.$header[$index].'</font></th>';
		//			}
		//
		//			$html .= '</thead>';
		//		}

		if($exibirHeader)
		{
			$html .= '<thead>';
			foreach($header as $index=>$v)
			{
				$html.='<th align="left"><font face="helvetica" size="1">'.$v.'</font></th>';
			}

			$html .= '</thead>';
		}

		foreach($records as $record)
		{

			$html.='<tr>';
			foreach($record as $name => $field)
			{
				if (array_key_exists($name, $header))
				$html.='<td width="200"><font face="helvetica" size="1">'.utf8_encode($field).'</font></td>';
			}


			$html.= '</tr>';

		}
		$html.='</table>';

		return $html;
	}
}
?>