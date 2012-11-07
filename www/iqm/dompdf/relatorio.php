<?php
require_once("dompdf_config.inc.php");
$html = $_POST['relatorio'];
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream('relatorio.pdf');
?>