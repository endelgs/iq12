<?php $relatorio = IQ_Relatorio::getRelatorio(base64_decode($_GET['r']));
$warning = (strtotime($relatorio['adp']) <= time());

?>
<div class="header">
	<p class="spacer"></p>
	<h1>Relat�rio Acad�mico de <?php echo $relatorio['nome']?></h1>
</div>
<p class="relDescription">Aqui s�o mostrados os datalhes de um relat�rio</p>
<h2>Relat�rio referente ao per�odo <?php echo (empty($relatorio['aluno_data_inicio']))?' - ':$relatorio['aluno_data_inicio']; ?> at� <?php echo (empty($relatorio['aluno_data_termino']))?' - ':$relatorio['aluno_data_termino']; ?> </h2>
<p class="margin-15"></p>
<table id="tableRelatorio">
	<tr>
		<td class="relatorioLeft" >Entrega prevista para</td>
		<td class="relatorioRight"><?php echo ($warning && $relatorio['aluno_data_entrega'] == "00/00/0000") ? "<span class=\"red\" >".$relatorio['data_prevista']."</span>" : $relatorio['data_prevista'];?></td>
	</tr>
	<tr>
		<td class="relatorioLeft" >Data de entrega</td>
		<td class="relatorioRight"><?php echo $relatorio['aluno_data_entrega'] != "00/00/0000" ? $relatorio['data_entrega'] : "N�o entregue"; ?></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ><b>Observa��es</b></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2"><?php echo $relatorio['observacao'] != "" ? $relatorio['observacao'] : "N�o h� observa��es."; ?></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ><b>Parecer do relat�rio</b></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2"><?php echo $relatorio['parecer'] != "" ? $relatorio['parecer'] : "N�o h� parecer."; ?></td>
	</tr>
</table>
