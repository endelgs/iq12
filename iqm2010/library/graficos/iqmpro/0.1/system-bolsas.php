<h1 class="header">Parecer de Bolsa</h1>
<p class="margin-12"></p>
<h2>Bolsas Sem Parecer</h2>
<p class="margin-12"></p>
<?php $bolsas = IQ_Relatorio::getBolsasProfessorSemParecer();?>
<?php if (empty($bolsas)){
	echo "<h3>Não existem bolsas sem parecer.</h3>";
	echo "<p class=\"margin-15\"></p>";
}else{
	foreach($bolsas as $bolsa):?>
	<table>
		<tr>
			<td class="leftPadding">Bolsa de <a href="<?php IQ_Usuario::profileLink($bolsa['id_pessoa'], true);?>" title="Perfil do aluno" ><?php echo $bolsa['nome']?></a></td>
			<td class="rightPadding alignRight">[<a href="<?php echo Config::HOMEURL."?q=".Config::PERFILDEBOLSA."&amp;b=".base64_encode($bolsa['id_bolsa']);?>" title="Ver bolsa completa">veja mais</a>]</td>
		</tr>
	</table>
	<p class="margin-12"></p>
<?php endforeach; 
}?>
<h2>Bolsas Com Parecer</h2>
<p class="margin-12"></p>
<?php $bolsas = IQ_Relatorio::getBolsasProfessorComParecer();?>
<?php if (empty($bolsas)):
	echo "<h3>Não existem bolsas com parecer.</h3>";
	echo "<p class=\"margin-15\"></p>";
else:
	foreach($bolsas as $bolsa):?>
	<table>
		<tr>
			<td class="leftPadding">Bolsa de <a href="<?php IQ_Usuario::profileLink($bolsa['id_pessoa'], true);?>" title="Perfil do aluno" ><?php echo $bolsa['nome']?></a></td>
			<td class="rightPadding alignRight">[<a href="<?php echo Config::HOMEURL."?q=".Config::PERFILDEBOLSA."&amp;b=".base64_encode($bolsa['id_bolsa']);?>" title="Ver bolsa completa">veja mais</a>]</td>
		</tr>
	</table>
	<p class="margin-12"></p>
<?php endforeach; 
endif;?>
<p class="margin-15"></p>
