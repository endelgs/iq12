<?php $orientandos = IQ_Relatorio::getAlunosDoProfessor($_SESSION['user']['id_professor']);?>
<h1 class="header">Orientandos</h1>
<p class="margin-15"></p>
<p>Esse relatório mostra todos os seus orientandos atuais</p>
<?php if (empty($orientandos)){?>
<h3>Não existem alunos orientandos.</h3>
<p class="margin-12"></p>
<?php }else{
foreach($orientandos as $orientando):?>
	<h3><a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($orientando['id_pessoa']); ?>" title="Perfil do Aluno"><?php echo $orientando['nome']; ?></a></h3>
	<p class="margin-12"></p>
<?php endforeach; 
}?>
