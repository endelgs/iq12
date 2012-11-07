<?php

require_once(APPLICATION_PATH . '/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH . '/models/Defesa.php');

class Application_Models_Alunos extends Zend_Db_Table_Abstract {

    protected $_name = 'v_ingresso_reingresso';
    protected $_primary = 'id_pos_graduacao';

    function CRUDread() {
        return($this->fetchAll($this->select()->order('id_pos_graduacao'))->toArray());
    }

    function todosAnosNotaBaixa($tipo) {
        $this->_name = 'v_alunos_nota_baixa';
        $this->_primary = 'ra';
        return($this->fetchAll($this->select()->where('id_tipo_curso=' . $tipo)->order('codigo'))->toArray());
    }

    function defesaCorrespondente($id_pos_graducao) {
        $Defesas = new Application_Models_Defesa();
        $arrDefesas = $Defesas->CRUDread($id_pos_graducao);

        return $arrDefesas;
    }

    function defesaAtivaParaDataEPos($id_pos_graducao, $dataDATETIME) {
        $Defesas = new Application_Models_Defesa();
        $arrDefesas = $Defesas->CRUDread($id_pos_graducao, $dataDATETIME);

        if (count($arrDefesas) == 0) {
            return false;
        }

        return true;
    }

    function jaExiste($array, $key, $valor) {
        forEach ($array as $a) {
            if ($a[$key] == $valor)
                return true;
        }
    }

    function alunosQueCancelaram($id_tipo_curso="", $periodo_inicio="", $periodo_fim="") {

        $where = '';
//        if ($periodo_inicio == "" && $periodo_fim == "")
        $array = $this->CRUDread();
//        else
//            $array = $this->CRUDWhere($where);

        $arrFinal = array();
        $Mask = new mascara();
        $dataInicio = $Mask->MascaraDataTodatetimeSQL($periodo_inicio);
        $dataFim = $Mask->MascaraDataTodatetimeSQL($periodo_fim);
        $dataInicio = strtotime($dataInicio);
        $dataFim = strtotime($dataFim);
        $dataB = date("Y-m-d");
        $dataB = strtotime($dataB);
        
 
        forEach ($array as $index => $a) {
            if (!$this->jaExiste($arrFinal, 'nome', $a['nome'])) {
                if ($id_tipo_curso == $a['id_tipo_curso'] || $id_tipo_curso == "") {
                    if ($a['id_reingresso'] != "") {
                        
                        
                        
                        $dataA = $a['data_integralizacao_reingresso'];

                        $dataA = strtotime($dataA);

                        $dataC = $a['data_reingresso'];

                        $dataC = strtotime($dataC);

                        if (($dataA <= $dataB && !empty ($dataC) && !empty ($dataA)) || ($a['aluno_desligado_reingresso'] == "1") ) {
                            if (!$this->defesaAtivaParaDataEPos($a['id_pos_graduacao'], $a['data_reingresso'])) {

                                if ($periodo_inicio == "" && $periodo_fim == "")
                                    $arrFinal[] = $a;
                                elseif ($dataC <= $dataFim && $dataC >= $dataInicio) {

                                    $arrFinal[] = $a;
                                }
                            }
                        }
                    } else {
                        
                        
                        $dataA = $a['data_integralizacao_ingresso'];
                        $dataC = $a['data_ingresso'];

                        $dataC = strtotime($dataC);
                        $dataA = strtotime($dataA);
                        if ((($dataA <= $dataB && !empty ($dataC) && !empty ($dataA)) || $a['aluno_desligado_ingresso'] == "1")  ) {
                            if (!$this->defesaAtivaParaDataEPos($a['id_pos_graduacao'], $a['data_ingresso'])) {
                                
                                if ($periodo_inicio == "" && $periodo_fim == "")
                                    $arrFinal[] = $a;
                                elseif ($dataA <= $dataFim && $dataA >= $dataInicio) {

                                    $arrFinal[] = $a;
                                }
                            }
                        }
                    }
                }
            }
        }

//        echo '<br>';
//        exit;
        return $arrFinal;
    }

    function CRUDWhere($where) {
        return($this->fetchAll($this->select()->where($where)->order('id_pos_graduacao'))->toArray());
    }

    function frequentouByPosEAnoMes($idpos, $mes, $ano) {
        $sql = "
SELECT count(*) as nao_frequencia
FROM `frequencia` 
WHERE `mes`='$ano-$mes-00'
AND `id_pos_graduacao`=$idpos";

        $dbAdapter1 = $this->getAdapter();
        $result = $dbAdapter1->query($sql);
        $result = $result->fetchAll();
        $qtd = $result[0]['nao_frequencia'];

        if ($qtd == 0 || $qtd == '' || $qtd == '0')
            return 1;
        else
            return 0;
    }

    function listaalunos($array=array()) {
        $dbAdapter = $this->getAdapter();
        $lista = array();
        $contador = 0;
        $contador2 = 0;

        if ($array['orderby'] == "")
            $orderby = 'id_pos, integralizacao DESC';
        else
            $orderby=$array['orderby'];

        if ($array['curso'] == 'todos') {
            $where = "WHERE pg.id_tipo_curso <> 1 ";
        } else {
            $where = "WHERE pg.id_tipo_curso = '" . $array['curso'] . "' ";
        }

        if ($array['periodo'] == 'nao') {
            $sql = "SELECT DISTINCT pg.id_pos_graduacao AS id_pos, pg.id_pessoa, (CASE pg.id_tipo_curso WHEN '3' THEN 'Mestrado' WHEN '5' THEN 'Doutorado' END) AS curso";
            if(!in_array($array['exclude'],'integralizacao'))   $sql.= ", date_format(ing.data_integralizacao,'%d/%m/%Y') AS integralizacao";
            if(!in_array($array['exclude'],'con_data_entrega')) $sql.= ", df.con_data_entrega";
            $sql .= $array['params'] . "
			FROM pos_graduacoes AS pg JOIN reingresso AS ing ON ing.id_pos_graduacao = pg.id_pos_graduacao
			LEFT JOIN defesa AS df
			ON pg.id_pos_graduacao = df.id_pos_graduacao
			AND df.data_defesa >= ing.data_reingresso
			AND df.con_data_entrega <= ing.data_integralizacao " . $array['sql'] . "
			" . $where . "
			AND ing.data_reingresso <= '" . $array['data'] . "'
			AND ((ing.data_desligamento >= '" . $array['data'] . "' AND ing.aluno_desligado = 1) OR ing.aluno_desligado = 0)
			AND ((df.con_data_entrega >= '" . $array['data'] . "' AND df.aprovado = 0) OR df.aprovado = 1 OR df.aprovado IS NULL)
			AND ing.data_integralizacao >= '" . $array['data'] . " '
			" . $array['and'] . "
			UNION ALL
			SELECT DISTINCT pg.id_pos_graduacao AS id_pos, pg.id_pessoa, (CASE pg.id_tipo_curso WHEN '3' THEN 'Mestrado' WHEN '5' THEN 'Doutorado' END) AS curso";
      if(!in_array($array['exclude'],'integralizacao'))   $sql.= ", date_format(ing.data_integralizacao,'%d/%m/%Y') AS integralizacao";
      if(!in_array($array['exclude'],'con_data_entrega')) $sql.= ", df.con_data_entrega";
      $sql .= $array['params'] . "
			FROM pos_graduacoes AS pg JOIN ingressos AS ing ON ing.id_pos_graduacao = pg.id_pos_graduacao
			LEFT JOIN defesa AS df
			ON pg.id_pos_graduacao = df.id_pos_graduacao
      
			AND df.data_defesa >= ing.data_ingresso
			AND df.con_data_entrega <= ing.data_integralizacao  " . $array['sql'] . "
			" . $where . "
			AND ing.data_ingresso <= '" . $array['data'] . "'
			AND ((ing.data_desligado >= '" . $array['data'] . "' AND ing.aluno_desligado = 1) OR ing.aluno_desligado = 0)
			AND ((df.data_defesa > '" . $array['data'] . "' AND df.aprovado = 0) OR df.aprovado = 1 OR df.aprovado IS NULL)
			AND ing.data_integralizacao >= '" . $array['data'] . " '
			" . $array['and'] . "
      
			ORDER BY $orderby;";

            $result = $dbAdapter->query($sql);
            $result = $result->fetchAll();
        } else {
            $sql = "SELECT DISTINCT pg.id_pos_graduacao AS id_pos, pg.id_pessoa, (CASE pg.id_tipo_curso WHEN '3' THEN 'Mestrado' WHEN '5' THEN 'Doutorado' END) AS curso";
            if(!in_array($array['exclude'],'integralizacao'))   $sql.= ", date_format(ing.data_integralizacao,'%d/%m/%Y') AS integralizacao";
            if(!in_array($array['exclude'],'con_data_entrega')) $sql.= ", df.con_data_entrega";
            $sql .= $array['params'] . "
			FROM pos_graduacoes AS pg JOIN reingresso AS ing ON ing.id_pos_graduacao = pg.id_pos_graduacao
			LEFT JOIN defesa AS df
			ON pg.id_pos_graduacao = df.id_pos_graduacao
			AND df.data_defesa >= ing.data_reingresso
			AND df.con_data_entrega <= ing.data_integralizacao " . $array['sql'] . "
			" . $where . "
			AND ing.data_reingresso <= '" . $array['data_ate'] . "'
			AND ((ing.data_desligamento >= '" . $array['data_de'] . "' AND ing.aluno_desligado = 1) OR ing.aluno_desligado = 0)
			AND ((df.data_defesa >= '" . $array['data_de'] . "' AND df.aprovado = 0) OR df.aprovado = 1 OR df.aprovado IS NULL)
			AND ing.data_integralizacao >= '" . $array['data_de'] . " '
			" . $array['and'] . "
			UNION ALL
			SELECT DISTINCT pg.id_pos_graduacao AS id_pos, pg.id_pessoa, (CASE pg.id_tipo_curso WHEN '3' THEN 'Mestrado' WHEN '5' THEN 'Doutorado' END) AS curso";
      if(!in_array($array['exclude'],'integralizacao'))   $sql.= ", date_format(ing.data_integralizacao,'%d/%m/%Y') AS integralizacao";
      if(!in_array($array['exclude'],'con_data_entrega')) $sql.= ", df.con_data_entrega";
      $sql .= $array['params'] . "
			FROM pos_graduacoes AS pg JOIN ingressos AS ing ON ing.id_pos_graduacao = pg.id_pos_graduacao
			LEFT JOIN defesa AS df
			ON pg.id_pos_graduacao = df.id_pos_graduacao
			AND df.data_defesa >= ing.data_ingresso
			AND df.con_data_entrega <= ing.data_integralizacao " . $array['sql'] . "
			" . $where . "
			AND ing.data_ingresso <= '" . $array['data_ate'] . "'
			AND ((ing.data_desligado >= '" . $array['data_de'] . "' AND ing.aluno_desligado = 1) OR ing.aluno_desligado = 0)
			AND ((df.con_data_entrega >= '" . $array['data_de'] . "' AND df.aprovado = 0) OR df.aprovado = 1 OR df.aprovado IS NULL)
			AND ing.data_integralizacao >= '" . $array['data_de'] . " '
			" . $array['and'] . "
			ORDER BY $orderby;";
//			
			echo $sql;
//			exit;
            $result = $dbAdapter->query($sql);
            $result = $result->fetchAll();
        }


        if (count($result) >= 1) {
            $lastitem = '';
            foreach ($result as $item) {
                if ($lastitem != $item['id_pos']) {
                    $lista[$contador] = $item;
                    $contador++;
                    $lastitem = $item['id_pos'];
                }
            }
        }


        if (count($lista) >= 1) {
            $and = '';
            foreach ($lista as $id) {
                $and.= 'AND id_pos_graduacao <> "' . $id['id_pos'] . '" ';
            }
        }

        $sql = "SELECT DISTINCT pg.id_pos_graduacao, pg.id_pessoa, pg.id_tipo_curso
        FROM pos_graduacoes AS pg WHERE pg.id_tipo_curso = '3' " . $and;
        $result = $dbAdapter->query($sql);
        $result = $result->fetchAll();

        if ($array['ativos'] == 'sim') {
            return $lista;
        } elseif ($array['ativos'] == 'nao') {
            return $result;
        } elseif ($array['ativos'] == 'todos') {


            $i = count($lista) - 1;

            if ($i < 0)
                $i = 0;
            //var_dump($result);

            forEach ($result as $index => $r) {

                $lista[$i] = $r;


                $i++;
            }

            return $lista;
        }
    }

    //    function alunosQueCancelaram()
    //    {
    //    	$array= $this->CRUDread();
    //
	//    	$arrFinal=array();
    //    	forEach($array as $index=>$a)
    //    	{
    //    		$arrFinal[$index]=$a['id_pos_graducao'];
    //
	//    		if($a['id_reingresso']!="") {
    //
	//    			if($a['data_integralizacao_reingresso']!="") {
    //
	//    				$status="Reingresso";
    //    				if($a['data_integralizacao_reingresso']<'now()'){
    //
	//    					//tem defesa terminada antes e comeÃ§ada depois
    //    				    if(!$this->defesaPosAtivaParaData($a['id_pos_graducao'],$a['data_integralizacao_reingresso']))
    //    					{
    //
	//    					}
    //
	//    				} else {
    //
	//    				}
    //
	//
	//
	//  				}
    //    		}
    //
	//
	//    		$arrFinal[$index]['status']=$status;
    //    	}
    //
	//    }
}

?>