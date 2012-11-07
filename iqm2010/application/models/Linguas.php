<?php
class Application_Models_Linguas extends Zend_Db_Table_Abstract
{
    protected $_name = 'linguas';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_lingua <> 1')->order('lingua'))->toArray());
    }
    
    function getIdioma($id)
    {
        return($this->fetchrow($this->select()->where('id_lingua='.$id))->toArray());
    }
    
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_lingua='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_lingua='.$id));
    }
}
?>