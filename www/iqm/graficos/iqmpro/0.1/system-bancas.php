<?php $bancas = IQ_Relatorio::getBancasProfessor();?>
<h1 class="header">Bancas</h1>
<p class="margin-15"></p>
<?php if (empty($bancas)){
	echo "<h3>Não há bolsas cadastradas.</h3>";
}else{?>
<?php $bancas = IQ_Relatorio::ordenaBancas($bancas);
foreach($bancas as $key => $value):?>
	<h2><?php echo $key?></h2>
	<p class="margin-12"></p>
	<?php foreach($value as $banca){?>
		<table>
			<tr>
				<td class="fullPadding" colspan="3"><span class="blue"><!-- <b><?php echo $banca['tipo_banca']?></b> -->Banca de <?php echo $banca['tipo']?> de <a href="<?php IQ_Usuario::profileLink($banca['id_pessoa'], true)?>" title="Perfil do Aluno"><?php echo $banca['nome']?></a></span></td>
			</tr>
			<tr>
				<td class="leftPadding igualmenteEspacados3"><?php echo $banca['data']?></td>
				<td class="igualmenteEspacados3">Atribuição: <?php echo $banca['atribuicao']?></td>
				<td class="rightPadding igualmenteEspacados3">&nbsp;</td>
			</tr>
		</table>
		<p class="margin-12"></p>
		<p class="margin-12"></p>
	<?php }?>
<?php endforeach; 
}?>
<p class="margin-15"></p>
