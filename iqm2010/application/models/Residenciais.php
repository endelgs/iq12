<?php

class Application_Models_Residenciais extends Zend_Db_Table_Abstract {

    protected $_name = 'residenciais';
    protected $_primary = 'id_pessoa';

    public function addResidencial($array) {
        return($this->insert($array));
    }
    
    function existe($id) {

        $array = $this->fetchAll($this->select()->where("id_pessoa=" . $id))->toArray();

        if ($array)
            return true;

        return false;
    }
    
    public function updateResidencial($array, $id) {
        
        if ($this->existe($id))
            return($this->update($array, 'id_pessoa=' . $id));
        else {

            $array['id_pessoa'] = $id;
            $this->addResidencial($array);
        }

    }

    public function atualizaResidencia($array) {
        $exist = $this->residenciaExits($array['id_pessoa']);
        if ($exist == 0)
            $this->insert($array);
        else
            $this->updateResidencial($array, $array['id_pessoa']);
    }

    public function residenciaExits($idpessoa) {
        $row = $this->fetchAll(
                        $this->select()
                                ->where('id_pessoa=' . $idpessoa)
        );

        try {
            $a = $row->toArray();
            return count($a);
        } catch (Exception $e) {
            return 0;
        }
    }

}