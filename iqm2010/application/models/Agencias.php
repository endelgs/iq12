<?php
class Application_Models_Agencias extends Zend_Db_Table_Abstract
{
    protected $_name = 'agencias';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_agencia <> 1')->order('agencia'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_agencia='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_agencia='.$id));
    }
}
?>