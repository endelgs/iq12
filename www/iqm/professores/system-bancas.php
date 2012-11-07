<?php $bancas = IQ_Relatorio::getBancasProfessor();?>
<div class="header">
<p class="spacer"></p>
	<h1>Bancas</h1>
</div>
<p class="relDescription">Aqui voc� v� todas as bancas das quais participou (como presidente, membro ou suplente) em ordem cronol�gica decrescente.</p>
<?php if (empty($bancas)){
	echo "<h3>N�o h� bolsas cadastradas.</h3>";
}else{?>
<?php $bancas = IQ_Relatorio::ordenaBancas($bancas);
foreach($bancas as $key => $value):?>
	<h2><?php echo $key?></h2>
	<p class="margin-12"></p>
	<?php foreach($value as $banca){?>
			<p><span class="blue"><!-- <b><?php echo $banca['tipo_banca']?></b> -->Banca de <?php echo $banca['tipo']?> de <a href="<?php IQ_Usuario::profileLink($banca['id_pessoa'], true)?>" title="Perfil do Aluno"><?php echo $banca['nome']?></a></span></p>
			<p>Atribui��o: <?php echo $banca['atribuicao']?></p>
			<p><?php echo $banca['data']?></p>
		<p class="margin-12"></p>
		<p class="margin-12"></p>
	<?php }?>
<?php endforeach; 
}?>
<p class="margin-15"></p>
