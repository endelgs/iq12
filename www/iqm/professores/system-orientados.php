<?php $orientadores = IQ_Relatorio::getOrientadoresDoProfessor();?>
<div class="header">
	<p class="spacer"></p>
	<h1>Orientados</h1>
</div>
<p class="relDescription">Este relatório mostra os seus ex-orientandos</p>
<?php if (empty($orientadores)){?>
<h3>Não existem registros de ex-orientandos.</h3>
<p class="margin-12"></p>
<?php }else{
foreach($orientadores as $orientador):?>
	<h3><a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($orientador['id_professor_pessoa']); ?>" title="Perfil do Aluno"><?php echo $orientador['nome_professor']; ?></a></h3>
	<p class="margin-12"></p>
<?php endforeach; 
}?>

