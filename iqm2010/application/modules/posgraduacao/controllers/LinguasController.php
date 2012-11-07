<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Linguas.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/ProficienciaLingua.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');

class Posgraduacao_LinguasController extends Zend_Controller_Action {

    public function init() {
        $p = new Models_General();
        $result = $p->getById('id_pos_graduacao', $this->_request->getParam("id_pos_graduacao"));
        $this->view->nomePessoa = $result['nome'];
        $general = new Models_General();
        $this->view->instituicoes = $general->listView('v_instituicoes', array('id', 'instituicao'));
    }

    public function indexAction() {
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->view->tipoPosGraduacao = $idTipoCurso;

        $idPessoa = $this->_request->getParam("id_pessoa");

        /* TROCA `id_pessoa` POR `id_pos_graduacao` */
//        if($this->_request->getParam("id_pessoa")!="")
//		{
//			$id_pessoa = $this->_request->getParam("id_pessoa");
//			$pos_graduacoes = new Application_Models_Pos_Graduacoes();
//			$resultPos = $pos_graduacoes->getPosByPessoa($this->_request->getParam("id_pessoa"),$idTipoCurso);
//			foreach($resultPos as $rowResult)
//        	{
//        		$id_pos_graduacao = $rowResult['id_pos_graduacao'];
//        	}
//        }
//		if($id_pos_graduacao!='')$this->_redirect('/posgraduacao/atividades/index/id_pos_graduacao/'.$id_pos_graduacao.'/id_pessoa/'.$idPessoa);

        $pChecker = new ParamChecker();
        $tipoCurso = $pChecker->getParam(array(
                    'rel_table' => 'tipo_cursos',
                    'need' => 'slug',
                    'value' => $idTipoCurso,
                    'where' => "id_tipo_curso = '$idTipoCurso'"
                ));

        $this->view->tipocurso = strtolower($tipoCurso);


        $data = new Application_Models_Proficiencia_Linguas();

        if ($this->_request->getParam("convalidado") == 'on') {

            $convalidado = '1';
        } else {

            $convalidado = '0';
        }
        if ($this->getRequest()->isPost() && $this->_request->getParam("id_proficiencia") != 0) {


            $params = array(
                'id_lingua' => $this->_request->getParam("linguaU"),
                'aprovado' => $this->_request->getParam("aprovadoU"),
                'data' => $this->_request->getParam("datapro"),
            	'dataconv' => $this->_request->getParam("dataconv"),
                'convalidado' => $convalidado,
                'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
                'id_instituicao' => $this->_request->getParam("instituicoesU")
            );

            $data->CRUDupdate($params, $this->_request->getParam("id_proficiencia"));
            //$this->view->alert = 'Dados alterados com sucesso';
        } elseif ($this->getRequest()->isPost()) {
            $params = array(
                'id_lingua' => $this->_request->getParam("lingua"),
                'aprovado' => $this->_request->getParam("aprovado"),
                'aprovado' => $this->_request->getParam("aprovado"),
                'data' => $this->_request->getParam("data"),
                'convalidado' => $convalidado,
            	'dataconv' => $this->_request->getParam("dataconv"),
                'id_pos_graduacao' => $this->_request->getParam("id_pos_graduacao"),
                'id_instituicao' => $this->_request->getParam("instituicoes")
            );
            $data->CRUDcreate($params);

            $idTipoCurso = $this->_request->getParam("tipo");
            $this->_redirect('/posgraduacao/linguas/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso . '?alert=Dados gravados com sucesso.');
        }
        $linguas = new Application_Models_Linguas();
        $lingua = $linguas->CRUDread();


        foreach ($lingua as $r) {
            $result[$r['id_lingua']] = $r['lingua'];
        }

        $this->view->linguas = $result;
        $id = $this->_request->getParam("id_pos_graduacao");

        $grid = $data->getProficienciaByIdAluno($id);

        foreach ($grid as $index => $g) {
            if ($g['aprovado'] == 0)
                $grid[$index]['aprovado_msg'] = "Sim";
            elseif ($g['aprovado'] == 1)
                $grid[$index]['aprovado_msg'] = "Não";
            elseif ($g['aprovado'] == 2)
                $grid[$index]['aprovado_msg'] = "Convalidado";
                
           if ($g['convalidado'] == 0)
                $grid[$index]['convalidado_msg'] = "Não";
            elseif ($g['convalidado'] == 1)
                $grid[$index]['convalidado_msg'] = "Sim";

            $idioma = $linguas->getIdioma($g['id_lingua']);
            $grid[$index]['lingua'] = $idioma[lingua];
        }

        $this->view->grid = $grid;
    }

    function deleteAction() {
        $this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");

        $data = new Application_Models_Proficiencia_Linguas();
        $data->CRUDdelete($this->_request->getParam("id"));
        $this->view->idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        $idTipoCurso = $this->_request->getParam("tipo");
        $this->_redirect('/posgraduacao/linguas/index/id_pos_graduacao/' . $this->_request->getParam("id_pos_graduacao") . '/tipo/' . $idTipoCurso);
    }

}