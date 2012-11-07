<?php $relatorio = IQ_Relatorio::getRelatorio(base64_decode($_GET['r']));
$warning = false;
if (strtotime($relatorio['adp']) <= time())
	$warning = true;
?>
<h1 class="header">Relat�rio de <?php echo $relatorio['nome']?></h1>
<p class="margin-15"></p>
<table id="tableRelatorio">
	<tr>
		<td class="relatorioLeft" >Data de in�cio</td>
		<td class="relatorioRight"><?php echo $relatorio['aluno_data_inicio']?></td>
	</tr>
	<tr>
		<td class="relatorioLeft" >Data de t�rmino</td>
		<td class="relatorioRight"><?php echo $relatorio['aluno_data_termino']?></td>
	</tr>
	<tr>
		<td class="relatorioLeft" >Data prevista</td>
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
		<td class="relatorioBoth" colspan="2" ><b>Observa��o</b></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2"><?php echo $relatorio['observacao'] != "" ? $relatorio['observacao'] : "N�o h� observa��o."; ?></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ><b>Parecer</b></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2"><?php echo $relatorio['parecer'] != "" ? $relatorio['parecer'] : "N�o h� parecer."; ?></td>
	</tr>
</table>
