<?php
class Application_Models_BolsaProdutividade extends Zend_Db_Table_Abstract
{
    protected $_name = 'bolsa_produtividade';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
    	return($this->fetchAll($this->select()->where("id_bolsa_produtividade = $id"))->toArray());
    }
    function CRUDupdate($array, $id)
    {
    	$this->update($array, 'id_bolsa_produtividade='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->delete('id_bolsa_produtividade = '.(int)$id));
    }
	function CRUDreadByDocente($id)
    {
    	$this->_name = 'v_bolsaprodutividade';
    	$this->_primary = 'id';
    	return($this->fetchAll($this->select()->where("id_docente = $id"))->toArray());
    }
}
?>