<?php
class StringUtil
{
	public function __construct()
	{
		
	}
	public static function antiInjection($sql)
	{
		// remove palavras que contenham sintaxe sql
		$sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
		$sql = trim($sql);//limpa espa�os vazio
		$sql = strip_tags($sql);//tira tags html e php
		$sql = addslashes($sql);//Adiciona barras invertidas a uma string
		return $sql;
	}
	
	// $echo - Indica se deve escrever ou retornar a string
	public static function getDate($echo = false)
	{
		$semana = array('Domingo', 'Segunda-feira', 'Ter�a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'S�bado');
		$mes = array('Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
		
		$output = $semana[date('w')].", ".date('d')." de ".$mes[date('n')-1]." de ".date('Y');
	
		if ($echo)
			echo $output;
		else
			return $output;
	}
	
	public static function cpfHumanizado($cpf)
	{
		return substr($cpf, 0, 3).".".substr($cpf, 3, 3).".".substr($cpf, 6, 3)."-".substr($cpf, 9, 2);
	}
	
	/* Converte uma data do jeito do banco de dados para uma data mais humana [hh:mm dd/mm/AAAA]
	 * Se $mostraHora � igual a true, a data retornada cont�m tamb�m as horas e minutos. Sen�o
	 * apenas o dia, m�s e ano s�o retornados.
	 */
	public static function dataHumanizada($data, $mostraHora = false)
	{
		// Separo a data da hora
		$data = split(' ', $data);
		
		$data[0] = split('-', $data[0]);
		if($mostraHora)
			return $data[1]." ".$data[0][2]."/".$data[0][1]."/".$data[0][0];
		else
			return $data[0][2]."/".$data[0][1]."/".$data[0][0];
	}
	public static function ano($data)
	{
		$data = split('/', $data); 
		return $data[2];
	}
}