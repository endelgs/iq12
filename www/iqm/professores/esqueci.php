<?php include 'header.php'?>
<table id="loginTable" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" id="loginHeader">
			<?php
			if ($_SESSION['message'] != "")
			{
				echo $_SESSION['message'];
				unset($_SESSION['message']);
			}
			?>
		</td>
	</tr>
	<tr>
		<td id="loginLeftMenu">
		</td>
		<td id="loginRightMenu">
			<h1>Sistema de Pós Graduação<br/>do Instituto de Química da UNICAMP</h1>
			<h5>Versão para professor</h5>
			<form method="post" action="iomanager.php" >
				<input type="hidden" name="entity" value="IQ_Usuario" />
				<input type="hidden" name="action" value="recuperaSenha" />
				<h3>Esqueci a senha</h3>
				<p style="width:220px; margin:auto; color: #01536b;">Sua nova senha será enviada para o e-mail especificado abaixo</p>
				<p style="height:10px; "> </p>
				<table style="width:220px;">
					<tr>
						<td class="desc">E-mail</td>
						<td class="input"><input type="text" name="params[login]" value=""/><br/></td>
					</tr>
				</table>
				<table>
					
					<tr>
						<td class="forgotPass"></td>
						<td class="submit"><input type="submit" value="OK" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include 'footer.php'?>