
<?php
if(isset($_REQUEST['action']))
	 $metodo = $_REQUEST['action'];

$url=explode('/',$_SERVER['HTTP_REFERER']);

forEach ($url as $index=>$u)
{
	if ($index<(count($url)-7))
	 $site.=$u.'/';
}

$site.='iqm2010/'
?>

<!-- saved from url=(0014)about:internet -->
<!-- 
Smart developers always View Source. 

This application was built using Adobe Flex, an open source framework
for building rich Internet applications that get delivered via the
Flash Player or to desktops via Adobe AIR. 

Learn more about Flex at http://flex.org 
// -->




<!--  BEGIN Browser History required section -->
<link rel="stylesheet" type="text/css" href="history/history.css" />
<!--  END Browser History required section -->

<title></title>
<script src="AC_OETags.js" language="javascript"></script>

<!--  BEGIN Browser History required section -->
<script src="history/history.js" language="javascript"></script>
<!--  END Browser History required section -->
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 124;
// -----------------------------------------------------------------------------
// -->
</script>

<body scroll="no" style="z-index: 1;background: #FFF;">
<script language="JavaScript" type="text/javascript">

// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);


	if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
			"src", "Main",
			"width", "800",
			"height", "600",
			"align", "middle",
			"FlashVars", "&s=<?php echo $site;?>&p=library/graficos/iqm-estatisticas/0.3/&e=IQ_Estatisticas&a=<?php echo $metodo;?>",
			"id", "Main",
			"quality", "high",
			"bgcolor", "#F00",
			"name", "Main",
			"wmode", "transparent",
			"allowScriptAccess","sameDomain",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
  }
// -->
</script>

</body>
