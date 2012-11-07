<?php
class Application_Models_Departamentos extends Zend_Db_Table_Abstract
{
    protected $_name = 'departamento';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado = 0 AND id_departamento <> 1')->order('departamento'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_departamento='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_departamento='.$id));
    }
}
?>