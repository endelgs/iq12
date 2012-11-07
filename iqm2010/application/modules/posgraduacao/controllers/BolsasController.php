<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Bolsas.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');

class Posgraduacao_BolsasController extends Zend_Controller_Action {

    public function init() {
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->view->tipoPosGraduacao = $idTipoCurso;



        // Essa eh a parte do codigo onde eu verifico os parametros
        $idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $idPessoa = $this->_request->getParam("id_pessoa");

        $pChecker = new ParamChecker();
        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);

        //		// Se nao vier o id_pos_graduacao, uso o ParamChecker que eu fiz
//		if(empty($idPosGraduacao))
//		{
//			$pChecker = new ParamChecker();
//			$idPosGraduacao = $pChecker->getParam(array(
//				'rel_table' => 'pos_graduacoes',
//				'need'		=> 'id_pos_graduacao',
//				'value'		=> $idPessoa,
//				'where'		=> "id_pessoa = '$idPessoa' AND id_tipo_curso = $idTipoCurso"
//			));
//			$this->_redirect('/posgraduacao/bolsas/index/id_pos_graduacao/'.$idPosGraduacao.'/id_pessoa/'.$idPessoa);
//		}

        $p = new Application_Models_Bolsas();

        $grid = $p->getByField('id_posgraduacao', $idPosGraduacao);
        //var_dump($grid);
        //exit;
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }


        foreach ($grid as $index => $g) {
            $grid[$index]['tempo'] = $grid[$index]['tempo'] . " mes(es)";
            $grid[$index]['tempo_susp'] = $grid[$index]['tempo_susp'] . " mes(es)";
            $datas = $p->CRUDreadSelect($g['id']);
            $grid[$index]['tempousobolsa'] = $this->calculaTempoBolsa($datas[0]) . " mes(es)";

//			var_dump($p->CRUDreadSelect($g['id']));
//			exit;
        }


//		$grid['tempo_total_bolsa']=$p->CRUDreadSelect('id_posgraduacao', $idPosGraduacao);
       // var_dump($grid);
        $this->view->bolsas = $grid;
        //
        //        $p = new Models_General();
        //        $result = $p->getById('id_pos_graduacao', $this->_request->getParam("id_pos_graduacao"));
        //
		//        $general = new Models_General();
        //    	if($this->_request->getParam("id_pessoa")!="")
        //		{
        //			$id_pessoa = $this->_request->getParam("id_pessoa");
        //			$pos_graduacoes = new Application_Models_Bolsas();
        //			$resultPos = $pos_graduacoes->getPosByPessoa($this->_request->getParam("id_pessoa"),3);
        //			foreach($resultPos as $rowResult)
        //        	{
        //        		$id_pos_graduacao = $rowResult['id_pos_graduacao'];
        //        	}
        //        }
        //        if($id_pos_graduacao!='')$this->_redirect('/posgraduacao/bolsas/index/id_pos_graduacao/'.$id_pos_graduacao);
    }

    public function indexAction() {


//				echo $this->calculaTempoBolsa();
//				exit;
//		



        $general = new Models_General();
        $this->view->agencias = $general->listView('v_agencias', array('id', 'agencia'));
        $this->view->professores = $general->listView('v_professores', array('id', 'nome'));
        $this->view->periodos = $general->listView('v_periodos', array('id', 'periodo'));
        $this->view->motivoCancelamento = $general->listView('v_motivos_cancelamento', array('id_motivo_cancelamento', 'motivo_cancelamento'));
        $this->view->motivoSuspensao = $general->listView('v_bolsa_motivo_suspensao', array('id_bolsa_suspensao_motivo', 'suspensao_motivo'));
        /* $this->view->areasDeConcentracao = $general->listView('v_areas_de_concentracao', array('id', 'area_de_concentracao'));
          $this->view->relacoesIes = $general->listView('v_relacoes_instituicao_ies', array('id', 'relacao_instituicao_ies'));
          $this->view->cidadesProva = $general->listView('v_cidade_provas', array('id', 'cidade'));
          $this->view->curso = $general->listView('v_cursos', array('id', 'curso'));
          $this->view->professores = $general->listView('v_professores', array('id', 'nome'));
          $this->view->instituicoes = $general->listView('v_instituicoes', array('id', 'instituicao')); */
    }

    public function deleteAction() {

        $id= $this->_request->getParam("id");
        
        $data = new Application_Models_Bolsas();
        $data->CRUDdelete($id);
        //$this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");       
     
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/bolsas/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

    public function postAction() {
        $data = new Application_Models_Bolsas();
        // Verifico os parametros.
        // Caso haja um id, faco update, senao, faco insert
        $cancelada = $this->_request->getParam("canceladaU");
        $cancelada = (!isset($cancelada)) ? '0' : '1';

        $suspensa = $this->_request->getParam("suspensaU");
        $suspensa = (!isset($suspensa)) ? '0' : '1';

        if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {
            $params = array(
                'id_agencia' => $this->_request->getParam("agenciaU"),
                'data_inicio' => $this->toMysqlDateFull($this->_request->getParam("datainicio")),
                'data_fim' => $this->toMysqlDateFull($this->_request->getParam("datafim")),
                'processo' => $this->_request->getParam("processoU"),
                'observacao' => $this->_request->getParam("observacoesU"),
                'id_periodo' => $this->_request->getParam("periodoU"),
                'suspensa' => $suspensa,
                'id_motivo_suspensao' => $this->_request->getParam("mSuspensaoU"),
                'suspensao_inicio' => $this->toMysqlDateFull($this->_request->getParam("suspensaoInicioU")),
                'suspensao_fim' => $this->toMysqlDateFull($this->_request->getParam("suspensaoFimU")),
                'cancelada' => $cancelada,
                'data_cancelamento' => $this->toMysqlDateFull($this->_request->getParam("dataCancelamentoU")),
                'id_motivo_cancelamento' => $this->_request->getParam("mCancelamentoU"),
            );

            $data->CRUDupdate($params, $this->_request->getParam("id"));
            $this->view->alert = 'Dado alterados com sucesso';
        } else if ($this->getRequest()->isPost()) {
            $params = array(
                'id_posgraduacao' => $this->_request->getParam("id_pos_graduacao"),
                'id_agencia' => $this->_request->getParam("agencia"),
                'data_inicio' => $this->toMysqlDateFull($this->_request->getParam("datainicio")),
                'data_fim' => $this->toMysqlDateFull($this->_request->getParam("datafim")),
                'processo' => $this->_request->getParam("processo"),
                'observacao' => $this->_request->getParam("observacoes"),
                'id_periodo' => $this->_request->getParam("periodo"),
                'suspensa' => '',
                'id_motivo_suspensao' => '1',
                'cancelada' => '',
                'id_motivo_cancelamento' => '1'
            );

            $data->CRUDcreate($params);
            $this->view->alert = 'Dado inseridos com sucesso';
        }
    }

    public function toMysqlDateFull($string) {
        $a = explode('/', $string);
        $b = $a[2] . '-' . $a[1] . '-' . $a[0] . ' 00:00:00';
        return $b;
    }

    //recebe datetime e devolte difereça de meses
    private function manipualDiferencaDatetime($data1, $data2) {
        $dias = $this->manipulaData($data1) - $this->manipulaData($data2);

//		var_dump($data1);
//		echo '<br>';
//		var_dump($data2);
//		echo '<br>';
//		var_dump($dias);

        $meses = $dias / 30;
        if ($meses < 0)
            $meses = $meses * (-1);

        return (int) $meses;
    }

    private function manipulaData($data) {
        $data = substr($data, 0, 10);
        $data = explode('-', $data);
        $tempo = $data[0] * 360 + $data[1] * 30 + $data[2];
//		var_dump($tempo);
        return $tempo;
    }

    //recebe array com datas
    //retorna o tempo que utilizou da bolsa
    private function calculaTempoBolsa($datas) {
//		$datas['data_inicio']="2004-01-20 00:00:00";
//		$datas['data_fim']="2006-02-20 00:00:00";
//		$datas['suspensao_inicio']="2010-05-05 15:15:28";
//		$datas['suspensao_fim']="2010-07-05 15:15:28";
//		$datas['data_cancelamento']="2010-01-01 15:15:28"; 
//		var_dump($datas);
//		exit;
        if ($datas['suspensa'] == 0)
            $suspensao = false;
        else
            $suspensao=true;

        if ($datas['cancelada'] == 0)
            $cancelado = false;
        else
            $cancelado=true;
//			
//		echo '<br>Suspensao:'.$suspensao;
//		echo '<br>Cancelado:'.$cancelado.'<br>';
//			
        //data de fim não pode ser menor que a de inicio, caso ocorra isso 0
//		if (strtotime($datas['data_inicio'])>=strtotime($datas['data_fim']))
//		return 0;
//			
//		//data de cancelamento não pode ser menor que inicio
//		if(strtotime($datas['data_cancelamento'])<strtotime($datas['data_inicio']))
//		return 0;
        //caso o cancelamento ocorra no meio de uma suspensao, considerar como suspensao fim
        if (($suspensao) && ($cancelado))
            if (strtotime($datas['data_cancelamento']) < strtotime($datas['suspensao_inicio'])) {
                $suspensao = false;
            }
        if (strtotime($datas['data_cancelamento']) > strtotime($datas['suspensao_fim'])) {
//					$datas['suspensao_fim']=$datas['data_cancelamento'];
        }

        $tempoBolsa = 0;

        //teve bolsa cancelada
        if ($cancelado) {
            $datas['data_fim'] = $datas['data_cancelamento'];
            $tempoBolsa = $this->tempoBolsa($datas, $suspensao);
        }else
            $tempoBolsa=$this->tempoBolsa($datas, $suspensao);
        return $tempoBolsa;
    }

    private function tempoBolsa($datas, $suspensao) {
        $dataAtual = date("Y-m-d H:i:s");
//		echo '<br>AQUI<br>';
//		var_dump($datas);
//		exit;
        if (strtotime($dataAtual) < strtotime($datas['data_fim']))
            $tempoBolsa = $this->manipualDiferencaDatetime($dataAtual, $datas['data_inicio']);
        elseif (strtotime($dataAtual) >= strtotime($datas['data_fim']))
            $tempoBolsa = $this->manipualDiferencaDatetime($datas['data_inicio'], $datas['data_fim']);

        //irá ser suspensa
        if (($suspensao) && (strtotime($dataAtual) < strtotime($datas['suspensao_inicio'])))
            $tempoBolsa = $this->manipualDiferencaDatetime($dataAtual, $datas['data_inicio']);
        //está suspensa atualmente
        elseif (($suspensao) && (strtotime($datas['suspensao_inicio']) < strtotime($dataAtual)) && (strtotime($dataAtual) < strtotime($datas['suspensao_fim']))) {
            $tempoBolsa = $this->manipualDiferencaDatetime($datas['suspensao_inicio'], $datas['data_inicio']);
        }
        //já esteve em suspensao
        elseif (($suspensao) && (strtotime($dataAtual) > strtotime($datas['suspensao_fim']))) {
//			echo 'Oi';
            $tempoSuspenso = $this->manipualDiferencaDatetime($datas['suspensao_fim'], $datas['suspensao_inicio']);
//		var_dump($tempoSuspenso);
//			exit;
            $tempoBolsa = $tempoBolsa - $tempoSuspenso;
        }

        return $tempoBolsa;
    }

}