<?php 
define(ABSPATH, dirname(__FILE__)."/");

//session_start();
include(ABSPATH."functions.php");
include(ABSPATH."header.php");

if ($_SESSION['loggedin']){
	//timeout se a conta ficar inativa por muito tempo (10 minutos)
	if (time() - $_SESSION['time'] > 60*10)
	{
		$_SESSION['loggedin'] = false;
		$_SESSION['message'] = "<p class=\"center\">Sua conexão foi terminada devido a inatividade.</p>";
	}
	if (isset($_GET['logout']) && $_GET['logout'] == 1)
	{
		$_SESSION['loggedin'] = false;
		$_SESSION['message'] = "<p class=\"center\">Sessão encerrada.</p>";
	}
}
if ($_SESSION['loggedin'])
{
	$_SESSION['time'] = time();
	include(ABSPATH."system-front-page.php");
}
else
{
?>
<!-- <center>  -->
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
			<h5>Para acesso exclusivo dos docentes</h5>
			<form method="post" action="iomanager.php" >
				<input type="hidden" name="entity" value="IQ_Usuario" />
				<input type="hidden" name="action" value="login" />
				<h3>Seja Bem-vindo</h3>
				<table>
					<tr>
						<td class="desc">Login:</td>
						<td class="input"><input type="text" name="params[login]" value="" /><br/></td>
					</tr>
					<tr>
						<td class="desc">Senha:</td>
						<td class="input"><input type="password" name="params[senha]" value="" /></td>
					</tr>
				</table>
				<table>
					<tr>
						<td class="forgotPass"><a href="<?php echo Config::HOMEURL.'esqueci.php';?>" >Não tenho / Esqueci minha senha</a></td>
						<td class="submit"><input type="submit" value="OK" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<!-- </center>  -->
<?php
}
include(ABSPATH."footer.php");
?>