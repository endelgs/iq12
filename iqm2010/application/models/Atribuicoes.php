<?php
class Application_Models_Atribuicoes extends Zend_Db_Table_Abstract
{
    protected $_name = 'atribuicoes';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado = 0 AND id_atribuicao <> 1')->order('atribuicao'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_atribuicao='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_atribuicao='.$id));
    }
}
?>