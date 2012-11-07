<div class="header">
	<p class="spacer"></p>
	<h1>Parecer de Bolsa</h1>
</div>
<p class="margin-12"></p>
<h2>Bolsas Sem Parecer</h2>
<p class="relDescription">Aqui você pode ver as bolsas que dependem do seu parecer, mas você ainda não o fez. Clicando em [veja mais] é possível ver os detalhes da bolsa.</p>
<?php $bolsas = IQ_Relatorio::getBolsasProfessorSemParecer();?>
<?php 
if(empty($bolsas))
{
	echo "<h3>Não existem bolsas sem parecer.</h3>";
	echo "<p class=\"margin-15\"></p>";
}
else
{
	foreach($bolsas as $bolsa):
?>
	<div class="profileBolsa">
		Bolsa de <a href="<?php IQ_Usuario::profileLink($bolsa['id_pessoa'], true);?>" title="Perfil do aluno" ><?php echo $bolsa['nome']?></a>
		<span style="float:right;">[<a href="<?php echo Config::HOMEURL."?q=".Config::PERFILDEBOLSA."&amp;b=".base64_encode($bolsa['id_bolsa']);?>" title="Ver bolsa completa">veja mais</a>]</span>
	</div>
	<p class="margin-12"></p>
<?php endforeach; 
}?>
<h2>Bolsas Com Parecer</h2>
<p class="relDescription">Aqui você pode ver as bolsas às quais você já deu seu parecer. <br />Clicando em [veja mais] é possível ver os detalhes da bolsa.</p>
<?php $bolsas = IQ_Relatorio::getBolsasProfessorComParecer();?>
<?php if (empty($bolsas)):
	echo "<h3>Não existem bolsas com parecer.</h3>";
	echo "<p class=\"margin-15\"></p>";
else:
	foreach($bolsas as $bolsa):?>
		<div class="profileBolsa">
			Bolsa de <a href="<?php IQ_Usuario::profileLink($bolsa['id_pessoa'], true);?>" title="Perfil do aluno" ><?php echo $bolsa['nome']?></a>
			<span style="float:right">[<a href="<?php echo Config::HOMEURL."?q=".Config::PERFILDEBOLSA."&amp;b=".base64_encode($bolsa['id_bolsa']);?>" title="Ver bolsa completa">veja mais</a>]</span>
		</div>
	<p class="margin-12"></p>
<?php endforeach; 
endif;?>
<p class="margin-15"></p>
