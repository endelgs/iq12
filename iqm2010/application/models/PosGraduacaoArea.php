<?php

class Application_Models_PosGraduacaoArea extends Zend_Db_Table_Abstract {

    protected $_name = 'pos_area_de_concentracao';

    function CRUDcreate($array) {
        return($this->insert($array));
    }

    public function getByAreaSemView($id) {
        $this->_name = 'pos_area_de_concentracao';
        $row = $this->fetchAll($this->select()->where('id_pos=' . $id));

        return $row->toArray();
    }

    public function replicaArea($id_pos_antigo, $id_pos_novo) {
        $arrArea = $this->getByAreaSemView($id_pos_antigo);
        //unset($arrOrientadores[""]);
       // var_dump($arrArea);
        // if (!$arrOrientadores)
        foreach ($arrArea as $index => $a) {

            unset($arrArea[$index]["id_pos_area_de_concentracao"]);
            $arrArea[$index]["id_pos"] = $id_pos_novo;
            // var_dump($arrOrientadores[$index]);
            $this->CRUDcreate($arrArea[$index]);
        }
    }

    function CRUDread($id) {
        $this->_name = 'v_pos_area';
        $this->_primary = 'id';
        return($this->fetchAll($this->select()
                        ->where('id_pos = ' . $id)
                        ->order('data DESC')
        )->toArray());
    }

    function CRUDupdate($array, $id) {
        return($this->update($array, 'id_pos_area_de_concentracao=' . $id));
    }

    function CRUDdelete($id) {
        return($this->delete('id_pos_area_de_concentracao=' . $id));
    }

}

?>