<?php

require_once(APPLICATION_PATH . '/../library/componentes/Upload.php');
require_once(APPLICATION_PATH . '/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH . '/models/inscricoes.php');
require_once(APPLICATION_PATH . '/models/Cidade.php');
require_once(APPLICATION_PATH . '/models/Pessoas.php');
require_once(APPLICATION_PATH . '/models/paises.php');
require_once(APPLICATION_PATH . '/models/Residenciais.php');
require_once(APPLICATION_PATH . '/models/pos_graduacoes.php');
require_once(APPLICATION_PATH . '/models/TipoCursos.php');
require_once(APPLICATION_PATH . '/models/Periodos.php');
require_once(APPLICATION_PATH . '/models/AreasDeConcentracao.php');
require_once(APPLICATION_PATH . '/models/Professores.php');
require_once(APPLICATION_PATH . '/models/CidadesDaProva.php');
require_once(APPLICATION_PATH . '/models/Profissionais.php');

class Gerenciar_SigaController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $Upload = new Upload();
        $this->view->upload = $Upload->getInputFile();

        if ($this->_request->getParam('status') != "") {
            if ($this->_request->getParam('status') == 'Ok') {
                $this->view->alert = 'Arquivo enviado';
            } elseif ($this->_request->getParam('status') == 'N') {
                $this->view->alert = 'Houve algum erro interno';
            }
        }
    }

    public function csvToArray($csv, $size) {
        $v = "";
        $row = 0;
        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, $size, ",")) !== FALSE) {
                $num = count($data);

                for ($c = 0; $c < $num; $c++) {
                    $v.= $data[$c];
                }

                $v = str_replace('"', '', $v);
                $a = explode(';', $v);
                if ($row == 0) {
                    $headers = $a;
                } else {

                    foreach ($a as $index => $r) {
                        $arrF[$row - 1][$headers[$index]] = utf8_encode($r);
                    }
                }

                $v = "";
                $row++;
            }
            fclose($handle);
        }
        return $arrF;
    }

    public function postAction() {

        $status = 'N';
        if (($_FILES['file']['error'] == 0)) {
            $caminho = $_FILES['file']['tmp_name'];
            $size = $_FILES['file']['size'];
            $dados = $this->csvToArray($caminho, $size);
//			$this->geraSQL($dados);
//			exit;
            $array = $this->importDadosPessoais($dados);
            $this->importInscricoes($array);
            $status = 'Ok';
        }

        $this->_redirect('/gerenciar/siga/index/status/' . $status);
    }

    public function geraSQL($data) {

        $inscricoes = new Application_Models_Inscricoes();

        foreach ($data as $index => $d) {

            $sql = "UPDATE `pos_graduacoes` SET `id_tipo_curso` = '3' WHERE `pos_graduacoes`.`id_pos_graduacao` =(SELECT `id_pos_graduacao`
FROM `inscricoes`
WHERE `codigo_inscricao` = '" . $d['Inscricao.numero'] . "' group by id_pos_graduacao); <br>";

            echo $sql;
        }
    }

    public function importDadosPessoais($arrayCSV) {
        $clsPessoas = new Application_Models_Pessoas();
        $paises = new Application_Models_Paises();
        $cidades = new Application_Models_Cidades();
        $mask = new mascara();
        $endereco = new Application_Models_Residenciais();
        $data = $arrayCSV;
        $clsPos = new Application_Models_Pos_Graduacoes();
        /// mae
        foreach ($data as $index => $d) {
            if ($d["DadosPessoais.sexo"] == 'MASCULINO')
                $sexo = '1';
            else
                $sexo='2';

            if ($d['DadosPessoais.estadocivil'] == 'SOLTEIRO')
                $estadoCivil = 2;
            elseif ($d['DadosPessoais.estadocivil'] == 'CASADO')
                $estadoCivil = 3;
            elseif ($d['DadosPessoais.estadocivil'] == 'UNIAO_ESTAVEL')
                $estadoCivil = 7;
            elseif ($d['DadosPessoais.estadocivil'] == 'VIUVO')
                $estadoCivil = 6;
            elseif ($d['DadosPessoais.estadocivil'] == 'OUTRO')
                $estadoCivil = 1;
            elseif ($d['DadosPessoais.estadocivil'] == 'SEPARADO')
                $estadoCivil = 4;
            elseif ($d['DadosPessoais.estadocivil'] == 'DIVORCIADO')
                $estadoCivil = 4;
            else
                $estadoCivil=1;

            $natural = trim((strtolower($d['DadosPessoais.nacionalidade'])));
            $id_pais = $paises->getId($natural);

            if ($id_pais == NULL) {

                if ($natural == "brasileira" || $natural == "brasileiro") {
                    $id_pais = 27;
                } else {
                    $id_pais = 1;
                }
            }
            $dataNascimento = $d['DadosPessoais.datanascimento'];
            $dataNascimento = $mask->MascaraDataTodatetimeSQL($dataNascimento);

            $cidadeNascimento = trim($d["DadosPessoais.cidadenaturalidade"]);
            //var_dump($cidadeNascimento);
            $id_cidadeNasc = $cidades->getId($cidadeNascimento, $d['DadosPessoais.estadonaturalidade']);
           if($id_cidadeNasc==NULL)
               $id_cidadeNasc=1;
           
            $Documento = array();
            if ($d['DadosPessoais.tipodocumento'] == "RG") {
                $Documento['rg'] = $d['DadosPessoais.numerodocumento'];
                $Documento['rg_data_emissao'] = $mask->MascaraDataTodatetimeSQL($d['DadosPessoais.dataemissaodocumento']);
                $Documento['rg_orgao_expeditor'] = $d['DadosPessoais.orgaoemissor'];
                $Documento['rg_uf'] = $d['DadosPessoais.ufdocumento'];
            } elseif ($d['DadosPessoais.tipodocumento'] == "RNE") {

                $Documento['rne'] = $d['DadosPessoais.numerodocumento'];
            }



            $params = array('nome' => str_replace("'", " ", $d['DadosPessoais.nomecandidato']),
                'sexo' => $sexo,
                'nascimento' => $dataNascimento,
                'id_estado_civil' => $estadoCivil,
                'id_pais' => $id_pais,
                'cpf' => $d['DadosPessoais.CPF'],
                'passaporte' => $d['DadosPessoais.passaporte'],
                'email_principal' => $d['DadosPessoais.emailinscricao'],
                'email_secundario' => $d['DadosPessoais.emailalternativo'],
                'id_local_nascimento' => $id_cidadeNasc,
                'pai' => $d['DadosPessoais.nomepai'],
                'mae' => $d['DadosPessoais.nomemae'],
                'deletado' => "0"
            );

            $params = $params + $Documento;

            $id_pessoa = $clsPessoas->atualizaPessoa($params);

            $data[$index]['id_pessoa'] = $id_pessoa;



            $id_cidade2 = $cidades->getId($d['EnderecoAtual.cidade'], $d['EnderecoAtual.estado']);

            $params2 = array(
                'id_pessoa' => $id_pessoa,
                'endereco' => $d["EnderecoAtual.tipologradouro"] . ' ' . $d["EnderecoAtual.logradouro"] . ', ' . $d['EnderecoAtual.Numero'] . ' - ' . $d["EnderecoAtual.bairro"],
                'telefone' => $d["EnderecoAtual.DDIfixo"] . ' ' . $d["EnderecoAtual.DDDfixo"] . '-' . $d["EnderecoAtual.telefonefixo"],
                'celular' => $d["EnderecoAtual.DDIcelular"] . ' ' . $d["EnderecoAtual.DDDcelular"] . '-' . $d["EnderecoAtual.telefonecelular"],
                'cep' => $d['EnderecoAtual.CEP'],
                'complemento' => $d['EnderecoAtual.complemento'],
                'id_cidade' => $id_cidade2
            );


            if ($d["EnderecoAtual.logradouro"] == "" && $d['EnderecoAtual.CEP'] == "") {
                $id_cidade2 = $cidades->getId($d['EnderecoFamiliar.cidade'], $d['EnderecoFamiliar.estado']);
                $params2 = array(
                    'id_pessoa' => $id_pessoa,
                    'endereco' => $d["EnderecoFamiliar.tipologradouro"] . ' ' . $d["EnderecoFamiliar.logradouro"] . ', ' . $d['EnderecoFamiliar.Numero'] . ' - ' . $d["EnderecoFamiliar.bairro"],
                    'telefone' => $d["EnderecoFamiliar.ddifixo"] . ' ' . $d["EnderecoFamiliar.dddfixo"] . '-' . $d["EnderecoFamiliar.telefonefix"],
                    'celular' => $d["EnderecoFamiliar.ddicelular"] . ' ' . $d["EnderecoFamiliar.dddcelular"] . '-' . $d["EnderecoFamiliar.telefonecelular"],
                    'cep' => $d['EnderecoFamiliar.CEP'],
                    'complemento' => $d['EnderecoFamiliar.complemento'],
                    'id_cidade' => $id_cidade2
                );
            }

            $endereco->atualizaResidencia($params2);


            $dataPro = array(
                'id_pessoa' => $id_pessoa);

            $profissionais = new Application_Models_Profissionais();
            $profissionais->addProfissional($dataPro);

            //id_pos_graduacao	n_inscricao	n_exame	aux
            if ($d['Inscricao.nomeniveldocursonolegado'] == 'Mestrado')
                $id_tipo_curso = 3;
            else
                $id_tipo_curso=5;


            $id_pos_graduacao = $clsPos->atualizaPosGraduacao($id_pessoa, $id_tipo_curso);

            $data[$index]['id_pos_graduacao'] = $id_pos_graduacao;
        }

        return $data;
    }

    public function importInscricoes($arrayCSV) {
        $clsInscricoes = new Application_Models_Inscricoes();
        $clsPeriodos = new Application_Models_Periodos();
        $clsPosGraduacao = new Application_Models_Pos_Graduacoes();
        $clsTipoCursos = new Application_Models_TipoCursos();
        $clsAreasDeConcentracao = new Application_Models_AreasDeConcentracao();
        $clsProfessor = new Application_Models_Professores();
        $clsCidadesProva = new Application_Models_CidadesDaProva();
        $mask = new mascara();

        $x = 0;
        foreach ($arrayCSV as $arr) {
            $id_pessoa = $arr['id_pessoa'];
            $id_pos = $arr['id_pos_graduacao'];
            $inscricao = $clsInscricoes->getInscricoesByCodigo($arr["Inscricao.numero"]);
            foreach ($arr as $index => $a) {
                $arr = explode('.', $index);
                if ($arr[0] == 'InformacoesAdicionais') {
                    //print_r($arr[1].'<br/>');
                    if ($arr[1] == "pretendesolicaproveitamentoestudodisc") {
                        if ($a == "Sim")
                            $aproveitamentoCurso = 1;
                        else
                            $aproveitamentoCurso=0;
                    }

                    if ($arr[1] == "nomeorientadorpretendido") {
                        if ($a != "")
                            $arrProfessor = $clsProfessor->CRUDreadbyProfessor($a);
                        $id_professor = ($arrProfessor[0]['id']);
                    }

                    if ($arr[1] == "numeroinscricaoexamenac") {
                        if ($a != "")
                            $arrCidadeProva = $clsCidadesProva->CRUDreadByCidade($a);
                        $id_cidade_prova = ($arrCidadeProva[0]['id']);
                    }
                }

                if ($arr[0] == 'Inscricao') {
                    //verifica se é mestrado ou doutorado
                    if ($arr[1] == "nomeniveldocursonolegado") {

                        $arrTipoCursos = $clsTipoCursos->CRUDreadByNome(trim($a));
                        $tipoCurso = $arrTipoCursos[0]['id_tipo_curso'];
                    }
                    
                    //verifica periodo
                    if ($arr[1] == "periodo" && $a != "") {
                        $periodo = explode('/', $a);
                        $periodo = $periodo[0];
                        $periodo = explode('.', $periodo);
                        $periodo = str_replace('o', "", $periodo[0]);
                        $id_periodo = $clsPeriodos->CRUDreadByNome(trim($periodo));
                        $id_periodo = $id_periodo[0]['id_periodo'];
                    }

                    //verifica area de concentracao
                    if ($arr[1] == "nomeareaconcentracaolegado" && $a != "") {
                        $arrAreaConcentracao = $clsAreasDeConcentracao->CRUDreadByNome(strtoupper(trim($a)));
                        $id_area_concentracao = ($arrAreaConcentracao[0]['id_area_de_concentracao']);
                    }

                    if ($arr[1] == "data") {
                        $data_inscricao = $mask->MascaraDataTodatetimeSQL($a);
                        //	print_r($a.'<br/>');
                    }

                    if ($arr[1] == 'numero') {
                        $numero = $a;
                    }
                }
            }



            $inscricaodata = array(
                'data_inscricao' => $data_inscricao,
                'id_pos_graduacao' => $id_pos,
                'id_periodo' => $id_periodo,
                'id_professor' => $id_professor,
                'id_area_de_concentracao' => $id_area_concentracao,
                'id_relacao_instituicao_ies' => 1,
                'id_cidade_prova' => $id_cidade_prova,
                'bolsa' => '',
                'inscricao_aceita' => 0,
                'id_instituicao' => 1,
                'id_curso' => 1,
                'data_conclusao' => '',
                'codigo_inscricao' => $numero,
                'pretende_aproveitamento' => $aproveitamentoCurso,
                'deletado' => 0,
            );



            if ($id_pos != "" && $numero != ""){
              if(count($inscricao) == 0){
                $clsInscricoes->addInscricoes($inscricaodata);
              }else{
                $clsInscricoes->updateInscricoes($inscricaodata,$inscricao[0]['id_inscricao']);
              }
            }
        }

        //exit;
    }

}
