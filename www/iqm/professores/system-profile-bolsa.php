<?php $bolsa = IQ_Relatorio::getBolsasProfessor('', base64_decode($_GET['b']));?>
<div class="header">
	<p class="spacer"></p>
	<h1>Informa��es da Bolsa</h1>
</div>
<p class="relDescription">Aqui, voc� v� os detalhes de bolsa de um aluno espec�fico</p>
<table>
	<tr>
		<td class="fullPadding" colspan="3"><b>Nome</b></td>
	</tr>
	<tr>
		<td class="fullPadding" colspan="3">
			<?php echo $bolsa['nome']?>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td class="leftPadding igualmenteEspacados3" ><b>Nome da Ag�ncia</b></td>
		<td class="igualmenteEspacados3"><b>Data de In�cio</b></td>
		<td class="rightPadding igualmenteEspacados3"><b>Data de T�rmino</b></td>
	</tr>
	<tr>
		<td class="leftPadding igualmenteEspacados3" ><?php echo $bolsa['agencia']?></td>
		<td class="igualmenteEspacados3"><?php echo $bolsa['data_inicio']?></td>
		<td class="rightPadding igualmenteEspacados3"><?php echo $bolsa['data_fim']?></td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td class="fullPadding"><b>Processo</b></td>		
	</tr>
	<tr>
		<td class="fullPadding"><?php echo $bolsa['processo'] != '' ? $bolsa['processo'] : "N�o h� informa��es."?></td>		
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td class="fullPadding"><b>Observa��es</b></td>		
	</tr>
	<tr>
		<td class="fullPadding"><?php echo $bolsa['observacao'] != '' ? $bolsa['observacao'] : "N�o h� informa��es."?></td>		
	</tr>
</table>
