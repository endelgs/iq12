<?php
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Docente.php');
require_once(APPLICATION_PATH . '/models/DetalhesDocente.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/Alunos.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/../library/graficos_google/grafico-google.php');

class Estatisticas_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        
    }

    public function inscricoesAction() {

        $table = new Table();

        $array = $table->read("Select  count(*) as qtd, periodo, ano
                               from v_grafico_listagem_inscricoes
                               
                               where 
                               periodo<>'' AND periodo IS NOT NULL AND 
                               ano>1900 AND ano<2100 
                               group by ano, periodo;");

        $header = array('x' => 'periodo',
            'y' => 'qtd',
            'z' => 'ano'
        );

        $arrayListagem = $table->read("Select  nome , periodo, ano
                                     from v_grafico_listagem_inscricoes 
                                     where 
                                       periodo<>'' AND periodo IS NOT NULL AND 
                                        ano>1900 AND ano<2100  
                                    ");

        $headerListagem = array(
            'nome' => 'Nome',
            'periodo' => 'Período',
            'ano' => 'Ano',
        );

        //em media será o nome da que retorna um ano
        $colunaFiltro = 'ano';

        $state = '{"dimensions":{"iconDimensions":["dim0"]},"time":"2000","iconKeySettings":[{"key":{"dim0":"1° semestre"}},{"key":{"dim0":"2° semestre"}}],"playDuration":15000,"iconType":"VBAR","nonSelectedAlpha":0.4,"xLambda":1,"yAxisOption":"2","showTrails":false,"yZoomedDataMax":2,"orderedByX":true,"orderedByY":false,"uniColorForNonSelected":false,"xAxisOption":"_ALPHABETICAL","duration":{"timeUnit":"Y","multiplier":1},"xZoomedDataMin":0,"yLambda":1,"yZoomedIn":false,"xZoomedDataMax":2,"colorOption":"_UNIQUE_COLOR","xZoomedIn":false,"yZoomedDataMin":0,"sizeOption":"_UNISIZE"}';
        $labelEixoX = 'Inscrições';
        $labelEixoY = 'Alunos';
        $titulo = "Inscritos por semestre e ano";
        $this->view->html = Grafico::get($labelEixoX, $labelEixoY, $array, $header, $state, $arrayListagem, $headerListagem, $colunaFiltro, $titulo);
    }

    private function arrayGraficoAlunosAtivos($ano, $tipo_curso) {

        $alunos = new Application_Models_Alunos();
        $param = array(
            'ativos' => 'sim',
            'periodo' => 'sim',
            'data_de' => $ano . '-01-01',
            'data_ate' => $ano . '-12-31',
            'curso' => $tipo_curso,
            'params' => ', pes.nome AS nome_pessoa, pes.cpf , ingr.ra',
            'sql' => ' LEFT JOIN pessoas pes on pes.id_pessoa = pg.id_pessoa
						 LEFT JOIN ingressos ingr ON ingr.id_pos_graduacao = pg.id_pos_graduacao',
                //	'and'		=> 'AND ps.nome LIKE "%'.$nome.'%"'
        );
        $arrAlu = $alunos->listaalunos($param);

        return $arrAlu;
    }

    public function alunosativosAction() {

        $table = new Table();


        $array = $table->read('SELECT MIN(`menor_data`) as menor_ano, MAX(`maior_data`) as maior_ano 
                               FROM v_anos_min_max_ingresso_reingresso ');

        $menorAno = (int) $array[0]['menor_ano'];
        $maiorAno = (int) $array[0]['maior_ano'];

        $arrFinalListagem = array();
        for ($i = $menorAno; $i <= $maiorAno; $i++) {

            $arrMestrado = $this->arrayGraficoAlunosAtivos($i, 3);
            $arrDoutorado = $this->arrayGraficoAlunosAtivos($i, 5);

            $arrGrafico[] = array(
                'qtd' => count($arrMestrado),
                'ano' => $i,
                'tipo_curso' => 'Mestrado'
            );

            $arrGrafico[] = array(
                'qtd' => count($arrDoutorado),
                'ano' => $i,
                'tipo_curso' => 'Doutorado'
            );

            $arrTodos = $this->arrayGraficoAlunosAtivos($i, 'todos');

            foreach ($arrTodos as $index => $a) {

                $arrFinalListagem[] = array(
                    'tipocurso' => $a['curso'],
                    'nome' => $a['nome_pessoa'],
                    'ano' => $i,
                );
            }
        }

        $header = array('x' => 'tipo_curso',
            'y' => 'qtd',
            'z' => 'ano'
        );


        $headerListagem = array(
            'nome' => 'Nome',
            'tipocurso' => 'Tipo de Curso',
            'ano' => 'Ano',
        );

        //em media será o nome da que retorna um ano
        $colunaFiltro = 'ano';

        $state = '{"xZoomedDataMax":2,"sizeOption":"_UNISIZE","orderedByY":false,"dimensions":{"iconDimensions":["dim0"]},"time":"2009","playDuration":15000,"xAxisOption":"_ALPHABETICAL","iconKeySettings":[{"key":{"dim0":"Doutorado"}},{"key":{"dim0":"Mestrado"}}],"xLambda":1,"yZoomedDataMin":0,"xZoomedDataMin":0,"yZoomedDataMax":2,"orderedByX":true,"uniColorForNonSelected":false,"duration":{"multiplier":1,"timeUnit":"Y"},"showTrails":false,"yLambda":1,"yZoomedIn":false,"nonSelectedAlpha":0.4,"iconType":"VBAR","yAxisOption":"2","colorOption":"_UNIQUE_COLOR","xZoomedIn":false}';
        $labelEixoX = 'Tipo de curso';
        $labelEixoY = 'Alunos';
        $titulo = "Alunos por tipo de curso e ano";
        $this->view->html = Grafico::get($labelEixoX, $labelEixoY, $arrGrafico, $header, $state, $arrFinalListagem, $headerListagem, $colunaFiltro, $titulo);
    }

    public function defesasAction() {

        $table = new Table();

        $array = $table->read("Select  count(*) as qtd, tipo_curso , ano
                               from v_grafico_listagem_defesas 
                               where 
                               tipo_curso<>'' AND tipo_curso IS NOT NULL AND 
                               ano>1900 AND ano<2100 
                               group by ano, tipo_curso;");

        $header = array('x' => 'tipo_curso',
            'y' => 'qtd',
            'z' => 'ano'
        );

        $arrayListagem = $table->read("Select  nome , tipo_curso, ano
                                      from v_grafico_listagem_defesas                               where 
                                     tipo_curso<>'' AND tipo_curso IS NOT NULL AND 
                                     ano>1900 AND ano<2100 
                                      ");

        $headerListagem = array(
            'nome' => 'Nome',
            'tipo_curso' => 'Curso',
            'ano' => 'Ano',
        );

        //em media será o nome da que retorna um ano
        $colunaFiltro = 'ano';

        $state = '{"orderedByY":false,"dimensions":{"iconDimensions":["dim0"]},"colorOption":"_UNIQUE_COLOR","iconKeySettings":[{"key":{"dim0":"Doutorado"}},{"key":{"dim0":"Mestrado"}}],"playDuration":15088.88888888889,"iconType":"VBAR","xLambda":1,"yZoomedDataMin":0,"showTrails":false,"yZoomedDataMax":1,"nonSelectedAlpha":0.4,"time":"2000","uniColorForNonSelected":false,"duration":{"timeUnit":"Y","multiplier":1},"orderedByX":true,"yLambda":1,"yZoomedIn":false,"xZoomedDataMax":2,"yAxisOption":"2","xZoomedIn":false,"xAxisOption":"_ALPHABETICAL","xZoomedDataMin":0,"sizeOption":"_UNISIZE"}';
        $labelEixoX = 'Defesas';
        $labelEixoY = 'Alunos';
        $titulo = "Defesas por curso e ano";
        $this->view->html = Grafico::get($labelEixoX, $labelEixoY, $array, $header, $state, $arrayListagem, $headerListagem, $colunaFiltro, $titulo);
    }

    public function orientadoresAction() {
        $table = new Table();


        $array = array();


        $arrayAlunos = $table->read('SELECT `nome_aluno`, id_pos_graduacao, 
                                        min(ano) as ano_min, 
                                        max(ano_integralizacao) as ano_max
                                        FROM `v_grafico_listagem_orientador`
                   
                                           GROUP BY `nome_aluno`, id_pos_graduacao 
                                           ORDER BY `nome_aluno`, id_pos_graduacao  
                                          ');
        $arrList = array();
        foreach ($arrayAlunos as $index1 => $a) {

            $anoMax = (int) $a['ano_max'];
            $anoMin = (int) $a['ano_min'];
            $j = 0;
            for ($i = $anoMin; $i <= $anoMax; $i++) {

                $sql = "SELECT * FROM `v_grafico_listagem_orientador`
                        WHERE 
                        ano<='$i' 
                        AND '$i'<=ano_integralizacao
                        AND id_pos_graduacao='" . $a['id_pos_graduacao'] . "'
                        ORDER BY ano ASC";

                $arrayOri = $table->read($sql);
                if ($arrayOri[$j]['ano'] == $i) {
                    $atual = $arrayOri[$j];
                    $j++;
                }

                $atual['ano_atual'] = $i;
                $arrList[] = $atual;
            }
        }


        $arrGrafico = array();
        $arrF = array();
        foreach ($arrList as $index => $a) {

            $arrF[$a["id_professor"]][$a["ano_atual"]]+=1;
        }

        foreach ($arrF as $index => $a) {

            $sql = "SELECT * FROM `v_grafico_listagem_orientador`
                        WHERE 
                        id_professor 	='" . $index . "'

                   ";

            $arrayOri = $table->read($sql);

            foreach ($a as $ano => $qtd) {
                $arrGrafico[] = array(
                    'orientador' => $arrayOri[0]['nome_orientador'],
                    'qtd' => $qtd,
                    'ano' => $ano
                );
            }
        }

//        echo '<ore>';
//        var_dump($arrList);
//        var_dump($arrF);
//        var_dump($arrGrafico);
//        exit;
        $header = array('x' => 'orientador',
            'y' => 'qtd',
            'z' => 'ano'
        );

        $headerListagem = array(
            'nome_aluno' => 'Aluno',
            'nome_orientador' => 'Orientador',
            'ano_atual' => 'Ano',
        );

        //em media será o nome da que retorna um ano
        $colunaFiltro = 'ano_atual';
        $ordenar = 'nome_orientador';

        $state = '{"nonSelectedAlpha":0.4,"dimensions":{"iconDimensions":["dim0"]},"orderedByY":false,"iconKeySettings":[],"yZoomedDataMax":2,"iconType":"VBAR","xLambda":1,"yZoomedDataMin":0,"showTrails":false,"playDuration":15000,"time":"2000","uniColorForNonSelected":false,"duration":{"timeUnit":"Y","multiplier":1},"xZoomedDataMin":0,"yLambda":1,"xAxisOption":"_ALPHABETICAL","xZoomedDataMax":2,"orderedByX":true,"colorOption":"_UNIQUE_COLOR","xZoomedIn":false,"yZoomedIn":false,"yAxisOption":"2","sizeOption":"_UNISIZE"}';
        $labelEixoX = 'Orientador';
        $labelEixoY = 'Alunos';
        $titulo = "Alunos por orientador e ano";
        $this->view->html = Grafico::get($labelEixoX, $labelEixoY, $arrGrafico, $header, $state, $arrList, $headerListagem, $colunaFiltro, $titulo, $ordenar);
    }

    public function bolsaAction() {

        $table = new Table();

        $arrayAnos = $table->read('SELECT max( `ano_fim` ) AS max_ano, min( `ano_inicio` ) AS min_ano
                                   FROM `v_grafico_listagem_bolsas`;');
        $array = array();

        $arrayAgencias = $table->read('SELECT `agencia` 
                                       FROM `v_grafico_listagem_bolsas` 
                                       GROUP BY `agencia` 
                                       ORDER BY `agencia`');

        $anoMax = (int) $arrayAnos[0]['max_ano'];
        $anoMin = (int) $arrayAnos[0]['min_ano'];
        $arrGrafico = array();
        $arrFinalListagem = array();
        foreach ($arrayAgencias as $a) {
            for ($i = $anoMin; $i <= $anoMax; $i++) {

                $arrayAgenciaAno = $table->read("SELECT * 
                                            FROM `v_grafico_listagem_bolsas` 
                                            WHERE `agencia`='" . $a["agencia"] . "' 
                                            AND `ano_inicio`<='$i' 
                                            AND '$i'<=`ano_fim`");

                if ($arrayAgenciaAno)
                    $arrGrafico[] = array(
                        'qtd' => count($arrayAgenciaAno),
                        'ano' => $i,
                        'agencia' => $a["agencia"]
                    );

                foreach ($arrayAgenciaAno as $index2 => $b) {

                    $arrFinalListagem[] = array(
                        'agencia' => $a["agencia"],
                        'nome' => $b['nome'],
                        'ano' => $i,
                    );
                }
            }
        }

        $header = array('x' => 'agencia',
            'y' => 'qtd',
            'z' => 'ano'
        );
        $headerListagem = array(
            'nome' => 'Nome',
            'agencia' => 'Agência',
            'ano' => 'Ano',
        );

        //em media será o nome da que retorna um ano
        $colunaFiltro = 'ano';

        $state = '{"xZoomedDataMax":2,"dimensions":{"iconDimensions":["dim0"]},"iconKeySettings":[],"playDuration":15000,"orderedByY":false,"nonSelectedAlpha":0.4,"xLambda":1,"yZoomedDataMin":0,"xZoomedDataMin":0,"yZoomedDataMax":1,"iconType":"VBAR","uniColorForNonSelected":false,"duration":{"timeUnit":"Y","multiplier":1},"showTrails":false,"yLambda":1,"yZoomedIn":false,"time":"2000","yAxisOption":"2","colorOption":"_UNIQUE_COLOR","xZoomedIn":false,"orderedByX":true,"xAxisOption":"_ALPHABETICAL","sizeOption":"_UNISIZE"}';
        $labelEixoX = 'Bolsas';
        $labelEixoY = 'Alunos';
        $titulo = "Bolsas por agência e ano";
        $this->view->html = Grafico::get($labelEixoX, $labelEixoY, $arrGrafico, $header, $state, $arrFinalListagem, $headerListagem, $colunaFiltro, $titulo);
    }

}