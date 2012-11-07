<?php

// A LINHA ABAIXO DEVERA SER REMOVIDA ASSIM QUE A QUESTAO DO MODEL FOR RESOLVIDA
require_once(APPLICATION_PATH . '/models/General.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/models/vpessoas.php');
require_once(APPLICATION_PATH . '/models/Profissionais.php');
require_once(APPLICATION_PATH . '/models/Residenciais.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/modules/default/lib/ParamChecker.lib.php');

class Pessoas_GeralController extends Zend_Controller_Action {

    public function init() {
        $p = new Models_General();
        $clsPessoa = new Application_Models_Pessoas();
        if ($this->_request->getParam("id_pessoa") != "") {
            $result = $clsPessoa->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nomePessoa = $result['nome'];
        }
    }

    public function indexAction() {
        $idPessoa = $this->_request->getParam("id_pessoa");
        $idTipoCurso = $this->_request->getParam("tipo");
        $idPosGraduacao = $this->_request->getParam("id_pos_graduacao");
        if (empty($idPessoa)) {
            $pChecker = new ParamChecker();
            $idPessoa = $pChecker->getParam(array(
                        'rel_table' => 'pos_graduacoes',
                        'need' => 'id_pessoa',
                        'value' => $idPosGraduacao,
                        'where' => "id_pos_graduacao = '$idPosGraduacao'"
                    ));
            if ($idPessoa != '')
                $this->_redirect('/pessoas/geral/index/id_pessoa/' . $idPessoa);
        }



        $pos_graduacoes = new Application_Models_Pos_Graduacoes();

        if ($this->_request->getParam("id_pos_graduacao") != "") {
            $resultPos = $pos_graduacoes->getPessoaByPos($this->_request->getParam("id_pos_graduacao"), $idPessoa);
            $id_pessoa = '';
            foreach ($resultPos as $rowResult) {
                $id_pessoa = $rowResult['id_pessoa'];
            }
        }
        if ($id_pessoa != '')
            $this->_redirect('/pessoas/geral/index/id_pessoa/' . $id_pessoa);

        $general = new Models_General();
        $this->view->cidades = $general->listView('v_cidades_uf', array('id', 'cidade_uf'));
        $this->view->paises = $general->listTable('paises', array('id_pais', 'pais'));
        $this->view->estadosCivis = $general->listTable('estados_civis', array('id_estado_civil', 'estado_civil'));

        if ($this->getRequest()->isPost()) {
            $pessoa = new Application_Models_Pessoas();
            $data = array(
                //'id_pessoa'           => $_REQUEST['id_pessoa'],
                'nome' => $_REQUEST['nome'],
                'sexo' => (int) $_REQUEST['sexo'],
                'nascimento' => $this->toMysqlDateFull($_REQUEST['nascimento']),
                'id_estado_civil' => (int) $_REQUEST['estadoCivil'],
                'id_pais' => (int) $_REQUEST['nacionalidade'],
                'cpf' => str_replace('.', '', str_replace('-', '', $_REQUEST['cpf'])),
                'rg' => $_REQUEST['rg'],
                'rg_data_emissao' => $this->toMysqlDateFull($_REQUEST['rgDataEmissao']),
                'rg_orgao_expeditor' => $_REQUEST['rgOrgaoEmissor'],
                'rg_uf' => $_REQUEST['unidadeFederativa'],
                'rne' => $_REQUEST['rne'],
                'passaporte' => $_REQUEST['passaporte'],
                'deletado' => '0',
                'email_principal' => $_REQUEST['email1'],
                'email_secundario' => $_REQUEST['email2'],
                'id_local_nascimento' => $_REQUEST['id_local_nascimento'],
                'lattes' => $_REQUEST['lattes'],
                'pai' => $_REQUEST['pai'],
                'mae' => $_REQUEST['mae'],
            );
            /* print_r($data);
              die(); */
            if ($_REQUEST['id_pessoa'] != 0) {
                $idPessoa = $_REQUEST['id_pessoa'];
                $pessoa->updatePessoa($data, $idPessoa);
            } else {
                /* print_r($_REQUEST);
                  print_r($data); */
                //die();
                $idPessoa = $pessoa->addPessoa($data);
            }
            $dataPro = array(
                'nome_ultima_empresa' => $_REQUEST['ultimaEmpresa'],
                'id_pessoa' => $idPessoa,
                'cargo' => $_REQUEST['ultimaCargo'],
                'data_termino' => $this->toMysqlDateFull($_REQUEST['ultimaTermino']),
                'data_inicio' => $this->toMysqlDateFull($_REQUEST['ultimaInicio']),
                'trabalhando' => (int) $_REQUEST['ultimaAinda'],
                'telefone_comercial' => $_REQUEST['ultimaTelefone'],
                'endereco' => $_REQUEST['ultimaEndereco'],
                'cep' => $_REQUEST['ultimaCep'],
                'id_cidade' => $_REQUEST['cidadecomercial'],
                'nome_penultima_empresa' => $_REQUEST['penultimaEmpresa'],
                'penultimo_cargo' => $_REQUEST['penultimaCargo'],
                'penultimo_data_inicio' => $this->toMysqlDateFull($_REQUEST['penultimaInicio']),
                'penultimo_data_termino' => $this->toMysqlDateFull($_REQUEST['penultimaTermino']),
                'caixa_postal' => $_REQUEST['ultimaCP']
            );
            $profissionais = new Application_Models_Profissionais();
            if ($_REQUEST['id_pessoa'] != 0) {
                $profissionais->updateProfissional($dataPro, $idPessoa);
            } else {
                $idProfissional = $profissionais->addProfissional($dataPro);
                //print_r($dataPro);
                //die($idProfissional);
            }
            $dataRes = array(
                'endereco' => $_REQUEST['endereco'],
                'id_cidade' => $_REQUEST['cidade'],
                'telefone' => $_REQUEST['telefoneResidencial'],
                'celular' => $_REQUEST['celular'],
                'cep' => $_REQUEST['cep'],
                'caixa_postal' => $_REQUEST['caixaPostal'],
                'deletado' => 0,
                'id_pessoa' => $idPessoa,
                'complemento' => $_REQUEST['complemento'],
            );
            $residenciais = new Application_Models_Residenciais();
            if ($_REQUEST['id_pessoa'] != 0) {
                $residenciais->updateResidencial($dataRes, $idPessoa);
            } else {
                $idResidencial = $residenciais->addResidencial($dataRes);
            }
            $redirector = $this->_helper->getHelper('redirector');
            $this->_redirect('/pessoas/geral/index/id_pessoa/' . $idPessoa . '?alert=Dados gravados com sucesso.');

            //$redirector->gotoSimpleAndExit('index', 'geral', 'pessoas', array('id_pessoa' => $idPessoa). '?alert=Dados gravados com sucesso.');
            //$this->view->alert = 'Dados inseridos com sucesso';
        }

        if ($this->_request->getParam("id_pessoa") != "") {
            $pessoas = new Application_Models_VPessoas();
            $pessoa = $pessoas->getPessoa($this->_request->getParam("id_pessoa"));
            $this->view->nome = $pessoa['nome'];
            $this->view->sexo = $pessoa['sexo'];
            $this->view->nascimento = $pessoa['nascimento'];
            $this->view->estadocivil = $pessoa['id_estado_civil'];
            $this->view->nacionalidade = $pessoa['id_pais'];
            $this->view->cpf = substr($pessoa['cpf'], 0, 3) . '.' . substr($pessoa['cpf'], 3, 3) . '.' . substr($pessoa['cpf'], 6, 3) . '-' . substr($pessoa['cpf'], 9, 2);
            $this->view->rg = $pessoa['rg'];
            $this->view->unidadeFederativa = $pessoa['rg_uf'];
            $this->view->rgorgemissor = $pessoa['rg_orgao_expeditor'];
            $this->view->rgdataemissao = $pessoa['rg_data_emissao'];
            $this->view->passaporte = $pessoa['passaporte'];
            $this->view->rne = $pessoa['rne'];
            $this->view->lattes = $pessoa['lattes'];
            $this->view->emailprincipal = $pessoa['email_principal'];
            $this->view->emailsegundo = $pessoa['email_secundario'];
            $this->view->idLocalNascimento = $pessoa['id_local_nascimento'];
            $this->view->endereco = $pessoa['endereco'];
            $this->view->cep = $pessoa['cep'];
            $this->view->caixapostal = $pessoa['caixa_postal'];
            $this->view->cidade = $pessoa['id_cidade'];
            $this->view->telefone = $pessoa['telefone'];
            $this->view->celular = $pessoa['celular'];

            $this->view->complemento = $pessoa['complemento'];
            $this->view->pai = $pessoa['pai'];
            $this->view->mae = $pessoa['mae'];

            $this->view->ultimaempresa = $pessoa['nome_ultima_empresa'];
            $this->view->cargo = $pessoa['cargo'];
            $this->view->datainicio = $pessoa['data_inicio'];
            $this->view->datatermino = $pessoa['data_termino'];
            $this->view->trabalhando = $pessoa['trabalhando'];
            $this->view->telefonecomercial = $pessoa['telefone_comercial'];
            $this->view->enderecocomercial = $pessoa['enderecocomercial'];
            $this->view->cepcomercial = $pessoa['cepcomercial'];
            $this->view->ultimaCP = $pessoa['profissionalCP'];
            $this->view->cidadecomercial = $pessoa['cidadecomercial'];
            $this->view->penultimaempresa = $pessoa['nome_penultima_empresa'];
            $this->view->penultimocargo = $pessoa['penultimo_cargo'];
            $this->view->penultimodatainicio = $pessoa['penultimo_data_inicio'];
            $this->view->penultimodatatermino = $pessoa['penultimo_data_termino'];
            $this->view->id_pessoa = $this->_request->getParam("id_pessoa");

            /* print_r($pessoa); */
        }
        if ($this->view->nacionalidade == "") {
            $this->view->nacionalidade = '27';
        }
    }

    public function postAction() {
        
    }

    public function deleteAction() {
        //die();
        //print_r($this->_request->getParam("id_pessoa"));
        $pessoas = new Application_Models_Pessoas();
        $pessoas->deletePessoa($this->_request->getParam("id_pessoa"));
    }

    public function toMysqlDate($string) {
        $a = explode('/', $string);
        $b = $a[1] . '-' . $a[0] . '-01 : 00:00:00';
        return $b;
    }

    public function toMysqlDateFull($string) {
        $a = explode('/', $string);
        $b = $a[2] . '-' . $a[1] . '-' . $a[0] . ' 00:00:00';
        return $b;
    }

}