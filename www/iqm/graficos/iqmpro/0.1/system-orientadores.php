<?php $orientadores = IQ_Relatorio::getOrientadoresDoProfessor();?>
<h1 class="header">Orientadores</h1>
<p class="margin-15"></p>
<p>Este relatório mostra os seus orientadores</p>
<?php if (empty($orientadores)){?>
<h3>Não existem orientadores.</h3>
<p class="margin-12"></p>
<?php }else{
foreach($orientadores as $orientador):?>
	<h3><a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($orientador['id_professor_pessoa']); ?>" title="Perfil do Aluno"><?php echo $orientador['nome_professor']; ?></a></h3>
	<p class="margin-12"></p>
<?php endforeach; 
}?>

