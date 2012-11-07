<?php
class Application_Models_NivelBolsa extends Zend_Db_Table_Abstract
{
    protected $_name = 'nivelbolsa';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado = 0 AND id_nivelbolsa <> 1')->order('nivelbolsa'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_nivelbolsa='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_nivelbolsa='.$id));
    }
}
?>