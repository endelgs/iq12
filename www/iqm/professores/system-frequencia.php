<?php
$alunos = IQ_Relatorio::getFrequencia($_SESSION['user']['id_professor']);
?>
<div class="header">
<p class="spacer"></p>
<h1>Frequência</h1></div>
<?php if(count($alunos) == 0): ?>
	<p>Você não tem orientandos</p>
<?php else:?>
<p class="relDescription">Aqui, você deve marcar apenas se a presença do aluno não está OK. Caso ele compareça regularmente, deixe o
marcador em branco. Só é possível anotar a frequência do mês imediatamente anterior ao atual. </p>
<h2>Exibindo frequência para o mês <?php echo (date('m')-1).'/'.date('Y');?></h2>
<p class="margin-12" ></p>
<form action="iomanager.php" method="post">
	<input type="hidden" name="entity" value="IQ_Usuario" />
	<input type="hidden" name="action" value="salvaFaltas" />
	<table style="margin-left:20px;">
	
<tr>
	<th style="width:60px">Não OK</th><th>Aluno</th>
</tr>	
<?php foreach($alunos as $aluno):?>
		<tr>
			<input type="hidden" name="params[id][]" value="<?php echo $aluno['id_pos_graduacao']?>" />
			<td><input name="params[aluno][<?php echo $aluno['id_pos_graduacao']?>]" value="1" type="checkbox" <?php if($aluno['nao_ok'])echo ' checked="checked" '?>/></td>
			<td><?php echo $aluno['nome']; ?></td>
		</tr>
	<?php endforeach;?>
	</table>
	<p><input type="submit" value="Salvar frequência" /></p>
	
</form>
<?php endif;?>