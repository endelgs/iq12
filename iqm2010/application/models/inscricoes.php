<?php

class Application_Models_Inscricoes extends Zend_Db_Table_Abstract {

    protected $_name = 'inscricoes';

    public function getInscricoes($id = null) {
        $this->_name = 'inscricoes';
        $id = (int) $id;
        $row = $this->fetchAll();
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function getInscricoesByAno($ano, $where, $id_tipo_curso=3) {
        $sql = "SELECT DISTINCT ir.ra, p.nome, date_format(v.data_inscricao,'%d/%m/%Y') as data_inscricao_mask,
date_format(ir.data_ingresso,'%d/%m/%Y') as data_ingresso_mask
FROM `v_inscricoes` v
LEFT JOIN ingressos ir ON v.`id_pos_graduacao` = ir.`id_pos_graduacao`
LEFT JOIN pessoas p ON p.`id_pessoa` = v.`id_pessoa`
LEFT JOIN exames e on e.id_pos_graduacao = v.id_pos_graduacao
INNER JOIN pos_graduacoes pg ON v.id_pos_graduacao=pg.id_pos_graduacao
WHERE  YEAR(v.data_inscricao) = '$ano' AND pg.id_tipo_curso=$id_tipo_curso  " . $where . " ORDER BY p.nome ASC";

        //echo $sql;
        $dbAdapter1 = $this->getAdapter();
        $result = $dbAdapter1->query($sql);
        return $result->fetchAll();
    }

    public function getInscricoesById($id) {
        $row = $this->fetchAll(
                                $this->select()
                                ->where('id_inscricao = ' . $id)
        );
        return $row->toArray();
    }





















    public function duplicaInscricaoParaDoutorado($id_inscricao, $id_pessoa) {
        $this->_name = 'pos_graduacoes';
        $row = $this->fetchAll(
                                $this->select()
                                ->where('id_pessoa = ' . $id_pessoa . ' AND id_tipo_curso=5')
                                ->order('id_pos_graduacao DESC')
        );
        //print_r($row->toArray());die();
        $array1 = $row->toArray();
        // Se nao houver um registro de doutorado associado, crio com os dados
        // da pessoa
        if (count($array1) == 0) {
            $data = array('id_tipo_curso' => '5',
                'id_pessoa' => $id_pessoa,
                'n_inscricao' => '0',
                'n_exame' => '0'
            );

            $id_pos_graduacao = $this->insert($data);
        // Caso ja exista, so associo o id_pos_graduacao
        }else
            $id_pos_graduacao=$array1[0]['id_pos_graduacao'];

        // DUPLICANDO A INSCRICAO 
        // duplico a inscricao com os dados ja existentes
        $this->_name = 'inscricoes';
        $arrF = $this->getInscricoesById($id_inscricao);
        $arrF = $arrF[0];

        $arrF['id_pos_graduacao'] = $id_pos_graduacao;

        unset($arrF['id_inscricao']);

        $this->insert($arrF);

        //print_r($arrF); die();
        return $id_pos_graduacao;
    }































    public function getInscricoesByPos($id_pos_graduacao, $where = "") {
        if ($where != "") {
            $where = " AND " . $where;
        }
        $this->_name = 'v_inscricoes';
        $this->_primary = 'id_inscricao';
        $row = $this->fetchAll(
                                $this->select()
                                ->where('id_pos_graduacao = ' . $id_pos_graduacao . $where)
        );
        return $row->toArray();
    }
    public function getInscricoesByCodigo($codigo, $where = "") {
        if ($where != "") {
            $where = " AND " . $where;
        }
        $where .= " AND deletado = 0 ";
        $this->_name = 'inscricoes';
        $this->_primary = 'id_inscricao';
        $row = $this->fetchAll(
                                $this->select()
                                ->where("codigo_inscricao = '" . $codigo ."'". $where)
                                ->limit("1")
        );
        return $row->toArray();
    }

    public function getInscricoesByPosDeletado() {
        $this->_name = 'inscricoes';
        $row = $this->fetchAll(
                                $this->select()
                                ->where('deletado = 1')
        );
        return $row->toArray();
    }

    public function addInscricoes($data) {
        $this->_name = 'inscricoes';
        return $this->insert($data);
    }

    public function updateInscricoes($data, $id_inscricao) {
        $this->_name = 'inscricoes';
        //echo $id_inscricao; die();
        return $this->update($data, 'id_inscricao=' . (int) $id_inscricao);
    }

    public function deleteInscricoes($id_inscricao) {
        $this->_name = 'inscricoes';
        $this->delete('id_inscricao =' . (int) $id_inscricao);
    }

    public function updateRemoveInscricoes($data, $id_pos_graduacao) {
        $this->_name = 'inscricoes';
        $this->update($data, 'id_pos_graduacao=' . $id_pos_graduacao);
    }
    
}

?>