<?php

class Application_Models_Relatorios extends Zend_Db_Table_Abstract {

    protected $_name = 'relatorios';
    protected $_primary = 'id_relatorio';

    public function addRelatorio($data) {
        $this->insert($data);
    }

    public function getByIngresso($idIngresso) {
        $this->_name = 'v_relatorios';
        $rows = $this->fetchAll($this->select()->where('id_ingresso=' . $idIngresso));
        return $rows->toArray();
    }

    public function getByRelSemView($id) {
        $this->_name = 'relatorios';
        $row = $this->fetchAll($this->select()->where('id_ingresso=' . $id));

        return $row->toArray();
    }

    public function replicaRel($id_ingres_velho, $id_ingres_novo) {
        $arrOrientadores = $this->getByRelSemView($id_ingres_velho);

        foreach ($arrOrientadores as $index => $a) {

            unset($arrOrientadores[$index]["id_relatorio"]);
            $arrOrientadores[$index]["id_ingresso"] = $id_ingres_novo;
            // var_dump($arrOrientadores[$index]);
            $this->addRelatorio($arrOrientadores[$index]);
        }
    }

    public function deleteRelatorio($id) {
        $result = $this->delete('id_relatorio=' . (int) $id);
    }

    function updateRelatorio($array, $id) {
        $this->update($array, 'id_relatorio=' . $id);
    }

}