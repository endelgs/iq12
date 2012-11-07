<?php $aluno = IQ_Relatorio::getAlunos(base64_decode($_GET['u'])); ?>
<h1 class="header">Perfil de Aluno</h1>
<p class="margin-15"></p>
<table id="tablePerfilAluno">
	<tr>
		<td class="colunaEsquerda alunoNome" colspan="2" ><b>Nome</b></td>
		<td class="alunoSexo"><b>Sexo</b></td>
		<td class="colunaDireita alunoRA"><b>RA</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda alunoNome" colspan="2" ><?php echo $aluno['nome'];?></td>
		<td class="alunoSexo"><?php echo $aluno['sexo'];?></td>
		<td class="colunaDireita alunoRA"><?php echo $aluno['ra'];?></td>
	</tr>
	<!-- 
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaEsquerda alunoNascimento" colspan="2" ><b>Nascimento</b></td>
		<td class="colunaDireita alunoNacionalidade" colspan="2" ><b>Nacionalidade</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda alunoNascimento" colspan="2" ><?php echo $aluno['nascimento']; ?></td>
		<td class="colunaDireita alunoNacionalidade" colspan="2" ><?php echo $aluno['nacionalidade']; ?></td>
	</tr>
	 -->
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaCheia alunoEmailP" colspan="4"><b>Email principal</b></td>
	</tr>
	<tr>
		<td class="colunaCheia alunoEmailP" colspan="4"><?php echo $aluno['email_principal'] ?></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaCheia alunoEmailS" colspan="4"><b>Email secundário</b></td>
	</tr>
	<tr>
		<td class="colunaCheia alunoEmailS" colspan="4"><?php echo $aluno['email_secundario'] ?></td>
	</tr>
	<!-- 
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="colunaEsquerda alunoCidade"><b>Cidade</b></td>
		<td class="alunoUF"><b>UF</b></td>
		<td class="colunaDireita alunoRG"><b>RG</b></td>
		<td class="colunaEsquerda alunoCPF"><b>CPF</b></td>
	</tr>
	<tr>
		<td class="colunaEsquerda alunoCidade"></td>
		<td class="alunoUF"></td>
		<td class="colunaDireita alunoRG"></td>
		<td class="colunaEsquerda alunoCPF"></td>
	</tr>
	<tr><td colspan="4"></td></tr>
	<tr>
		<td class="alunoRNE"></td>
		<td class="colunaDireita alunoPassaporte"></td>
	</tr>
	 -->
</table>