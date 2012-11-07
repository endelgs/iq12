<?php

class Application_Models_GerenciarGenerico extends Zend_Db_Table_Abstract {

    protected $_name = '';
    protected $_primary = '';

    public function _name($name) {
        $this->_name = $name;
    }

    public function _primary($primary) {
        $this->_primary = $primary;
    }

    function CRUDcreate($array) {
        return($this->insert($array));
    }

    function CRUDreadByPrimary($id) {

        $primary = $this->_primary;

        if (is_array($primary))
            $primary = $primary[1];

        $sql = $this->select()->where($primary . ' = ' . $id);
//      echo $sql;
        $row = $this->fetchAll($sql);

        return($row->toArray());
    }

    function CRUDread() {

        return($this->fetchAll($this->select())->toArray());
    }

    function CRUDupdate($array, $id) {

        $primary = $this->_primary;
        if (is_array($primary))
            $primary = $primary[1];

//        var_dump($array);
//        echo $primary . "= '" . $id . "'";
//        exit;
        $this->update($array, $primary . "= '" . $id . "'");
    }

    function CRUDdelete($id) {

        $primary = $this->_primary;
        if (is_array($primary))
            $primary = $primary[1];
        return($this->update(array('deletado' => '1'), $primary . '=' . $id));
    }

}

?>