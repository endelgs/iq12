<?php
function normalize($str){
  $a = array("ã","â","á","à","ä","Ã","Â","Á","À","Ä");
  $e = array("ẽ","ê","é","è","ë","Ẽ","Ê","É","È","Ë");
  $i = array("í","ì","ĩ","î","ï","Í","Ì","Ĩ","Î","Ï");
  $o = array("ó","ò","õ","ô","ö","Ó","Ò","Õ","Ô","Ö");
  $u = array("ú","ù","û","ũ","ü","Ú","Ù","Û","Ũ","Ü");
  $c = array("ç","Ç");
  $str = strtolower($str);
  
  foreach($a as $v)
    $str = str_replace($v,"a",$str);
  foreach($e as $v)
    $str = str_replace($v,"e",$str);
  foreach($i as $v)
    $str = str_replace($v,"i",$str);
  foreach($o as $v)
    $str = str_replace($v,"o",$str);
  foreach($u as $v)
    $str = str_replace($v,"u",$str);
  foreach($c as $v)
    $str = str_replace($v,"c",$str);
    
  $str = preg_replace("|<[^>]+>|","",$str);
  return $str;
}
function grid($arrDados, $headers, $isCrud, $exclude, $doubleClick, $functionDelete, $height) {
	
	if($arrDados)
	foreach($arrDados as $index=>$a){
		foreach($a as $index2=>$v)
		{	

			
			$arrDados[$index][$index2]=str_replace("'", '',$v);
			$arrDados[$index][$index2]=str_replace(':', '&#58;',$arrDados[$index][$index2]);
			$arrDados[$index][$index2]=$arrDados[$index][$index2];
			$arrDados[$index][$index2]=str_replace('&quot;', ' ',$arrDados[$index][$index2]);
			$arrDados[$index][$index2]=$arrDados[$index][$index2];
			$arrDados[$index][$index2]=str_replace("\r\n", '  ',$arrDados[$index][$index2]);

		}
	}
	
	//$arrDados= array_unique($arrDados);

    $arrDeTipos = array();
    $jSonObj = "";

    if ($height == "")
        $height = 300;
    $arrayOriginal = $arrDados;
    $id = md5(microtime());
//  
    // ordena array pelo header
    if (is_array($headers) && is_array($arrDados) && $arrDados && $arrDados != array() && is_array($arrDados[0])) {
        $inicial = $arrDados;
        if ($inicial && is_array($inicial)) {
            $arrDados = array();
            forEach ($inicial as $indexI => $r) {
                foreach ($headers as $indexH => $h) {
                    $arrDados[$indexI][$indexH] = $r[$indexH];
                }
            }
        }
    }
//cols
    $jSonObj.="{cols: [";
    $cols = array();
    $showNumber = 'showRowNumber: true,';

    if (!is_array($arrDados) || !is_array($arrDados[0])) {

        $arrDados = array();
        foreach ($headers as $index => $label) {
            $arrAux[$index] = " ";
        }
        $isCrud = false;
        $showNumber = ' ';
        $arrDados[0] = $arrAux;
    }


    foreach ($arrDados[0] as $index => $t) {
        $tipo = "string";

        $header = $headers[$index];
        if (is_numeric($t))
            $tipo = "number";
        if($index=="RA"){
                $tipo="string";
        }      
        
        if ($header == '')
            $header = $index;

        if (!is_array($exclude) || !in_array($index, $exclude)) {
            $cols[] = "{id: '$index', label: '" . $header . "', type: '$tipo'}";
        }

        $arrDeTipos[$index] = $tipo;
    }

    if ($isCrud) {
        if ($doubleClick != "")
            $cols[] = "{id: 'editar', label: ' ', type: 'string'}";
        if ($functionDelete != "")
            $cols[] = "{id: 'excluir', label: ' ', type: 'string'}";
    }
    $jSonObj.=implode(",", $cols);
    $jSonObj.="],";

//rows
    $jSonObj.=" rows: [";
    $rows = array();
    foreach ($arrDados as $index => $value) {
        $rows[$index] = " {c:[";
        unset($subrows);
        foreach ($value as $index2 => $value2) {
            if (!is_array($exclude) || !in_array($index2, $exclude)) {
                $aspas = "";
                if ($arrDeTipos[$index2] != "number") {
                    $aspas = "'";
                } else {
                    if (empty($value2))
                        $value2 = 0;

                    $value2 = ((int) $value2)+0;
                }
                  

                $subrows[].="{v: ".$aspas.normalize(addslashes($value2)).$aspas.",f: " . $aspas . addslashes($value2) . $aspas . "}";
            }
        }
        if ($isCrud) {
            $class = 'data {';
            foreach ($arrayOriginal[$index] as $index2 => $value2) {
                $index2 = $index2;
                $value2 = str_replace(",", "~#044~", $value2);
                $class .= "{$index2}:'" . str_replace("\r\n", " ", $value2) . "',";
            }
            $class = substr($class, 0, strlen($class) - 1) . '}';

            if ($doubleClick != "")
                $subrows[].="{v: '" . '<a  style="cursor:pointer;" class="' . addslashes($class) . '" onClick="' .
                        $doubleClick . ';subtituirVirgula();"><span class="edite-button">&nbsp;</span></a>' . "'}";
            //onclick="javascript:return confirm('Are you sure you want to delete this POC?')"
//            $subrows[].="{v: '" . '<a  style="cursor:pointer;" class="' . addslashes($class) . '" onclick="doDelete(this.className,'."\'".$functionDelete."\'".');subtituirVirgula();">Excluir</a>' . "'}";
            if ($functionDelete != "")
                $subrows[].="{v: '" . '<a style="cursor:pointer;" class="' . addslashes($class) . '" onclick="javascript:if(confirm(' . "\'" . 'Deseja realmente excluir este dado?' . "\'" . '))' . $functionDelete . '(this.className);"><span class="x-button">&nbsp;</span></a>' . "'}";
        }

        $rows[$index] .=implode(", ", $subrows) . "]}";
    }
    $jSonObj.=implode(", ", $rows) . "]}";

    ob_start();
    ?>
    <script type="text/javascript">
        google.load('visualization', '1', {packages: ['table']});
    </script>
    <script type="text/javascript">
        function drawVisualization<?php echo $id; ?>() {
            var JSONObject =<?php echo $jSonObj; ?>;

            var data = new google.visualization.DataTable(JSONObject, 0.5);
            visualization = new google.visualization.Table(document.getElementById('<?php echo $id; ?>'));
            visualization.draw(data,{allowHtml: true,  <?php echo $showNumber; ?> height: <?php echo $height; ?>, width: '100%', page: 'enable', pageSize: 10});
        }
        google.setOnLoadCallback(drawVisualization<?php echo $id; ?>);
    </script>
    <div id="<?php echo $id; ?>" ></div>
    <style type="text/css">
        .google-visualization-table-table{

            font-family: Verdana;
        }
    </style>
    <?php
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}
?>