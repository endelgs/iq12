<div class="header">
<p class="spacer"></p>
	<h1>Home</h1>
</div>
<p class="relDescription">Esta é sua página inicial. Aqui se concentram as informações mais relevantes e que devem ser mais acessadas por você.</p>
<p class="margin-12"></p>
<h2>Relatórios de Orientandos</h2>
<p class="margin-15"></p>
<?php $relatorios = IQ_Relatorio::getRelatorioPendenteOrientador($_SESSION['user']['id_professor']);?>
<h2 class="blue small">Relatórios Pendentes</h2>
<p class="relDescription">Todos os relatórios não entregues por seus orientandos</p>
<?php if (empty($relatorios)){
	echo "<h3>Não há relatórios pendentes de seus orientandos.</h3>";
	echo '<p class="margin-12"></p>'."\n";
}else{
	foreach($relatorios as $relatorio):?>
	<h3>
		Relatório de <a href="<?php IQ_Usuario::profileLink($relatorio['id_pessoa'], true)?>" title="Perfil do Aluno"><?php echo $relatorio['nome']?></a>, 
		prazo de entrega dia <span class="red" ><?php echo $relatorio['data_prevista_f'];?></span> [<a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relatório completo">veja mais</a>]
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}?>
<p class="margin-15"></p>
<?php $pareceres = IQ_Relatorio::getPareceresPendenteOrientador($_SESSION['user']['id_professor']);?>
<h2 class="blue small" >Pareceres Pendentes</h2>
<p class="relDescription">Relatórios de orientandos já entregues, nos quais você ainda não deu seu parecer</p>
<?php if (empty($pareceres)){
	echo "<h3>Não há pareceres pendentes de seus orientandos.</h3>";
	echo '<p class="margin-12"></p>'."\n";
}else{
	foreach($pareceres as $parecer):?>
	<h3>
		Parecer do relatório de <a href="<?php IQ_Usuario::profileLink($parecer['id_pessoa'], true)?>" title="Perfil do Aluno"><?php echo $parecer['nome']?></a>, 
		entregue em <?php echo $parecer['aluno_data_entrega'];?>
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}?>
<p class="margin-15"></p>

<h2>Relatórios como Assessor ou Parecerista</h2>


<p class="margin-15"></p>
<h2 class="blue small">Relatórios Pendentes</h2>
<p class="relDescription">Todos os relatórios não entregues por seus não-orientandos</p>
<?php $relatorios = IQ_Relatorio::getRelatorioPendente($_SESSION['user']['id_professor']);
if (!empty($relatorios)){
	foreach($relatorios as $relatorio):?>
	<h3>
		Relatório de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($relatorio['id_pessoa'])?>" title="Perfil do Aluno">
		<?php echo $relatorio['nome']?></a>, prazo de entrega dia 
		<span class="red" ><?php echo $relatorio['data_prevista_f'];?></span> [<a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relatório completo">veja mais</a>]
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}else{?>
	<h3>Não há relatórios pendentes.</h3>
	<p class="margin-12"></p>
<?php }?>
<p class="margin-15"></p>
<h2 class="blue small">Pareceres Pendentes</h2>
<p class="relDescription">Relatórios de não-orientandos já entregues, nos quais você ainda não deu seu parecer</p>

<?php $pareceres = IQ_Relatorio::getParecerPendente($_SESSION['user']['id_professor']);
if (!empty($pareceres)){
	foreach($pareceres as $parecer):?>
	<h3>
		
		Parecer do relatório de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($parecer['id_pessoa'])?>" title="Perfil do Aluno">
		<?php echo $parecer['nome']?></a>, entregue em <?php echo $parecer['data_entrega_f'];?>
	</h3>
	<p class="margin-12"></p>
	<?php endforeach;
}else{?>
	<h3>Não há pareceres pendentes.</h3>
	<p class="margin-12"></p>
<?php }?>