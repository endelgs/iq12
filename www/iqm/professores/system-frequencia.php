<?php
$alunos = IQ_Relatorio::getFrequencia($_SESSION['user']['id_professor']);
?>
<div class="header">
<p class="spacer"></p>
<h1>Frequ�ncia</h1></div>
<?php if(count($alunos) == 0): ?>
	<p>Voc� n�o tem orientandos</p>
<?php else:?>
<p class="relDescription">Aqui, voc� deve marcar apenas se a presen�a do aluno n�o est� OK. Caso ele compare�a regularmente, deixe o
marcador em branco. S� � poss�vel anotar a frequ�ncia do m�s imediatamente anterior ao atual. </p>
<h2>Exibindo frequ�ncia para o m�s <?php echo (date('m')-1).'/'.date('Y');?></h2>
<p class="margin-12" ></p>
<form action="iomanager.php" method="post">
	<input type="hidden" name="entity" value="IQ_Usuario" />
	<input type="hidden" name="action" value="salvaFaltas" />
	<table style="margin-left:20px;">
	
<tr>
	<th style="width:60px">N�o OK</th><th>Aluno</th>
</tr>	
<?php foreach($alunos as $aluno):?>
		<tr>
			<input type="hidden" name="params[id][]" value="<?php echo $aluno['id_pos_graduacao']?>" />
			<td><input name="params[aluno][<?php echo $aluno['id_pos_graduacao']?>]" value="1" type="checkbox" <?php if($aluno['nao_ok'])echo ' checked="checked" '?>/></td>
			<td><?php echo $aluno['nome']; ?></td>
		</tr>
	<?php endforeach;?>
	</table>
	<p><input type="submit" value="Salvar frequ�ncia" /></p>
	
</form>
<?php endif;?>