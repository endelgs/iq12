<?php
class Application_Models_Reingresso extends Zend_Db_Table_Abstract
{
    protected $_name = 'reingresso';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
    	return($this->fetchAll($this->select()->where("id_reingresso = $id"))->toArray());
    }
    function CRUDupdate($array, $id)
    {
    	$this->update($array, 'id_reingresso = '.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->delete('id_reingresso = '.(int)$id));
    }
	function CRUDreadByPos($id)
    {
    	$this->_name = 'v_reingresso';
    	$this->_primary = 'id';
    	$row = $this->fetchAll($this->select()->where('id_pos_graduacao ='.$id));
    	if($row=='')return $row;
    	else return $row->toArray();
    }
}
?>