<?php $relatorio = IQ_Relatorio::getRelatorio(base64_decode($_GET['r']));
$warning = false;
if (strtotime($relatorio['adp']) <= time())
	$warning = true;
?>
<h1 class="header">Relatório de <?php echo $relatorio['nome']?></h1>
<p class="margin-15"></p>
<table id="tableRelatorio">
	<tr>
		<td class="relatorioLeft" >Data de início</td>
		<td class="relatorioRight"><?php echo $relatorio['aluno_data_inicio']?></td>
	</tr>
	<tr>
		<td class="relatorioLeft" >Data de término</td>
		<td class="relatorioRight"><?php echo $relatorio['aluno_data_termino']?></td>
	</tr>
	<tr>
		<td class="relatorioLeft" >Data prevista</td>
		<td class="relatorioRight"><?php echo ($warning && $relatorio['aluno_data_entrega'] == "00/00/0000") ? "<span class=\"red\" >".$relatorio['data_prevista']."</span>" : $relatorio['data_prevista'];?></td>
	</tr>
	<tr>
		<td class="relatorioLeft" >Data de entrega</td>
		<td class="relatorioRight"><?php echo $relatorio['aluno_data_entrega'] != "00/00/0000" ? $relatorio['data_entrega'] : "Não entregue"; ?></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ><b>Observação</b></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2"><?php echo $relatorio['observacao'] != "" ? $relatorio['observacao'] : "Não há observação."; ?></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2" ><b>Parecer</b></td>
	</tr>
	<tr>
		<td class="relatorioBoth" colspan="2"><?php echo $relatorio['parecer'] != "" ? $relatorio['parecer'] : "Não há parecer."; ?></td>
	</tr>
</table>
