<?php 
include_once 'lib/config.php';
include_once 'lib/stringutil.php';
switch($_GET['q'])
{
	case Config::SYSTEMHOME:
		include(ABSPATH."system-home.php");
		break;
	case Config::RELATORIOS:
		include(ABSPATH."system-relatorios.php");
		break;
	case Config::ORIENTADOS:
		include(ABSPATH."system-orientados.php");
		break;
	case Config::ORIENTANDOS:
		include(ABSPATH."system-orientandos.php");
		break;
	case Config::DISCIPLINAS:
		include(ABSPATH."system-disciplinas.php");
		break;
	case Config::BOLSAS:
		include(ABSPATH."system-bolsas.php");
		break;
	case Config::BANCAS:
		include(ABSPATH."system-bancas.php");
		break;
	case Config::FREQUENCIA:
		include(ABSPATH."system-frequencia.php");
		break;
	case Config::UMRELATORIO:
		include(ABSPATH."system-profile-relatorio.php");
		break;
	case Config::PROFILES:
		include(ABSPATH."system-profile-aluno.php");
		break;
	case Config::PERFILDEBOLSA:
		include(ABSPATH."system-profile-bolsa.php");
		break;
	case Config::DADOSPESSOAIS:
		include(ABSPATH."system-dados-pessoais.php");
		break;
	default:
		include(ABSPATH."system-404.php");
		break;
}?>