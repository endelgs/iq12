<h1 class="header">Home</h1>
<p class="margin-12"></p>
<h2>Relat�rios de Orientandos</h2>
<p class="margin-15"></p>
<?php $relatorios = IQ_Relatorio::getRelatorioPendenteOrientador($_SESSION['user']['id_professor']);?>
<h2 class="blue small">Relat�rios Pendentes</h2>
<p class="descricao">Todos os relat�rios n�o entregues por seus orientandos</p>
<p class="margin-15"></p>
<?php if (empty($relatorios)){
	echo "<h3>N�o h� relat�rios pendentes de seus orientandos.</h3>";
	echo '<p class="margin-12"></p>'."\n";
}else{
	foreach($relatorios as $relatorio):?>
	<h3>
		Relat�rio de <a href="<?php IQ_Usuario::profileLink($relatorio['id_pessoa'], true)?>" title="Perfil do Aluno"><?php echo $relatorio['nome']?></a>, 
		prazo de entrega dia <span class="red" ><?php echo $relatorio['aluno_data_previsto'];?></span> [<a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relat�rio completo">veja mais</a>]
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}?>
<p class="margin-15"></p>
<?php $pareceres = IQ_Relatorio::getPareceresPendenteOrientador($_SESSION['user']['id_professor']);?>
<h2 class="blue small" >Pareceres Pendentes</h2>
<p class="tip">Relat�rios de orientandos j� entregues, nos quais voc� ainda n�o deu seu parecer</p>
<p class="margin-15"></p>
<?php if (empty($pareceres)){
	echo "<h3>N�o h� pareceres pendentes de seus orientandos.</h3>";
	echo '<p class="margin-12"></p>'."\n";
}else{
	foreach($pareceres as $parecer):?>
	<h3>
		Parecer do relat�rio de <a href="<?php IQ_Usuario::profileLink($parecer['id_pessoa'], true)?>" title="Perfil do Aluno"><?php echo $parecer['nome']?></a>, 
		entregue em <?php echo $parecer['aluno_data_entrega'];?>
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}?>
<p class="margin-15"></p>

<h2>Relat�rios como Acessorista</h2>
<p class="margin-15"></p>
<h2 class="blue small">Relat�rios Pendentes</h2>
<p class="margin-15"></p>
<?php $relatorios = IQ_Relatorio::getRelatorioPendente($_SESSION['user']['id_professor']);
if (!empty($relatorios)){
	foreach($relatorios as $relatorio):?>
	<h3>
		Relat�rio de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($relatorio['id_pessoa'])?>" title="Perfil do Aluno">
		<?php echo $relatorio['nome']?></a>, prazo de entrega dia 
		<span class="red" ><?php echo $relatorio['aluno_data_previsto'];?></span> [<a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relat�rio completo">veja mais</a>]
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}else{?>
	<h3>N�o h� relat�rios pendentes.</h3>
	<p class="margin-12"></p>
<?php }?>
<p class="margin-15"></p>
<h2 class="blue small">Pareceres Pendentes</h2>
<p class="margin-15"></p>

<?php $pareceres = IQ_Relatorio::getParecerPendente($_SESSION['user']['id_professor']);
if (!empty($pareceres)){
	foreach($pareceres as $parecer):?>
	<h3>
		
		Parecer do relat�rio de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($parecer['id_pessoa'])?>" title="Perfil do Aluno">
		<?php echo $parecer['nome']?></a>, entregue em <?php echo $parecer['data_entrega_f'];?>
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}else{?>
	<h3>N�o h� pareceres pendentes.</h3>
	<p class="margin-12"></p>
<?php }?>