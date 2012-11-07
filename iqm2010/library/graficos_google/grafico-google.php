<?php
require_once(APPLICATION_PATH . '/models/Table.php');

class Grafico {

    //$labelEixoX � o nome do eixo x
    //$labeEixoY � o nome do eixo y
    //header
    //$state cadeia de caracteres de estado, que est� em configuracoes -> avan�ado

    public static function get($labelEixoX, $labelEixoY, $arrDados, $header, $state, $arrayListagem, $headerListagem, $colunaFiltro="ano", $titulo="", $ordenar="", $width=750, $height=600) {



        $string = "\n";
        $stringImpressao = "";
        $stringTabelaTotais = "";
        $total = count($arrDados);
        $arrImpressao = array();
        $arrAnos = array();
        $i = 1;
        $anoAtual = $arrDados[0][$header['x']];
        $xAtual = "";
        $anos = array();
        foreach ($arrDados as $index => $a) { //
//            $string.="['" . $a[$header['x']] . "', " . $a[$header['z']] . ", " . $a[$header['y']] . "]\n";
//            if ($i != $total)
//                $string.=",";
            $i++;

            $arrImpressao[$a[$header['x']]][$a[$header['z']]] = $a[$header['y']];

            if (!in_array($a[$header['z']], $arrAnos))
                $arrAnos[] = $a[$header['z']];
        }

        sort($arrAnos);
        $primeiro = $arrAnos[0];
        $ultimo = $arrAnos[count($arrAnos) - 1];
        $arrJson = $arrImpressao;

        foreach ($arrJson as $index => $a) {
            ksort($a);
            $total = count($a);

            foreach ($a as $ano => $qtd) {

                if (empty($index))
                    $index = " ";

                if (empty($ano))
                    $index = "0";

                if (empty($qtd))
                    $index = "0";

                $string.="['" . $index . "', " . $ano . ", " . $qtd . "],";
            }

            foreach ($arrAnos as $ano) {

                if (!array_key_exists($ano, $a)) {

                    if (empty($index))
                        $index = " ";

                    if (empty($ano))
                        $index = "0";

                    $string.="['" . $index . "', " . $ano . ", 0],";
                }
            }
        }
        $string = substr($string, 0, strlen($string) - 1);

        $totalAnos = (count($arrAnos));
        $i = 1;
        $fechar = "";

        $stringTabelaTotais = "<table border='0' >";
        $i = 0;
        ksort($arrDados);
        $arrDados = array_sort($arrDados, $header['z'], SORT_ASC);
        $inicio = (int) $_REQUEST['inicio'];
        $fim = (int) $_REQUEST['fim'];
        foreach ($arrDados as $index => $a) {
            if ($a[$header['z']] >= $inicio && $a[$header['z']] <= $fim) {
                if ($anoAtual != $a[$header['z']]) {
                    $stringTabelaTotais.=$fechar;
                    if ($i % 2 == 0) {
                        $stringTabelaTotais.="<tr>";
                    }
                    $stringTabelaTotais.= "<td valign='top'><table class='hor-minimalist-b'>";
                    $stringTabelaTotais.="<tr><th colspan='2'>" . $a[$header['z']] . "</th></tr>\n";
                    $fechar = "</table></td>\n\n";
                    if ($i % 2 != 0)
                        $stringTabelaTotais.="</tr>";
                    $i++;
                }

                $stringTabelaTotais.="<tr><td>" . $a[$header['x']] . "</td>\n";
                $stringTabelaTotais.="<td style='text-align: right;'>" . $a[$header['y']] . "</td></tr>\n";
                $anoAtual = $a[$header['z']];
            }
        }
        $stringTabelaTotais.="</table></table>";

        $stringImp = "";
        $arrFImp = array();
        foreach ($arrImpressao as $index => $a) {
            ksort($a);
            $arrFImp[$index] = $a;
        }
        $i = 1;
        $inicio = (int) $_REQUEST['inicio'];
        $fim = (int) $_REQUEST['fim'];

        foreach ($arrFImp as $index => $a) {

            $stringImp.="['" . trim($index) . "', ";
//            foreach ($a as $index => $value) {
//                $anoAtual = (int) $index;
//                if ($anoAtual >= $inicio && $anoAtual <= $fim) {
//
//                    $stringImp.=$value . ',';
//                }
//            }
            for ($i = $inicio; $i <= $fim; $i++) {
                if (in_array($i, $arrAnos)) {
                    $anoAtual = (int) $i;
                    $qtd = $a[$i];
                    if ($a[$i] == "")
                        $qtd = 0;
                    $stringImp.=$qtd . ',';
                }
            }
            $stringImp = substr($stringImp, 0, strlen($stringImp) - 1);
            $stringImp.="]";
            if ($i != count($arrFImp))
                $stringImp.=", ";
            $i++;
        }
        $stringAnos = "[";
        for ($i = $inicio; $i <= $fim; $i++) {

            if (in_array($i, $arrAnos)) {
                $stringAnos.=$i;

                if ($i != $fim)
                    $stringAnos.=",";
            }
        }
        $stringAnos.="]";


        ob_start();

        $idDiv = rand(10000, 99999);

        //$headerListagem, $colunaFiltro

        $arrFinalListagem = array();
        foreach ($arrayListagem as $index => $a) {

            $anoAtual = (int) $a[$colunaFiltro];

            if ($anoAtual >= $inicio && $anoAtual <= $fim) {


                $arrFinalListagem[] = $a;
            }
        }

        if ($ordenar != "")
            $arrFinalListagem = array_sort($arrFinalListagem, $ordenar, SORT_ASC);

        $arrFinalListagem = array_sort($arrFinalListagem, $colunaFiltro, SORT_ASC);

        $listagemHtml = geraListaPadrao($arrFinalListagem, $headerListagem, 'listagem', 'hor-minimalist-b', true);
        ?>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.6.min.js"></script>
        <script type="text/javascript">            
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
            google.load('visualization', '1', {'packages':['motionchart']});	
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    	  
            var chart= null;
            function drawChart() {
                var data = new google.visualization.DataTable();
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            		
                data.addColumn('string', '<?php echo $labelEixoX; ?>');		//agrupar X
                data.addColumn('number', 'Rolagem');                            //filtro rolagem            
                data.addColumn('number', '<?php echo $labelEixoY; ?>');    	//eixo Y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            	   
                data.addRows([ <?php echo $string; ?> ]);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            	
                options = new Array();
                options['state']='<?php echo $state; ?>';
                options['width']=<?php echo $width; ?>;
                options['height']=<?php echo $height; ?>;
                chart = new google.visualization.MotionChart(document.getElementById('<?php echo $idDiv; ?>')); 
                chart.draw(data, options);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
            }
            google.setOnLoadCallback(drawChart);	
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
        </script>
        <script type="text/javascript">
                                                                                                                                                                                                               
                                                                                                                                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                                                                                                                                                                                                                
            function imprimir(){
                                                                                                                                                                                                                                                                                                                                                                
                inicio=$('#txtInicio').val();
                fim = $('#txtFim').val();  
                                                                                                                                                                                                                                                                                              
                inicioPossivel = <?php echo $primeiro; ?> ;
                fimPossivel = <?php echo $ultimo; ?> ;
                                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                                                     
                listagem=$('#list').is(':checked'); 
                legenda=$('#legenda').is(':checked'); 
                                                                                                                                                                                                                                                                                                                                            
                url=window.document.URL;
                url = url + '?inicio='+inicio+'&fim='+fim+'&listagem='+listagem +'&legenda='+legenda;
                                                                                                                                                                                                                                                                                    
                if(inicio>=inicioPossivel && fim<=fimPossivel && inicio<=fim && inicio<=fimPossivel && fim>=inicioPossivel){
                    window.open(url ,'page','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=0,width=900,height=600');  
                }else {
                                                                                                                                                                                                                                                                                   
                    alert("Período inválido!\n" + "Utilize "+ inicioPossivel+ " a "+ fimPossivel);
                }                                                               
            }
                                                                                                                                                                                                                                                                                                                                                                                                                    

        </script>
        <style type="text/css">

            .hor-minimalist-b
            {
                font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
                font-size: 12px;
                background: #fff;
                margin: 15px;
                width: 350px;
                border-collapse: collapse;
                text-align: left;
            }
            .hor-minimalist-b th
            {
                font-size: 14px;
                font-weight: normal;
                color: #333;
                padding: 4px 10px;
                border-bottom: 2px solid #999;
                border-top:2px solid #999;
            }
            .hor-minimalist-b td
            {
                border-bottom: 1px solid #ccc;
                color: #444;
                padding: 4px 6px;
            }
            .hor-minimalist-b tbody tr:hover td
            {
                color: #009;
            }

            #listagem{

                width: 745px;

            }

        </style>


        <?php
        $html = ob_get_contents();
        ob_end_clean();

        if ($_REQUEST['inicio'] && $_REQUEST['fim']) {
            echo $html;
            ?>
            <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
            <script type="text/javascript">
                google.load('visualization', '1', {packages: ['corechart']});
                var years = "";
                function drawVisualization() {
                    // Create and populate the data table.
                    var data = new google.visualization.DataTable();
                    var raw_data = [<?php echo $stringImp; ?>];
                    years= <?php echo $stringAnos; ?>;  
                    data.addColumn('string', 'Year');
                    for (var i = 0; i  < raw_data.length; ++i) {
                        data.addColumn('number', raw_data[i][0]);    
                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                    data.addRows(years.length);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
                    for (var j = 0; j < years.length; ++j) {    
                        data.setValue(j, 0, years[j].toString());    
                    }
                    for (var i = 0; i  < raw_data.length; ++i) {
                        for (var j = 1; j  < raw_data[i].length; ++j) {
                            data.setValue(j-1, i+1, raw_data[i][j]);    
                        }
                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                    // Create and draw the visualization.
                    new google.visualization.ColumnChart(document.getElementById('visualization')).
                        draw(data,
                    {title:"<?php echo $titulo; ?>", 
                        width:850, height:400,
                        hAxis: {title: "Ano"}}
                );
                }                                                                                                                                                                                   
                google.setOnLoadCallback(drawVisualization);
                                                                                                                                                                                                                                                                                                                
                window.print();
            </script>
            <div id="areaImpressao" >
                <div onclick="window.print();" style="cursor: pointer;text-align: right;text-decoration: underline;">Imprimir</div>
                <div id="visualization" style="width: 600px; height: 400px;"></div>
                <?php if ($_REQUEST["legenda"] == "true")
                    echo $stringTabelaTotais; ?> 
                <?php if ($_REQUEST["listagem"] == "true")
                    echo $listagemHtml; ?>
            </div>
            <?php
            exit;
        }
        //  
        ob_start();
        ?>

        <div id="<?php echo $idDiv; ?>" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"></div>
        <br/>
        <div >    
            <h4> Impressão: </h4>
            <p>
                <label for="legenda" > Imprimir legenda do gráfico? </label>
                <input type="checkbox" name="legenda" id="legenda" checked="checked"/>
            </p>
            <p>
                <label for="list"> Imprimir listagem de dados? </label>
                <input type="checkbox" name="list" id="list" checked="checked" />
            </p>

            <label for="txtInicio"> Periodo de </label>
            <input type="text" name="txtInicio" id="txtInicio" value="<?php echo $primeiro; ?>" />
            <label for="txtFim"> a </label>
            <input type="text" name="txtFim" id="txtFim" value="<?php echo $ultimo; ?>" />
            <br/><br/>
            <input type="submit" value="Imprimir" onclick="imprimir();"/>
        </div>
        </br>
        <?php
        $html.= ob_get_contents();
        ob_end_clean();
        return $html;
    }

}

function array_sort($array, $on, $order=SORT_ASC, $numerico=false) {
    $new_array = array();
    $sortable_array = array(); //$numerico

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                if ($numerico) {
                    asort($sortable_array);
                } else {

                    asort($sortable_array, SORT_NUMERIC);
                }
                break;
            case SORT_DESC:
                if ($numerico) {
                    arsort($sortable_array);
                } else {
                    arsort($sortable_array, SORT_NUMERIC);
                }
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function geraListaPadrao($records, $header, $idTable="", $class ="", $exibirHeader=true) {

    if (empty($records))
        return '';

    $html = '<table border="0" class="' . $class . '" id="' . $idTable . '">';

    if (!is_array($records))
        $records = $records->toArray();

    $inicial = $records;
    $records = array();
    forEach ($inicial as $indexI => $r) {
        foreach ($header as $indexH => $h) {
            $records[$indexI][$indexH] = $r[$indexH];
        }
    }

    if ($exibirHeader) {
        $html .= '<thead>';
        foreach ($header as $index => $v) {
            $html.='<th align="left">' . $v . '</th>';
        }

        $html .= '</thead>';
    }

    foreach ($records as $record) {

        $html.='<tr>';
        foreach ($record as $name => $field) {
            if (array_key_exists($name, $header))
                $html.='<td >' . $field . '</td>';
        }


        $html.= '</tr>';
    }
    $html.='</table>';

    return $html;
}

//
////array é igual a retornada pelo mysql
//$array = array();
//$array[0] = array(
//    'agencia' => 'Ag�ncia 1',
//    'ano' => '2010',
//    'defesas' => '1000',
//);
//
//$array[1] = array(
//    'agencia' => 'Agência 2',
//    'ano' => '2010',
//    'defesas' => '1150',
//);
//
//$array[2] = array(
//    'agencia' => 'Agência 3',
//    'ano' => '2010',
//    'defesas' => '300',
//);
//
//$array[3] = array(
//    'agencia' => 'Agência 1',
//    'ano' => '2011',
//    'defesas' => '1200',
//);
//
//$array[4] = array(
//    'agencia' => 'Agência 2',
//    'ano' => '2011',
//    'defesas' => '750',
//);
//
//$array[5] = array(
//    'agencia' => 'Agência 3',
//    'ano' => '2011',
//    'defesas' => '850',
//);
//
//$header = array('x' => 'agencia',
//    'y' => 'defesas',
//    'z' => 'ano'
//);
//$state = '{"colorOption":"_UNIQUE_COLOR","xZoomedIn":false,"nonSelectedAlpha":0.4,"yLambda":1,"showTrails":false,"sizeOption":"_UNISIZE","dimensions":{"iconDimensions":["dim0"]},"yAxisOption":"2","playDuration":27438.88888888889,"iconKeySettings":[{"key":{"dim0":"Ag�ncia 2"}},{"key":{"dim0":"Ag�ncia 3"}},{"key":{"dim0":"Ag�ncia 1"}}],"yZoomedIn":false,"xLambda":1,"yZoomedDataMin":0,"xZoomedDataMin":0,"orderedByX":true,"iconType":"VBAR","uniColorForNonSelected":false,"time":"2010","duration":{"multiplier":1,"timeUnit":"Y"},"orderedByY":false,"xAxisOption":"_ALPHABETICAL","yZoomedDataMax":1300,"xZoomedDataMax":3}';
//$labelEixoX = 'Agência';
//$labelEixoY = 'Defesas';
//echo Grafico::get($labelEixoX, $labelEixoY, $array, $header, $state);
?>
