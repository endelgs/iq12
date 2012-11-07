<?php

class Application_Models_Profissionais extends Zend_Db_Table_Abstract {

    protected $_name = 'profissionais';
    protected $_primary = 'id_pessoa';

    public function addProfissional($array) {
        return($this->insert($array));
    }

    function existe($id) {

        $array = $this->fetchAll($this->select()->where("id_pessoa=" . $id))->toArray();

        if ($array)
            return true;

        return false;
    }

    public function updateProfissional($array, $id) {
        
        if ($this->existe($id))
            return($this->update($array, 'id_pessoa=' . $id));
        else {

            $array['id_pessoa'] = $id;
            $this->addProfissional($array);
        }
    }

}