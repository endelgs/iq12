<?php $bolsa = IQ_Relatorio::getBolsasProfessor('', base64_decode($_GET['b'])); ?>
<h1 class="header">Informações da Bolsa</h1>
<p class="margin-15"></p>	
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
		<td class="leftPadding igualmenteEspacados3" ><b>Nome da Agência</b></td>
		<td class="igualmenteEspacados3"><b>Data de Início</b></td>
		<td class="rightPadding igualmenteEspacados3"><b>Data de Término</b></td>
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
		<td class="fullPadding"><?php echo $bolsa['processo'] != '' ? $bolsa['processo'] : "Não há informações."?></td>		
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td class="fullPadding"><b>Observações</b></td>		
	</tr>
	<tr>
		<td class="fullPadding"><?php echo $bolsa['observacao'] != '' ? $bolsa['observacao'] : "Não há informações."?></td>		
	</tr>
</table>
