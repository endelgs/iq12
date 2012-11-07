<?php
class Application_Models_TiposProjetos extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipos_projetos';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_tipo_projeto <> 1')->order('tipo_projeto'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_tipo_projeto='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_tipo_projeto='.$id));
    }
}
?>