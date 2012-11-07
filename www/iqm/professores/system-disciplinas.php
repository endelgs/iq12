<?php $disciplinas = IQ_Relatorio::getDisciplinasProfessoresOrdenados();?>
<div class="header">
	<p class="spacer"></p>
	<h1>Disciplinas Ministradas</h1>
</div>
<p class="relDescription">Este relatório mostra as disciplinas que você lecionará durante o ano atual. Caso você seja o coordenador da
disciplina, aparecerá um indicador especial.</p>
<?php 
if(is_array($disciplinas))
	foreach($disciplinas as $disciplina): ?>
	<div style="margin-top:10px;">
		<h2><?php echo $disciplina['codigo'].' - '.$disciplina['titulo'].' - '.$disciplina['ano'].'/'.$disciplina['periodo']; ?></h2>
		<p style="margin-top:5px"><?php echo $disciplina['subtitulo']?></p>
		<p>Oferecimento: <?php echo $disciplina['oferecimento_disciplina']?></p>
		<p>Turma: <?php echo $disciplina['turma']?></p>
		<p>Horário: <?php echo $disciplina['horario']?></p>
		<?php if($disciplina['coordenador']) echo '<p style="font-weight:bold">Você é coordenador dessa disciplina</p>'; ?>
	</div>
<?php endforeach; ?>
<?php 

//Criei esta funcao para evitar repetição de codigo, conforme o codigo vai lendo as disciplinas ele bufferiza as turmas
//sempre que outra disciplina vai ser lida, as turmas da disciplina anterior deve ser escrita na tela.
//Esta funcao imprime as turmas caso exista alguma, caso não exista ela checa se há algum <h4> aberto, se houver ele
//será fechado.

/*function limpaTurmas(){
	global $turmas;
	global $h4aberto;
	if (!empty($turmas)){
		echo " - ";
		if (count($turmas) > 1){
			echo "Turmas {$turmas[0]}";
			for ($i = 1; $i < count($turmas); $i++){
				if ($i == count($turmas)-1)
					echo " e {$turmas[$i]}";
				else
					echo ", {$turmas[$i]}";
			}
		}else
			echo "Turma {$turmas[0]}";
		echo "</h4>\n";
		$h4aberto = false;
		echo "<p class=\"margin-12\"></p>\n";
	}else{
		//por enquanto isto nunca ocorre, mas por segurança está aqui.
		if ($h4aberto){
			echo "</h4>\n";
			$h4aberto = false;
			echo "<p class=\"margin-12\"></p>\n";
		}
	}
	$turmas = array();
}

$prevAno = -1;
$prevPeriodo = -1;
$prevID = -1;
$turmas = array();
$h4aberto = false;
foreach($disciplinas as $disciplina):
	//Novo ano
	if ($prevAno != $disciplina['ano']){
		limpaTurmas();
		$prevAno = $disciplina['ano'];
		$prevPeriodo = -1;
		$prevID = -1;
		echo "<h2>".$disciplina['ano']."</h2>\n";
		echo '<p class="margin-12"></p>';	
	}
	//Novo período
	if ($prevPeriodo != $disciplina['periodo']){
		limpaTurmas();
		$prevID = -1;
		$prevPeriodo = $disciplina['periodo'];
		echo "<h3 class=\"disciplinaPeriodo\">".$disciplina['periodo']."</h3>\n";
		echo '<p class="margin-12"></p>';
	}
	//Nova disciplina
	if ($prevID != $disciplina['id_disciplina']){
		limpaTurmas();
		$prevID = $disciplina['id_disciplina'];
		echo "<h4 class=\"disciplina\">".$disciplina['disciplina'];
		$h4aberto = true;
	}
	//Adicionando as turmas no array
	$turmas[] = $disciplina['turma'];
	
//  $alunos = getAlunosDisciplina($disciplina['id_disciplina'], $disciplina['periodo'], $disciplina['turma'], $disciplina['ano']);
//	echo "<h3>{$disciplina['periodo']} de {$disciplina['ano']} - Turma ".$disciplina['turma']."</h3>\n";
//	echo "<p class=\"margin-12\"></p>\n";
//	if (empty($alunos)){
//		echo '<h3 class="disciplinaAluno">Não há alunos nesta disciplina.</h3>';
//	}else{
//		echo "<table>\n";
//		foreach($alunos as $aluno){
//			echo "<tr>\n";
//			echo "<td class=\"disciplinaAluno\"><a href=\"".HOMEURL."?q=".PROFILES."&amp;u=".base64_encode($aluno['id_pessoa'])."\" title=\"Perfil do aluno\" >{$aluno['nome']}</a></td>";
//			echo "</tr>\n";
//		}
//		echo "</table>\n";
//	}
//	echo "<p class=\"margin-12\"></p>\n";
endforeach;

limpaTurmas();
*/
?>
<p class="margin-15"></p>