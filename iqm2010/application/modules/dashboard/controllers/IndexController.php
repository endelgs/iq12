<?php

require_once(APPLICATION_PATH . '/../library/relatorios/GridPdf.php');
require_once(APPLICATION_PATH . '/../library/componentes/Mascara.php');
require_once(APPLICATION_PATH . '/models/ListaRelatorioPDF.php');
require_once(APPLICATION_PATH . '/models/Orientadores.php');
require_once(APPLICATION_PATH . '/models/Alunos.php');
require_once(APPLICATION_PATH . '/models/General.php');

class Dashboard_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {

        $alunos = new Application_Models_Alunos();
        $GridPDF = new GridPdf();


        $tabela = new Application_Models_ListaRelatiorPDF();
        $tabela->table('v_alunos_em_curso');
        $tabela->primary('ra');
        $lista = $tabela->CRUDread();

        foreach ($lista as $index => $arr) {
            $link = $this->view->url(array('module' => 'pessoas', 'controller' => 'geral', 'action' => 'index', 'id_pessoa' => $arr['id_pessoa']));

            $dadoLink = '<a href="' . $link . '">Ver</a>';
            $lista[$index]['link'] = $dadoLink;
            $lista[$index]['nome'] = $arr['nome'];
            $dadoLinkRA = '<a href="' . $link . '">' . $arr['ra'] . '</a>';
            $lista[$index]['linkra'] = $arr['ra'];
        }

            $headers = array(
            'ra' => 'RA',
            'nome' => 'Nome Aluno',
            'tipo_curso' => 'Curso',
            'link' => ' ',
        );
        //var_dump($lista);
        $this->view->flex1 = $GridPDF->flexGridJS($lista, $headers, 750, 210);

        //-----------------------------------------------------//

        $tabela = new Application_Models_ListaRelatiorPDF();
        $tabela->table('v_relatorio_pendente');
        $tabela->primary('ra');
        $lista = $tabela->CRUDread();

        foreach ($lista as $index => $arr) {
            $link = $this->view->url(array('module' => 'pessoas', 'controller' => 'geral', 'action' => 'index', 'id_pessoa' => $arr['id_pessoa']));

            $dadoLink = '<a href="' . $link . '">Ver</a>';
            $lista[$index]['link'] = $dadoLink;
            $lista[$index]['nome'] = $arr['nome'];
            $dadoLinkRA = '<a href="' . $link . '">' . $arr['ra'] . '</a>';
            $lista[$index]['linkra'] = $arr['ra'];
        }

            $headers = array(
            'ra' => 'RA',
            'nome' => 'Nome Aluno',
            'tipo_curso' => 'Curso',
            'link' => ' ',
        );
        //var_dump($lista);
        $this->view->flex2 = $GridPDF->flexGridJS($lista, $headers, 750, 210);

        //-----------------------------------------------------//
        $tabela = new Application_Models_ListaRelatiorPDF();
        $tabela->table('v_alunos_conclusao_proxima');
        $tabela->primary('ra');
        $lista = $tabela->CRUDread();

        foreach ($lista as $index => $arr) {
            $link = $this->view->url(array('module' => 'pessoas', 'controller' => 'geral', 'action' => 'index', 'id_pessoa' => $arr['id_pessoa']));

            $dadoLink = '<a href="' . $link . '">Ver</a>';
            $lista[$index]['link'] = $dadoLink;
            $lista[$index]['nome'] = $arr['nome'];
            $dadoLinkRA = '<a href="' . $link . '">' . $arr['ra'] . '</a>';
            $lista[$index]['linkra'] = $arr['ra'];
        }

            $headers = array(
            'ra' => 'RA',
            'nome' => 'Nome Aluno',
            'tipo_curso' => 'Curso',
            'link' => ' ',
        );

        //var_dump($lista);
        $this->view->flex3 = $GridPDF->flexGridJS($lista, $headers, 750, 210);
    }

}

