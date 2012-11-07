<?php

require_once(APPLICATION_PATH . '/../library/relatorios/GridPdf.php');
require_once(APPLICATION_PATH . '/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH . '/models/ListaRelatorioPDF.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/models/Alunos.php');
require_once(APPLICATION_PATH . '/models/General.php');

class Relatorios_FormulariosController extends Zend_Controller_Action {

    public $coordenador = "";

    public function init() {
        $general = new Models_General();
        $orientadores = $general->listView('v_relatorio_professores_bancas', array('id', 'nome'));
        $this->view->orientadoresbancas = $orientadores;

        $general = new Models_General();
        $coordenador = $general->listTable('coordenador', array('id_coordenador', 'nome_coordenador'));
        $this->coordenador = $coordenador[1];
    }

    public function indexAction() {
        
    }

    public function htmlParaExemplo($array="") {
        //como é uma coisa variável e eu nao descobri como fazer
        //um cabecalho, tem que colocar o espaço

        $html.='Declaro, para os devidos fins que ';
        $html.=$array['nome'];
        $html.=', participou como presidente da banca tal. ';
        return $html;
    }

    public function htmlPDF($array=array()) {
        $html.='<div style="margin-left:20px;"> DECLARO, para os devido fins, que o(a) Doutor(a) abaixo identificado(a) participou como ' . $array['atribuicao'];
        $html.=' na<br/> Banca Examinadora de ' . $array['natureza'];
        $html.=' de ' . $array['nome'] . '. ';
        $html.='<br/><br/><br/><br/>';
        //html.=utf8_encode($array['professor']);
        $html.=$array['professor'];
        $html.='<br/><br/>';
        $html.='Data:' . $array['data'] . '';
        $html.='<p>';
        $html.='' . $this->coordenador . '<br/>';
        $html.='Coordenador(a) de Pós-Graduação - Instituto de Química - UNICAMP</p></div>';
        return $html;
    }

    public function exemploAction() {
        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
        $banco->table('v_professores');
        $banco->primary('id');
        $where = '';
        if ($_REQUEST['nome'] != "") {
            $campo = $_REQUEST['nome'];
            $where = "`nome` LIKE '%$campo%'";
            $this->view->nome = $_REQUEST['nome'];
        }

        $banco->where = $where;


        $arrayDados = $banco->CRUDread();
        $titulo = 'Declaração de professor';
        $campoLink = 'nome';

        forEach ($arrayDados as $a) {
            //o que ira indexar, será o parametro para impressão unica, neste caso o id

            $htmlPDF[$a[$campoLink]] = $this->htmlParaExemplo($a);
        }

        $headers = array('nome' => 'Professor',
            'id' => 'Código'
        );

        $flex = $GridPDF->exibicaoPDF($headers, $arrayDados, $htmlPDF, $titulo, $campoLink);
        $this->view->flex = $flex;
    }

    public function matriculaAction() {

        $alunos = new Application_Models_Alunos();
        $GridPDF = new GridPdf();

        $nome = $_REQUEST['nome'];
        $tipo = 'todos';

        $array = array(
            'ativos' => 'sim',
            'periodo' => 'nao',
            'data' => date('Y-m-d'),
            'curso' => $tipo,
            'params' => ', ps.nome AS nome_pessoa, ps.cpf ',
            'sql' => 'JOIN pessoas AS ps ON ps.id_pessoa = pg.id_pessoa',
            'and' => 'AND ps.nome LIKE "%' . $nome . '%"'
        );

        $lista = $alunos->listaalunos($array);

        $headers = array(
            'nome_pessoa' => 'Nome Aluno',
            'curso' => 'Curso',
            'cpf' => 'CPF'
        );

        $titulo = 'Comprovante de Matrícula';
        $campoLink = 'id_pos';
        //penultimo parametro false é opcional, caso true, exibe array de dados

        foreach ($lista as $a) {
            if ($a['cpf'] != '') {
                $cpf = substr($a['cpf'], 0, 3) . '.' . substr($a['cpf'], 3, 3) . '.' . substr($a['cpf'], 6, 3) . '-' . substr($a['cpf'], 9, 2);
            } else {
                $cpf = '';
            }
            $htmlPDF[$a[$campoLink]] = '
			Comprovamos, para os devidos fins, a regularidade da matrícula do(a) aluno 
			abaixo identificado(a):<br><br>
			Nome: ' . $a['nome_pessoa'] . '<br>
			CPF: ' . $cpf . '<br>
			Nível: ' . $a['curso'] . '
                        <br><br><p aligh="center"><center>' . $this->coordenador . '
                        <br>Coordenador(a) de Pós-Graduação - Instituto de Química - UNICAMP</center></p>';
        }

        $flex = $GridPDF->exibicaoPDF($headers, $lista, $htmlPDF, $titulo, $campoLink);
        $this->view->flex = $flex;
        $this->view->nome = $_REQUEST['nome'];
    }

    public function participacaobancaAction() {
        extract($_GET);
        $mostrar = $_REQUEST['mostrar'];

        if ($tipo == 2)
            $this->participacaoBancaBanca($orientador, $mostrar);
        else
            $this->participacaoBancaDocente($mostrar);

        $this->view->tipo = $tipo;
        $this->view->orientador = $_GET['orientador'];



        if (!is_array($mostrar)) {
            $mostrar['mestrado'] = 'on';
            $mostrar['doutorado'] = 'on';
            $mostrar['qualificacao'] = 'on';
        }

        $this->view->mostrar = $mostrar;
    }

    public function participacaoBancaBanca($orientador, $mostrar) {
        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
        $banco->table('v_formularios_participacao_banca_aluno');
        $banco->primary('id_pessoa');

        if (is_array($mostrar)) {
            if (isset($mostrar['mestrado']))
                $arrmostrar['mestrado'] = '0';

            if (isset($mostrar['doutorado']))
                $arrmostrar['doutorado'] = '1';

            if (isset($mostrar['qualificacao']))
                $arrmostrar['qualificacao'] = '2';

            $where.="(";
            $i = 1;
            foreach ($arrmostrar as $m) {
                $where .=" codigo='$m'";

                if (count($arrmostrar) != $i)
                    $where.=" OR";
                $i++;
            }
            $where.=" )";
        }
        if ($orientador != "0" && $orientador != "") {
            if (!is_array($arrmostrar))
                $where.="";
            else
                $where.=" AND";

            $where.=" orientador=" . $orientador;
        }


        $banco->where = $where;
        $arrayDados = $banco->CRUDread();

        $titulo = 'Comprovantes de participação em banca';
        $htmlPDF = '';
        $campoLink = 'nome';

        foreach ($arrayDados as $index => $arr) {
            $link = $this->view->url(array('module' => 'relatorios', 'controller' => 'Formularios', 'action' => 'detalhesbancasdocente', 'id' => $arr['id'], 'codigo' => $arr['Codigo']));
            $dadoLink = '<a href="' . $link . '">' . $arr['nome'] . '</a>';
            $arrayDados[$index]['link'] = $dadoLink;
        }

        $headers = array('link' => 'Aluno',
            'data' => 'Data',
            'Tipo' => 'Tipo');

        $flex = $GridPDF->flexGridJS($arrayDados, $headers);
        $this->view->flex = $flex;
    }

    public function participacaoBancaDocente($mostrar) {
        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
//		$banco->table('v_docente_banca');
//		$banco->primary('id_professor');
//		$banco->fields(array('nome','id_professor'));
//		$banco->groupby='nome, id_professor';
        $where = '1=1';

        if (is_array($mostrar)) {
            if (isset($mostrar['mestrado']))
                $arrmostrar['mestrado'] = '0';

            if (isset($mostrar['doutorado']))
                $arrmostrar['doutorado'] = '1';

            if (isset($mostrar['qualificacao']))
                $arrmostrar['qualificacao'] = '2';

            $where = "(";
            $i = 1;
            foreach ($arrmostrar as $m) {
                $where .=" codigo='$m'";

                if (count($arrmostrar) != $i)
                    $where.=" OR";
                $i++;
            }
            $where.=" )";
        }


        $banco->where = $where;


        $arrayDados = $banco->participacaoEmBanca($where);

        forEach ($arrayDados as $index => $a) {
            $arr[$index] = $a['id_professor'];
        }

        $titulo = 'Comprovantes de participação em banca';
        $htmlPDF = '';
        $campoLink = 'nome';

        foreach ($arrayDados as $index => $arr) {
            $link = $this->view->url(array('module' => 'relatorios', 'controller' => 'Formularios', 'action' => 'detalhesbancas', 'id_orientador' => $arr['id_professor']));
            $dadoLink = '<a href="' . $link . '">' . $arr['nome'] . '</a>';
            $arrayDados [$index]['link'] = $dadoLink;
        }

        $headers = array('link' => 'Docente');

        $flex = $GridPDF->flexGridJS($arrayDados, $headers);
        $this->view->flex = $flex;
    }

    public function detalhesbancasdocenteAction() {
        $id = ($this->_request->getParam("id"));
        $codigo = ($this->_request->getParam("codigo"));

        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
        $banco->table('v_formularios_participacao_banca');
        $banco->primary('id_pessoa');
        $banco->orderby('data');
        
        $where = " id=$id  AND Codigo=$codigo";
        $banco->where = $where;

        $arrayDados = $banco->CRUDread();
        $titulo = 'Comprovantes de participação em banca';
        $htmlPDF = '';
        $campoLink = 'id_professor';

        
        print_r($arrayDados);
        exit;
        
        forEach ($arrayDados as $index => $a) {
            $arrayDados [$index]['link'] = $dadoLink;
            $htmlPDF[$a[$campoLink]] = $this->htmlPDF($a);
            $aluno = $a['nome'];
        }

        $headers = array('professor' => 'Orientador',
            'Tipo' => 'Tipo'
        );

        $this->view->aluno = $aluno;
        $flex = $GridPDF->exibicaoPDF($headers, $arrayDados, $htmlPDF, $titulo, $campoLink);
        $this->view->flex = $flex;
    }

    public function detalhesbancasAction() {
        
        extract($_REQUEST);
        
        $id_orientador = ($this->_request->getParam("id_orientador"));

        $GridPDF = new GridPdf();
        
        
        $where=" id_professor=$id_orientador  ";

		if((int)$anoinicial!="")
		{
			$where.=" AND databd >= '$anoinicial-01-01 00:00:0000' ";
		}

		if((int)$anofinal!="")
		{
			$where.=" AND databd <= '$anofinal-12-31 00:00:0000' ";
		}

        
        //print_r($where);
        //exit;
        
                
                
        $banco = new Application_Models_ListaRelatiorPDF();
        $banco->table('v_formularios_participacao_banca');
        $banco->primary('id_professor');
        
        $banco->where = $where;

        $arrayDados = $banco->CRUDread();
        $titulo = 'Comprovantes de participação em banca';
        $htmlPDF = '';
        $campoLink = 'unico';


        forEach ($arrayDados as $index => $a) {
            //o que ira indexar, será o parametro para impressão unica, neste caso o id
            if ($a['natureza'] == "Mestrado") {
                $a['natureza'] = "Tese de Mestrado";
            }

            if ($a['natureza'] == "Doutorado") {
                $a['natureza'] = "Tese de Doutorado";
            }
            $arrayDados [$index]['link'] = $dadoLink;
            $htmlPDF[$a[$campoLink]] = $this->htmlPDF($a);
            $professor = $a['professor'];
        }

        $headers = array('nome' => 'Aluno',
            'natureza' => 'Natureza',
            'data' => 'Data'
        );

        $this->view->professor = $professor;

        $flex = $GridPDF->exibicaoPDF($headers, $arrayDados, $htmlPDF, $titulo, $campoLink);
        $this->view->flex = $flex;
    }

    public function presencaAction() {
        $GridPDF = new GridPdf();
        $Mask = new mascara();
        $this->view->mascara = $Mask->mascaraInputMesAnoJS('.data');
        $Alunos = new Application_Models_Alunos();


        $bolsa = 'nao';
        if ($_REQUEST['bolsa'] != "") {
            $bolsa = $_REQUEST['bolsa'];
        }
        $this->view->bolsa = $bolsa;

        if ($_REQUEST['data'] != "") {
            $data = $_REQUEST['data'];
        }
        $this->view->data = $data;

        $dataArr = $Mask->getArrayData('00/' . $data);

        $ano = $dataArr['ano'] != "" ? $dataArr['ano'] : date('Y');
        $mes = $dataArr['mes'] != "" ? $dataArr['mes'] : date('m');

        $Orientadores = new Application_Models_Orientadores();

        $sql = '';
        $and = '';
        $parans = '';

        $headers = array('ra' => 'RA',
            'nome' => 'Nome',
            'orientador_nome' => 'Orientador'
        );



        if ($bolsa == 'sim') {
            $and = " AND e.data_fim>='$ano-$mes-31 00:00:00' AND e.deletado='0000' ";

            $headers = array('ra' => 'RA',
                'nome' => 'Nome',
                'agencia' => 'Agência',
                'orientador_nome' => 'Orientador');

            $sql = " RIGHT JOIN bolsas e on e.id_posgraduacao = pg.id_pos_graduacao";
            $sql.=' INNER JOIN agencias a on a.id_agencia=e.id_agencia';
            $parans = ', e.id_agencia, a.agencia';
        }

        $array = array(
            'ativos' => 'sim',
            'periodo' => 'sim',
            'data_de' => $ano . '-' . $mes . '-01',
            'data_ate' => $ano . '-' . $mes . '-31',
            'curso' => 'todos',
            'params' => ',pes.nome, ingr.ra' . $parans,
            'sql' => $sql . ' LEFT JOIN pessoas pes on pes.id_pessoa = pg.id_pessoa
						 LEFT JOIN ingressos ingr ON ingr.id_pos_graduacao = pg.id_pos_graduacao',
            'and' => "" . $and
        );


        $arrayFinal = array();
        $lista = $Alunos->listaalunos($array);
        $i = 0;
        forEach ($lista as $index => $a) {
            $bool = $Alunos->frequentouByPosEAnoMes($a['id_pos'], $mes, $ano);

            if ($bool == 1) {

                $arrayFinal[$i] = $lista[$index];
                $Orientador = $Orientadores->getOrientadorByPosEData($a['id_pos'], $ano . '-' . $mes . '-31 00:00:00');
                $arrayFinal[$i]['orientador_nome'] = $Orientador['nome'];
                $i++;
            }
        }

        $arrayDados = $arrayFinal;
        $titulo = 'Atestado de frequência e bom desempenho';
        $campoLink = 'id_pos';


        forEach ($arrayDados as $index => $a) {
            $tipo = "";
            if ($bolsa == 'sim')
                $tipo = $a['agencia'];
            $htmlPDF[$a[$campoLink]] = $this->presencaHtml($a['nome'], $a['orientador_nome'], $mes, $ano, $tipo);
        }



        $flex = $GridPDF->exibicaoPDF($headers, $arrayDados, $htmlPDF, $titulo, $campoLink);
        $this->view->flex = $flex;
    }

    private function presencaHtml($nome, $orientador, $mes, $ano, $tipo='') {
        $Mask = new mascara();
        $mesextenso = $Mask->getMes($mes);

        if ((int) $mes == 1) {
            $mesAnterior = 12;
            $anoAterior = ((int) $ano) - 1;
        } else {
            $mesAnterior = ((int) $mes) - 1;
            $anoAterior = ((int) $ano);
        }

        if (($tipo != "") && (substr_count(strtoupper($tipo), strtoupper('capes'), 0, strlen($tipo)) > 0)) {
            $html = "<center><h3>BOLSISTA / CAPES</h3></center>
			  <p align='left'>Atesto, para os devidos fins,
			   que o aluno(a)
			   " . $nome . ", do Programa de Pós-Graduação em Química,
			   teve frequência regular e apresentou bom desempenho 
			   em seus trabalhos durante o período de 20/$mesAnterior/$anoAterior a 19/$mes/$ano, 
			    fazendo jus à mensalidade do mês de 
			   $mesextenso/$ano.
			  </p> 
			 <p align='left'style='margin-left:60px;'> Prof(a). Dr(a). " . $orientador . "
			   <br>
			   Orientador(a)
			  </p>
			   ";
        } elseif ($tipo != "") {

            $html = "<center><h3>BOLSISTA / $tipo</h3></center>
			  <p>Atesto para os devidos fins, que a aluno(a) regularmente 
			  matriculada no Programa de Pós-Graduação  em Química deste 
			  Instituto, teve freqüência regular e apresentou bom 
			  desempenho em seus trabalhos, durante o período de 
			  20/$mesAnterior/$anoAterior a 19/$mes/$ano, fazendo jus à 
			  mensalidade do mês de  $mesextenso/$ano.
			 <br>
				<p align='left'style='margin-left:60px;'>
				" . $nome . "
			  	</p>
			  
			  </p>
			  <br>
			  <p align='left'style='margin-left:60px;'> Prof(a). Dr(a). " . $orientador . "
			   <br>
			   Orientador(a)
			  </p>
			   ";
        } else {
            $html = "
			  <p align='left'style='margin-left:60px;margin-right:30px;'>Atesto para os devidos fins, que a aluno(a) regularmente 
			  matriculada no Programa de Pós-Graduação  em Química deste 
			  Instituto, teve freqüência regular e apresentou bom 
			  desempenho em seus trabalhos, durante o período de 
			  20/$mesAnterior/$anoAterior a 19/$mes/$ano, fazendo jus à 
			  mensalidade do mês de  $mesextenso/$ano.
			 <br>
				<p align='left'style='margin-left:60px;'>
				" . $nome . "
			  	</p>
			  
			  </p>
			  <br>
			  <p align='left'style='margin-left:60px;'> Prof(a). Dr(a). " . $orientador . "
			   <br>
			   Orientador(a)
			  </p>
			   ";
        }
        $html.='<br><br><p aligh="center"><center>' . $this->coordenador . '
                        <br>Coordenador(a) de Pós-Graduação - Instituto de Química - UNICAMP</center></p>';
        return '<div style="">' . $html . '</div>';
    }

    public function defesaAction() {
        $alunos = new Application_Models_Alunos();
        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
        $banco->table('v_defesas_aluno_concentracao');
        $banco->primary('id_pos_graduacao');
        $where = '';
        if ($_REQUEST['nome'] != "") {
            $campo = $_REQUEST['nome'];
            $where = "`nome_aluno` LIKE '%$campo%'";
            $this->view->nome = $_REQUEST['nome'];
        }
        $banco->where = $where;
        $headers = array(
            'nome_aluno' => 'Nome',
            'tipo_curso' => 'Curso',
            'data_defesa_mask' => 'Data Defesa',
            'data_entrega_mask' => 'Data de Entrega'
        );
        $lista = $banco->CRUDread();
        $titulo = 'Comprovante de participação em banca';
        $campoLink = 'id_defesa';
        $mask = new mascara();
        $bancaDefesa = new Application_Models_ListaRelatiorPDF();
        $bancaDefesa->table('v_professor_defesa');
        $bancaDefesa->primary('id_defesa');


        forEach ($lista as $index => $l) {
            $nomealuno = $l['nome_aluno'];
            $tipocurso = $l['tipo_curso'];
            $ra = $l['RA'];
            $datadefesa = $l['data_defesa_mask'];
            $tipoprojeto = $l['tipo_projeto'];
            $area = $l['area_de_concentracao'];

            //FALTA LÓGICA DE APROVADO
            $aprovado = (int) $l['aprovado'];
            //AQUI ESTAH =0, pode ser que mude
            $lista[$index]['data_entrega_mask'] = $mask->datetimeSQLToMascaraData($l['data_entrega']);

            if ($aprovado == 0) {
                $resultado = 'Em andamento';
                if ($l['data_entrega'] != "0000-00-00") {
                    $resultado = 'Aprovado';
                }
            }else
                $resultado='Não aprovado';

            $where = "id_defesa=" . $l['id_defesa'];
            $bancaDefesa->where = $where;
            $arrBanca = $bancaDefesa->CRUDread();

            $htmlPDF[$l[$campoLink]] = $this->defesaHtml($arrBanca, $nomealuno, $area, $tipocurso, $ra, $datadefesa, $resultado, $tipoprojeto);
        }

        $flex = $GridPDF->exibicaoPDF($headers, $lista, $htmlPDF, $titulo, $campoLink);
        $this->view->flex = $flex;
        $this->view->nome = $_REQUEST['nome'];
    }

    public function defesaHtml($arrBanca, $nomealuno, $area, $tipocurso, $ra, $datadefesa, $resultado, $tipoprojeto) {
        $html = "<div style='margin-left:60px;margin-right:40px;'>";
        $html.="<p align='left'>DECLARO, para os devidos fins, que o(a) aluno(a) abaixo identificado(a), defendeu " . $tipoprojeto . " de " . $tipocurso;
        $html.=" na área de " . $area . ". </p>";

        $html.="<p align='left'>Aluno(a): " . $nomealuno . "<br>";
        $html.="Registro Acadêmico: " . $ra . "<br>";
        $html.="Data da defesa: " . $datadefesa . "<br></p>";

        $html.="<p align='left'><br>Banca examinadora:";

        forEach ($arrBanca as $b) {
            $html.="<br>(" . $b['instituicao'] . ") " . $b['nome'];
        }
        $html.="</p><br><br>";

        $html.="<p  align='left'>Resultado final: $resultado</p>";

        $html.="<p align='left'>Para que o respectivo título possa ser concedido, com as prerrogativas legais  dele <br> advindas,  é
necessário que ocorra a homologação do exemplar definitivo da <br> respectiva " . $tipoprojeto . "  pelas instâncias
competentes da UNICAMP.</p>";

        

        $html.='<br><br><p aligh="center"><center>' . $this->coordenador . '
                        <br>Coordenador(a) de Pós-Graduação - Instituto de Química - UNICAMP</center></p>';
        $html.="</div>";
        return $html;
    }

}

?>