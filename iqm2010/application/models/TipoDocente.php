<?php
class Application_Models_TipoDocente extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipo_docente';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado = 0 AND id_tipo_docente <> 1')->order('tipo_docente'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_tipo_docente='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_tipo_docente='.$id));
    }
}
?>