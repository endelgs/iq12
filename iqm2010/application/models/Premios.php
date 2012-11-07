<?php
class Application_Models_Premios extends Zend_Db_Table_Abstract
{
    protected $_name = 'premios';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread($id, $tipo)
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_pos_graduacao="'.$id.'" AND tipo="'.$tipo.'"')->order('premio'))->toArray());
		
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_premio='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_premio='.$id));
    }
}
?>