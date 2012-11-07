<?php

include 'lib/flexuploader.php';

// Instancio o uploader
$uploader = new FlexUploader();

// Seto o caminho
$uploader->path = 'upload/';
// E o nome do arquivo
$uploader->fileName = 'screenshot_'.date('Ymdhis').'.jpg';
// Passo o arquivo em Base64
$uploader->fileContents = $_REQUEST['fileContents'];

// Mando salvar
$uploader->save();
$uploader->chmod("777"); 

echo '<?xml version="1.0" encoding="utf-8"?><response><filename>'.$uploader->fileName.'</filename></response>';