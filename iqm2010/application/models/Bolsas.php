<?php
class Application_Models_Bolsas extends Zend_Db_Table_Abstract
{
    protected $_name = 'bolsas';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        $this->_name = 'v_bolsas';
    	return($this->fetchAll($this->select()->where("id_bolsa = $id"))->toArray());
    }
    public function getByField($campo,$id)
    {
    	$this->_name= 'v_bolsas';
    	$this->_primary = 'id';
    	return($this->fetchAll($this->select()->where("$campo = $id"))->toArray());
    }
    
    function CRUDreadSelect($id)
    {	$this->_name = 'bolsas';
    	return($this->fetchAll($this->select()->where("id_bolsa = $id"))->toArray());
    }
    
    function CRUDupdate($array, $id)
    {
    	$this->update($array, 'id_bolsa='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_bolsa='.$id));
    }
}
?>