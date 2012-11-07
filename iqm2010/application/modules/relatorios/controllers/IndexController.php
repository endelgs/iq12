<?php

require_once(APPLICATION_PATH . '/../library/relatorios/GridPdf.php');
require_once(APPLICATION_PATH . '/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH . '/models/ListaRelatorioPDF.php');
require_once(APPLICATION_PATH . '/models/inscricoes.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/models/Alunos.php');
require_once(APPLICATION_PATH . '/models/General.php');

class Relatorios_IndexController extends Zend_Controller_Action {

    public function init() {
        $general = new Models_General();
        $this->view->orientadores = $general->listView('v_professores', array('id', 'nome'));

        $general = new Models_General();
        $this->view->tiposBolsa = $general->listView('v_relatorios_agencias', array('id', 'agencia'));

        $general = new Models_General();
        $this->view->fitroTipo = $general->listView('v_relatorios_filtro', array('id', 'tipo_curso'));

        $general = new Models_General();
        $this->view->orientadoresbancas = $general->listView('v_relatorio_professores_bancas', array('id', 'nome'));
    }

    public function indexAction() {
        
    }

    public function alunonotabaixaAction() {

        $Alunos = new Application_Models_Alunos();
        $GridPDF = new GridPdf();
        $tipo = '3';
        if ($_REQUEST['tipo'] != "") {
            $tipo = $_REQUEST['tipo'];
        }

        if ($_REQUEST['ativos'] != "") {
            $ativo = $_REQUEST['ativos'];
        }


        $this->view->tipo = $tipo;
        $this->view->ativo = $ativo;


        //		$request = Zend_Controller_Front::getInstance()->getRequest();
        //		$url = $request->getScheme() . '://' . $request->getHttpHost() . $url;

        if ($ativo == 'Apenas') {

            $array = array(
                'ativos' => 'sim',
                'periodo' => 'nao',
                'data' => date('Y-m-d'),
                'curso' => $tipo,
                'params' => ', pd.`conceito`, d.`codigo`, i.ra, p.nome, t.*',
                'sql' => "INNER JOIN pg_disciplinas pd ON pd.id_pos_graduacao = pg.id_pos_graduacao
						INNER JOIN turmas t ON t.`id_turma`=pd.`id_turma`
						INNER JOIN disciplinas d ON t.`materia`=d.`id_disciplina`
						INNER JOIN pessoas p ON pg.`id_pessoa`=p.`id_pessoa`
						INNER JOIN ingressos i ON pg.`id_pos_graduacao`=i.`id_pos_graduacao`	
						",
                'and' => " AND pd.conceito='C' OR pd.conceito='D' OR pd.conceito='E'"
            );

            $arrDados = $Alunos->listaalunos($array);
        } else {

            $arrDados = $Alunos->todosAnosNotaBaixa($tipo);
        }



        forEach ($arrDados as $index => $l) {
            $link = $this->view->url(array('module' => 'pessoas', 'controller' => 'geral', 'action' => 'index', 'id_pessoa' => $l['id_pessoa']));
            ///posgraduacao/disciplinas/index/id_pessoa/363/id_pos_graduacao/427/tipo/3
            $link2 = $this->view->url(array('module' => 'posgraduacao', 'controller' => 'disciplinas', 'action' => 'index', 'id_pessoa' => $l['id_pessoa'], 'id_pos_graduacao' => $l['id_pos'], 'tipo' => $l['id_tipo_curso']));
            $arrDados[$index]['Link'] = '<a href="' . $link . '">' . $l['nome'] . '</a>';
            $arrDados[$index]['Link2'] = '<a href="' . $link2 . '">' . $l['codigo'] . '</a>';
        }


        $headers = array('ra' => 'RA',
            'Link' => 'Nome',
            'Link2' => 'Disciplina',
            'turma' => 'Turma',
            'ano' => 'Ano',
            'conceito' => 'conceito'
        );

        //penultimo parametro false é opcional, caso true, exibe array de dados
        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $arrDados, false, '');
        $this->view->flex = $flex;
    }

    public function conclusaocursoAction() {
        $alunos = new Application_Models_Alunos();
        $GridPDF = new GridPdf();

        $tipo = '3';
        if ($_REQUEST['tipo'] != "") {
            $tipo = $_REQUEST['tipo'];
        }

        $this->view->tipo = $tipo;
        $meses = 0;
        if ($_REQUEST['meses'] != '')
            $meses = $_REQUEST['meses'];
        $data = $this->SomarMeses(date('d/m/Y'), $meses);

        $array = array(
            'ativos' => 'sim',
            'periodo' => 'nao',
            'data' => date('Y-m-d'),
            'curso' => $tipo,
            'params' => ', nome_aluno, vc.ra AS registro_academico, nome_orientador ',
            'sql' => 'JOIN v_conclusaocurso AS vc ON vc.id_pos_graduacao = pg.id_pos_graduacao'
        );
        $lista = $alunos->listaalunos($array);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;

        forEach ($lista as $index => $l) {
            $link = $this->view->url(array('module' => 'pessoas', 'controller' => 'geral', 'action' => 'index', 'id_pessoa' => $l['id_pessoa']));
            $lista[$index]['Link'] = '<a href="' . $link . '">' . $l['nome_aluno'] . '</a>';
        }

        $contador = 0;
        $lista_nova = array();
        foreach ($lista as $item) {
            $return = $this->ComparaData($item['integralizacao'], '<=', $data);
            if ($return) {
                $lista_nova[$contador] = $item;
                $contador++;
            }
        }

        $headers = array(
            'registro_academico' => 'ra',
            'nome_aluno' => 'Nome Aluno',
            'nome_orientador' => 'Orientador',
            'integralizacao' => 'Integralizacao'
        );

        //penultimo parametro false é opcional, caso true, exibe array de dados
        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $lista_nova, false, '');
        $this->view->flex = $flex;
    }

    public function alunocanceladosAction() {

        $Alunos = new Application_Models_Alunos();
        $GridPDF = new GridPdf();
        $tipo = '3';
        if ($_REQUEST['tipo'] != "") {
            $tipo = $_REQUEST['tipo'];
        }
        
        
        if ($_REQUEST['inicio'] && $_REQUEST['fim'])
            $list = $Alunos->alunosQueCancelaram($tipo, $_REQUEST['inicio'], $_REQUEST['fim']);
        else
            $list = $Alunos->alunosQueCancelaram($tipo);
        
        $Mask = new mascara();
        $this->view->mascara = $Mask->mascaraInputDataJS('.data');

        $this->view->tipo = $tipo;
        $list = $Alunos->alunosQueCancelaram($tipo);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;


        forEach ($list as $index => $l) {
            $link = $this->view->url(array('module' => 'pessoas', 'controller' => 'geral', 'action' => 'index', 'id_pessoa' => $l['id_pessoa']));
            $list[$index]['Link'] = '<a href="' . $link . '">' . $l['nome'] . '</a>';
        }

        $headers = array('RA' => 'RA',
            'Link' => 'Nome'
        );

        //penultimo parametro false é opcional, caso true, exibe array de dados
        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $list, false, '');
        
        $this->view->flex = $flex;
    }

    public function exemploAction() {
        $GridPDF = new GridPdf();

        $tabela = 'v_professores';
        $primarykey = 'id';
        $where = "";
        $orderby = "";
        if ($_REQUEST['nome'] != "") {
            $campo = $_REQUEST['nome'];
            $where = "`nome` LIKE '%$campo%'";
            $this->view->nome = $_REQUEST['nome'];
        }

        $headers = array(
            'id' => 'Código',
            'nome' => 'Professor'
        );

        //penultimo parametro false é opcional, caso true, exibe array de dados
        $flex = $GridPDF->exibicaoFinal($tabela, $primarykey, $where, $headers, array(), false, $orderby);
        $this->view->flex = $flex;
    }

    public function defesasocorridasAction() {
        $GridPDF = new GridPdf();
        $Prof = new Application_Models_Orientadores();
        $Mask = new mascara();

        $this->view->mascara = $Mask->mascaraInputDataJS('.data');
        $this->view->subfiltro = false;
        $where = '';
        $orderby = 'nome_aluno';
        $tabela = 'v_defesa_aluno_orientador';
        $primarykey = 'id_defesa';
        $tipo = '3';
        if ($_REQUEST['tipo'] != "") {
            $tipo = $_REQUEST['tipo'];
            $this->view->tipo = $tipo;
        }
        $where.='id_tipo_curso=' . $tipo . ' ';


        if ($this->_getParam('datainicio') != "" && $this->_getParam('datafim') != "") {

            $datainicio = $Mask->MascaraDataTodatetimeSQL($this->_getParam('datainicio'));
            $datafim = $Mask->MascaraDataTodatetimeSQL($this->_getParam('datafim'));

            $arrProf = $Prof->orientadoresPorData($datainicio, $datafim, $tipo);
            $this->view->subfiltro = true;
            $arrProf = $Mask->trataArrayParaComboBox($arrProf, 'nome', 'id');
            $arrInicio['All'] = 'Todos';
            $arrProf = $arrInicio + $arrProf;
            $this->view->profSelect = 'All';
            $this->view->prof = $arrProf;

            $this->view->datainicio = $this->_getParam('datainicio');
            $this->view->datafim = $this->_getParam('datafim');

            $where.=" AND data_defesa>=" . "'" . $datainicio . "'" . " AND data_defesa<" . "'" . $datafim . "'";

            if ($this->_getParam('professores') != "") {

                $id_professor = $this->_getParam('professores');
                $this->view->ordenar = $this->_getParam('ordernar');
                $orderby = $this->_getParam('ordernar');
                if ($id_professor != 'All')
                    $where.=" AND id_orientador_professor='$id_professor'";

                $this->view->profSelect = $this->_getParam('professores');
            }
        }

        $headers = array('RA' => 'RA',
            'nome_aluno' => 'Aluno',
            'titulo' => 'Título',
            'nome_orientador' => 'Orientador',
            'data_defesa_mask' => 'Data defesa'
        );

        $flex = $GridPDF->exibicaoFinal($tabela, $primarykey, $where, $headers, array(), false, $orderby);
        $this->view->flex = $flex;
    }

    public function exemplocomarrayAction() {
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
        $headers = array('nome' => 'Professor',
            'id' => 'Código'
        );

        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $arrayDados, false, '');
        $this->view->flex = $flex;
    }

    public function calculaData($data1, $data2) {

        $mask = new mascara();
        $data1 = $mask->dateSQLtoDay($data1);
        $data2 = $mask->dateSQLtoDay($data2);

        $dataF = ($data2 - $data1) / 30;
        $dataF = (int) $dataF;

        return $dataF;
    }

    public function reprovadosdefesaAction() {
        extract($_GET);
        $where = " aprovado=0 ";

        $mask = new mascara();
        $this->view->mask = $mask->mascaraInputDataJS('.data');

        $dataInicio = $mask->MascaraDataTodatetimeSQL($dataInicio);
        $dataFim = $mask->MascaraDataTodatetimeSQL($dataFim);

        if ($dataInicio != "")
            $where.=" AND data_defesa >= '$dataInicio' ";

        if ($dataFim != "")
            $where.=" AND data_defesa <= '$dataFim' ";

        if ($tipo_curso != "")
            $where.=" AND id_tipo_curso='$tipo_curso' ";


        $GridPDF = new GridPdf();

        $tabela = 'v_defesa_aluno_orientador';
        $primarykey = 'id_pos_graduacao';


        $headers = array('nome_aluno' => 'Aluno',
            'data_defesa_mask' => 'Data de defesa');

        $flex = $GridPDF->exibicaoFinal($tabela, $primarykey, $where, $headers, array(), false);
        $this->view->flex = $flex;

        $this->view->flex = $flex;
        $this->view->dataInicio = $mask->datetimeSQLToMascaraData($dataInicio);
        $this->view->dataFim = $mask->datetimeSQLToMascaraData($dataFim);
        $this->view->tipoCurso = $tipo_curso;
    }

    public function examesqualificacaoAction() {
        extract($_GET);

        $Mask = new mascara();
        $this->view->mask = $Mask->mascaraInputDataJS('.data');

        $GridPDF = new GridPdf();

        $tabela = 'v_relatorios_exames_qualificacao';
        $primarykey = 'RA';
        $where = " 1=1  ";

        if ((int) $tipo_curso != 1) {
            if ((int) $tipo_curso != 0)
                $where.=" AND id_tipo_curso='$tipo_curso' ";
        }


        if ($datainicio != "") {
            $datainicio = $Mask->MascaraDataTodatetimeSQL($datainicio);
            $where.=" AND data > '$datainicio 00:00:00' ";
        }

        if ($datafim != "") {
            $datafim = $Mask->MascaraDataTodatetimeSQL($datafim);
            $where.=" AND data < '$datafim 00:00:00' ";
        }




        $headers = array('RA' => 'RA',
            'nome' => 'Aluno',
            'data_f' => 'Data de qualificação',
            'tipo_curso' => 'Tipo Curso',
            'tipo_q' => 'Status');


        //ultimo parametro false é opcional, caso true, exibe array de dados
        $flex = $GridPDF->exibicaoFinal($tabela, $primarykey, $where, $headers, array(), false);
        $this->view->flex = $flex;

        if ($datafim != "")
            $this->view->datafim = $Mask->datetimeSQLToMascaraData($datafim);

        if ($datainicio != "")
            $this->view->datainicio = $Mask->datetimeSQLToMascaraData($datainicio);

        if ($tipo_curso != "")
            $this->view->tipocurso = $tipo_curso;
    }

    public function bolsistasAction() {
        extract($_GET);

        $GridPDF = new GridPdf();
        $Alunos = new Application_Models_Alunos();
        $Mask = new mascara();
        $this->view->mascara = $Mask->mascaraInputMesAnoJS('.data');

        $this->view->data = $_REQUEST['data'];

        $dataArr = $Mask->getArrayData('00/' . $data);

        $ano = $dataArr['ano'] != "" ? $dataArr['ano'] : date('Y');
        $mes = $dataArr['mes'] != "" ? $dataArr['mes'] : date('m');

        $this->view->orientador = $_REQUEST['orientsdores'];

        $this->view->agencia = $_REQUEST['agencia'];

        $tipo = $_REQUEST['tipo_curso'];
        $this->view->tipo = $tipo;
        if ($tipo == "")
            $tipo = "todos";


        $where = "";
        $where1 = "";
        if ($agencia != "1") {
            $where1 = " AND a.id_agencia='$agencia' ";

            $this->view->agencia = $agencia;
        }

        $sql = "";
        if ($vinculo == "on") {

            $this->view->vinculo = $vinculo;

            $sql = " RIGHT JOIN profissionais pro ON pro.id_pessoa=pg.id_pessoa";
            $where.=" AND pro.trabalhando=1";
        }


        $orderby = "";
        if ($agrupar == "on") {
            $this->view->agrupar = $agrupar;
            $orderby = ' orientador, data_inicio_bolsa  ASC';
        } else {
            $orderby = ' data_inicio_bolsa DESC';
        }
        $datas = "";
        $dtInicio = "'$ano-$mes-01 00:00:00'"; //b.data_fim
        $dtFim = "'$ano-$mes-31 00:00:00'"; //b.data_inicio
        //		$datas=" AND ((b.data_inicio>=$dtInicio OR b.data_inicio<=$dtFim) AND";
        //		$datas.="(b.data_fim>=$dtInicio OR b.data_fim=$dtFim)";
        //		$datas.=")";

        $sqlorientador = ", (SELECT pess.nome  FROM `orientadores` orient
LEFT JOIN professores pro ON pro.id_professor=orient.id_professor
LEFT JOIN pessoas pess ON pess.id_pessoa=pro.id_pessoa
WHERE orient.`id_ingresso`=i.id_ingresso
AND (

orient.data<=$dtFim


)ORDER BY orient.data DESC

LIMIT 1) as orientador ";


        $array = array(
            'ativos' => 'sim',
            'periodo' => 'sim',
            'data_de' => $ano . '-' . $mes . '-01',
            'data_ate' => $ano . '-' . $mes . '-31',
            'curso' => $tipo,
            'params' => ",i.RA, p.nome, a.agencia, pg.id_tipo_curso $sqlorientador",
            'sql' => 'LEFT JOIN ingressos i on i.id_pos_graduacao = pg.id_pos_graduacao
						RIGHT JOIN bolsas b on b.id_posgraduacao = pg.id_pos_graduacao
						LEFT JOIN pessoas p on p.id_pessoa = pg.id_pessoa
						LEFT JOIN agencias a on b.id_agencia = a.id_agencia' . $sql,
            'and' => $datas . " " . $where . " " . $where1,
            //'orderby' => $orderby,
            'exclude' => array('integralizacao','con_data_entrega')
        );



        if ($sem_bolsa == "on") {
            $this->view->sembolsa = $sem_bolsa;



            $array = array(
                'ativos' => 'sim',
                'periodo' => 'sim',
                'data_de' => $ano . '-' . $mes . '-01',
                'data_ate' => $ano . '-' . $mes . '-31',
                'curso' => $tipo,
                'params' => ",i.RA, p.nome, a.agencia, pg.id_tipo_curso $sqlorientador",
                'sql' => 'LEFT JOIN ingressos i on i.id_pos_graduacao = pg.id_pos_graduacao
						LEFT JOIN bolsas b on b.id_posgraduacao = pg.id_pos_graduacao
						LEFT JOIN pessoas p on p.id_pessoa = pg.id_pessoa
						LEFT JOIN agencias a on b.id_agencia = a.id_agencia ' . $sql,
                'and' => " AND ISNULL(b.id_bolsa)  " . $where,
                //'orderby' => "  " . $orderby . " ",
                'exclude' => array('integralizacao','con_data_entrega')
            );
        }

        $lista = $Alunos->listaalunos($array);

        //var_dump($lista);

        $headers = array('RA' => 'RA',
            'nome' => 'Aluno',
            'orientador' => 'Orientador',
            'agencia' => 'Agência',
            'curso' => 'Curso');

        //ultimo parametro false é opcional, caso true, exibe array de dados
        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $lista, false);
        $this->view->flex = $flex;
    }

    public function inscritosAction() {
        extract($_GET);

        $where = "";

        $tipo = $_GET['tipo_curso'];

        if ($tipo == "")
            $tipo = 5;

        if ($cbTipo == "2")//nao aceitos
            $where.=" AND inscricao_aceita='0'";

        if ($cbTipo == "3")//sem prova
            $where.=" AND (e.fez_prova='0' OR ISNULL(e.fez_prova)) ";

        if ($cbTipo == "4")//reprovados
            $where.=" AND( e.aprovado='0' OR ISNULL(e.aprovado))";

        if ($cbTipo == "5")//sem matricula
            $where.=" AND ( e.matriculado='0' OR ISNULL(e.matriculado))";

        if ($dataInicio == "")
            $dataInicio = date('Y');

        $this->view->tipo = $_REQUEST['tipo_curso'];
        $this->view->cbTipo = $_REQUEST['cbTipo'];
        $this->view->dataInicio = $_REQUEST['dataInicio'];
        $alunos = new Application_Models_Alunos();

        $inscri = new Application_Models_Inscricoes();

        $lista = $inscri->getInscricoesByAno($dataInicio, $where, $tipo);

        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
        $headers = array('ra' => 'RA',
            'nome' => 'Nome',
            'data_inscricao_mask' => 'Data Inscrição',
            'data_ingresso_mask' => 'Data de início');

        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $lista, false, '');
        $this->view->flex = $flex;
    }

    public function alunoscursoAction() {
        extract($_GET);

        if ($data != "") {
            if ($data != "1")
                $where = " AND prof.id_professor='$data'";
        }

        $this->view->data = $_REQUEST['data'];
        $alunos = new Application_Models_Alunos();
        $alunos->listaalunos();
        $array = array(
            'ativos' => 'sim',
            'periodo' => 'nao',
            'data' => date('Y-m-d'),
            'curso' => 'todos',
            'params' => ' ,pp.nome as professor, insc.id_professor, pes.nome as aluno ',
            'sql' => 'INNER JOIN inscricoes insc ON pg.id_pos_graduacao = insc.id_pos_graduacao
						INNER JOIN pessoas pes on pes.id_pessoa = pg.id_pessoa
						INNER JOIN professores prof ON prof.id_professor = insc.id_professor
						INNER JOIN pessoas pp ON pp.id_pessoa= prof.id_pessoa ',
            'and' => $where
        );
        $lista = $alunos->listaalunos($array);




        $GridPDF = new GridPdf();
        $banco = new Application_Models_ListaRelatiorPDF();
        $headers = array('professor' => 'Professor',
            'aluno' => 'Aluno',
            'curso' => 'Curso');
        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $lista, false, '');
        $this->view->flex = $flex;
    }

    public function alunosativosAction() {
        $alunos = new Application_Models_Alunos();
        $alunos->listaalunos();
        $array = array(
            'ativos' => 'sim',
            'periodo' => 'nao',
            'data' => date('Y-m-d'),
            'params' => ',pes.nome, ingr.ra',
            'sql' => ' LEFT JOIN pessoas pes on pes.id_pessoa = pg.id_pessoa
						 LEFT JOIN ingressos ingr ON ingr.id_pos_graduacao = pg.id_pos_graduacao',
        );
        $lista = $alunos->listaalunos($array);
        //print_r($lista);
    }

    public function tempomediotitulacaoAction() {
        extract($_GET);
        $mask = new mascara();

        $dataInicio = $mask->MascaraDataTodatetimeSQL($dataInicio);
        $dataFim = $mask->MascaraDataTodatetimeSQL($dataFim);

        $GridPDF = new GridPdf();

        $banco = new Application_Models_ListaRelatiorPDF();
        $banco->table('v_curso_defesa');
        $banco->primary('id_pos_graduacao');

        $where = " 1=1 ";

        if ($dataInicio != "")
            $where.=" AND data_inscricao >= '$dataInicio' ";

        if ($dataFim != "")
            $where.=" AND data_defesa <= '$dataFim' ";

        if ($tipo_curso != "")
            $where.=" AND id_tipo_curso='$tipo_curso' ";

        $banco->where = $where;
        $arrayDefesas = $banco->CRUDread();


        $arr = explode('-', $dataInicio);
        $anoInicio = $arr[0];

        $arr = explode('-', $dataFim);
        $anoFim = $arr[0];


        $arrayDados = array();
        $i = 0;
        $anoTotal = 0;

        foreach ($arrayDefesas as $arrDefesa) {
            $igual = false;
            $mestrado = false;
            $doutorado = false;

            foreach ($arrayDados as $arrDado) {
                if ($arrDefesa['id_tipo_curso'] == 3)
                    $mestrado = true;

                if ($arrayDefesa['id_tipo_curso'] == 5)
                    $doutorado = true;

                if ($arrDado['id_pessoa'] == $arrDefesa['id_pessoa'] && ($doutorado == true || $mestrado == true))
                    $igual = true;
            }

            if (!$igual) {
                $arrayDados[$i]['nome'] = $arrDefesa['nome'];
                $arrayDados[$i]['id_pos_graduacao'] = $arrDefesa['id_pos_graduacao'];
                $arrayDados[$i]['id_inscricao'] = $arrDefesa['id_inscricao'];
                $arrayDados[$i]['id_tipo_curso'] = $arrDefesa['id_tipo_curso'];
                $arrayDados[$i]['data_inscricao'] = $arrDefesa['data_inscricao'];
                $arrayDados[$i]['data_defesa'] = $arrDefesa['data_defesa'];
                $arrayDados[$i]['id_pessoa'] = $arrDefesa['id_pessoa'];
                $arrayDados[$i]['tempo_medio'] = $this->calculaData($arrDefesa['data_inscricao'], $arrDefesa['data_defesa']);
                $arrayDados[$i]['data_inscricao_mask'] = $arrDefesa['data_inscricao_mask'];
                $arrayDados[$i]['data_defesa_mask'] = $arrDefesa['data_defesa_mask'];

                $total = $total + $arrayDados[$i]['tempo_medio'];

                $arr = explode('-', $arrDefesa['data_inscricao']);
                $anoInscricao = $arr[0];

                $arr = explode('-', $arrDefesa['data_defesa']);
                $anoDefesa = $arr[0];


                while ($anoInicio <= $anoDefesa) {
                    if ($anoInicio >= $anoInscricao) {
                        $zb = new Zend_Db_Table();

                        $dbAdapter1 = $zb->getAdapter();
                        $sql1 = "SELECT * FROM v_curso_defesa WHERE data_inscricao >= '$anoInicio' AND data_defesa <= '$anoInicio' ;";
                        $result = $dbAdapter1->query($sql1);
                        $result = $result->fetchAll();
                    }
                    $anoInicio++;
                }
            }
            $i++;
        }

        $headers = array('nome' => 'Nome',
            'data_inscricao_mask' => 'Data de inscrição',
            'data_defesa_mask' => 'Data de defesa',
            'tempo_medio' => 'Tempo médio');

        //ultimo parametro false é opcional, caso true exibe array de dados
        $flex = $GridPDF->exibicaoFinal('', '', '', $headers, $arrayDados);
        $this->view->flex = $flex;
        $this->view->dataInicio = $dataInicio;
        $this->view->dataFim = $dataFim;
        $this->view->tipo = $tipo_curso;

        if (count($arrayDefesas) > 1)
            $registros = (count($arrayDefesas) - 1);
        else
            $registros= ( count($arrayDefesas));

        //calculo de media
        $this->view->total = number_format($total / $registros, 2);


        $dtInicio = $mask->getArrayData($mask->datetimeSQLToMascaraData($dataInicio));
        $dtFim = $mask->getArrayData($mask->datetimeSQLToMascaraData($dataFim));

        $dataIn = (int) $dtInicio['ano'];
        $htmlData = "";

        while ($dataIn <= (int) $dtFim['ano']) {
            $zb = new Zend_Db_Table();

            $dbAdapter1 = $zb->getAdapter();
            $sql1 = "SELECT * FROM v_curso_defesa WHERE YEAR(data_defesa) ='$dataIn' ";
            if ($tipo_curso != "")
                $sql1.=" AND id_tipo_curso='$tipo_curso';";
            $result = $dbAdapter1->query($sql1);
            $results = $result->fetchAll();

            $total = 0;
            $aux = 0;
            foreach ($results as $r) {
                if ($r['data_inscricao'] != "" && $r['data_defesa'] != "") {
                    $mediaData = $this->calculaData($r['data_inscricao'], $r['data_defesa']);
                    $total = $total + (int) $mediaData;
                    $aux++;
                }
            }
            $htmlData.=$dataIn;
            $htmlData.=': ';

            if ((int) $total != 0) {
                if (count($result) != 0)
                    $htmlData.=number_format($total / $aux, 2);
                //$htmlData.=number_format($total/$aux,2);
            }
            else
                $htmlData.='0';

            $htmlData.='<br/>';

            $dataIn++;
            $i++;
        }
        $this->view->mediaAno = ($htmlData);
    }

    function SomarMeses($data, $meses) {

        $data = explode("/", $data);

        $aux = $data[1] + $meses;
        $ano = $aux / 13;
        $ano = (int) $ano;
        $ano = $ano + $data[2];
        $mes = ($aux % 12);
        if ($mes == 0)
            $mes = 12;
        if ($mes < 10)
            $mes = '0' . $mes;
        $newData = $data[0] . '/' . $mes . '/' . $ano;
        return $newData;
    }

    function ComparaData($data1, $tipo, $data2) {
        $aux = explode('/', $data1);
        $n_data1 = $aux[2] . $aux[1] . $aux[0];

        $aux = explode('/', $data2);
        $n_data2 = $aux[2] . $aux[1] . $aux[0];

        if ($tipo == '>=') {
            if ($n_data1 >= $n_data2)
                return true;
            else
                return false;
        }
        if ($tipo == '<=') {
            if ($n_data1 <= $n_data2)
                return true;
            else
                return false;
        }
    }

}
