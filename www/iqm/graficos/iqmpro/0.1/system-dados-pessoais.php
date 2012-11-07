<?php $professor = IQ_Usuario::getUserInfo($_SESSION['user']['id_pessoa']); ?>
<h1 class="header">Dados Pessoais</h1>
<p class="margin-15"></p>
<table id="tablePerfilProfessor">
	<tr>
		<td class="colunaEsquerda professorNome" colspan="2" ><b>Nome</b></td>
		<td class="professorSexo"><b>Sexo</b></td>
		<td class="colunaDireita professorRA"><b>Estado Civil</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda professorNome" colspan="2" ><?php echo $professor['nome'];?></td>
		<td ><?php echo $professor['sexo'];?></td>
		<?php 
		$estado = IQ_Usuario::getEstadoCivil($professor['estado_civil']);
		$estado = $estado['estado_civil'];
		
		
		if ($professor['sexo'] == "M")
			$estado = str_replace("(a)", "", $estado);
		else{
			if (strpos($estado, "(a)") !== false){
				$estado[strpos($estado, "(a)")-1] = "a";
				$estado = str_replace("(a)", "", $estado);
			}
		}?>
		<td class="colunaDireita professorEstado"><?php echo $estado;?></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaEsquerda professorRG" colspan="2"><b>RG</b></td>
		<td class="colunaDireita professorCPF" colspan="2"><b>CPF</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda professorRG" colspan="2"><?php echo $professor['rg'];?></td>
		<td class="colunaDireita professorCPF" colspan="2"><?php echo StringUtil::cpfHumanizado($professor['cpf'])?></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaEsquerda professorNascimento" ><b>Data Nascimento</b></td>
		<td class="professorCidadeNascimento" colspan="2"><b>Local Nascimento</b></td>
		<td class="colunaDireita professorNacionalidade" ><b>Nacionalidade</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda professorNascimento" ><?php echo $professor['nascimento']; ?></td>
		<td class="professorCidadeNascimento" colspan="2"><?php echo $professor['municipio']; ?></td>
		<td class="colunaDireita professorNacionalidade" ><?php echo $professor['nacionalidade']; ?></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaCheia professorEmailP" colspan="4"><b>Email principal</b></td>
	</tr>
	<tr>
		<td class="colunaCheia professorEmailP" colspan="4"><?php echo $professor['email_principal'] ?></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaCheia professorEmailS" colspan="4"><b>Email secundário</b></td>
	</tr>
	<tr>
		<td class="colunaCheia professorEmailS" colspan="4"><?php echo $professor['email_secundario'] ?></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaEsquerda professorUniversidade"><b>Universidade</b></td>
		<td class="professorUnidade"><b>Unidade</b></td>
		<td class="professorDepartamento"><b>Departamento</b></td>
		<td class="colunaDireita professorExterno"><b>Professor Externo</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda professorUniversidade"><?php echo $professor['universidade'];?></td>
		<td class="professorUnidade"><?php //echo $professor['unidade']; ?></td>
		<td class="professorDepartamento"><?php //echo $professor['departamento']; ?></td>
		<td class="colunaDireita professorExterno"><?php //echo $professor['externo'] == '1' ? 'Sim' : "Não"; ?></td>
	</tr>
</table>