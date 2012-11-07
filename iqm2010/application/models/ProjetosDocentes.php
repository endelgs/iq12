<?php

class Application_Models_ProjetosDocente extends Zend_Db_Table_Abstract {

    protected $_name = 'projetos_docente';

    function CRUDcreate($array) {
        $array = $this->utf8_decode($array);
        return($this->insert($array));
    }

    function utf8_decode($arr) {
        foreach ($arr as $index => $a) {
            //$arr[$index] = utf8_decode($a);
        }

        return $arr;
    }

    function CRUDread($id_pessoa_docente) {
        return($this->fetchAll($this->select()->where("id_pessoa_docente = $id_pessoa_docente AND deletado='0'"))->toArray());
    }

    function allByPos($id_pos, $id_projeto_atual="") {

        if ($id_projeto_atual != "") {
            $this->_name = "projetos_docente";
            $this->_primary = 'id_projeto';
            $arr = $this->fetchAll($this->select()->where("id_projeto = $id_projeto_atual AND deletado='1'"))->toArray();
        }

        $this->_name = "v_projeto_orientador_pos";
        $this->_primary = 'id_projeto';
        $sql = $this->select()->where("id_pos_graduacao = '$id_pos' AND deletado='0'  AND atual='1'")->order('titulo');
        $arrFinal = $this->fetchAll($sql)->toArray();


        if (count($arr) > 0) {

            $arrFinal[] = $arr[0];
        }

        return $arrFinal;
    }

    function CRUDupdate($array, $id) {
        //$array = $this->utf8_decode($array);
        $this->update($array, 'id_projeto=' . $id);
    }

    function CRUDdelete($id) {
        return($this->update(array('deletado' => '1'), 'id_projeto=' . $id));
    }

}

?>