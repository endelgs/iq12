<?php 
define(ABSPATH, dirname(__FILE__)."/");
if(!function_exists('__autoload'))
{
	function __autoload($classname)
	{
		$classname = str_replace('_','/',$classname);
		$file = strtolower($classname).'.php';
		$path = dirname(__FILE__).'/lib/'.$file;
		if(file_exists($path))
			include_once($path);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo Config::HOMEURL."style.css"?>" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1;" /> 
<title>Sistema de P�s Gradua��o do Instituto de Qu�mica da UNICAMP</title>
</head>

<body id="page">
<div id="top">
	<?php include(ABSPATH."systemTopMenu.php");?>
</div>

<div id="tableMiddle">
	<div id="leftContent">
			<?php include(ABSPATH."systemLeftMenu.php");?>
	</div>
	<div id="rightContent">
		<?php include(ABSPATH."systemRightContent.php");?>
	</div>
</div>
</body>
</html>