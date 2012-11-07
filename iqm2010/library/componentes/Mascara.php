<?php
class mascara
{
	public function __construct()
	{
	}

	public function dateSQLtoDay($date)
	{
		$array= explode('-',$date);

		$total=0;
		$total=((int)$array[0])*365;
		$total+=((int)$array[1])*30;
		$total+=((int)$array[2]);

		return $total;

	}
	
	public function getArrayData($data_mascara)
	{
		if(strlen($data_mascara)==10)
		$data=explode('/',$data_mascara);
		
		$data['dia']=$data[0];
		$data['mes']=$data[1];
		$data['ano']=$data[2];
		
		return $data;
	}
	
	
	public function getMes($m)
	{
		switch($m)
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
	public function trataArrayParaComboBox($array, $field, $value)
	{
		$result=array();
		foreach($array as $r)
		{
			$result[$r[$value]] = htmlentities($r[$field]);
		}

		return $result;
	}

	public function datetimeSQLToMascaraData($value)
	{
		$value=explode('-',$value);
		$dia=substr($value[2],0,2);
		return $dia.'/'.$value[1].'/'.$value[0];
	}

	public function datetimeSQLToMascaraHora($value)
	{
		$value=explode('-',$value);
		$hora=substr($value[2],2,strlen($value[2]));
	
		return $hora;
	}
	
	
	public function datetimeSQLToMascaraMesAno($value)
	{
		$value=explode('-',$value);
		$dia=substr($value[2],0,2);
		return $value[1].'/'.$value[0];
	}

	public function MascaraDataTodatetimeSQL($value)
	{
		$value=explode('/',$value);
		$value=$value[2].'-'.$value[1].'-'.$value[0];

		return $value;
	}

	public function floatToMoeda($value)
	{
		$value= number_format($value, 2, ',', '.');

		return $value;
	}

	public function moedaToFloat($value)
	{
		$value=str_replace('.','',$value);
		$value=str_replace(',','.',$value);

		return $value/1;
	}

	public function mascaraInputDataHoraJS($id_class_input)
	{
		$html='<script type="text/javascript">';
		$html.="jQuery(function($){";


		$html.='$("'.$id_class_input.'").mask("99/99/9999, 99:99");';

		$html.="});";
		$html.="</script>";

		return $html;
	}

	public function mascaraInputDataJS($id_class_input)
	{
		$html='<script type="text/javascript">';
		$html.="jQuery(function($){";


		$html.='$("'.$id_class_input.'").mask("99/99/9999");';

		$html.="});";
		$html.="</script>";

		return $html;
	}

	public function mascaraInputMesAnoJS($id_class_input)
	{
		$html='<script type="text/javascript">';
		$html.="jQuery(function($){";


		$html.='$("'.$id_class_input.'").mask("99/9999");';

		$html.="});";
		$html.="</script>";

		return $html;
	}

	public function mascaraInputCPFJS($id_class_input)
	{
		$html='<script type="text/javascript">';
		$html.="jQuery(function($){;";


		$html.='$("'.$id_class_input.'").mask("999.999.999-99");';

		$html.="});";
		$html.="</script>";

		return $html;

	}

	public function mascaraInputHoraJS($id_class_input)
	{
		$html='<script type="text/javascript">';
		$html.="jQuery(function($){";


		$html.='$("'.$id_class_input.'").mask("99:99");';

		$html.="});";
		$html.="</script>";

		return $html;
	}

	public function mascaraInputMoedaJS($id_class_input)
	{
		$html='<script type="text/javascript">';
		$html.="jQuery(function($){";


		$html.='$("'.$id_class_input.'").maskMoney({showSymbol:true,symbol:"R$ ",decimal:",",thousands:"."});  ';

		$html.="});";
		$html.="</script>";

		return $html;
	}
}
?>