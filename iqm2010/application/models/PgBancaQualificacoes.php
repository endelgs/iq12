<?php
class Application_Models_PgBancaQualificacoes extends Zend_Db_Table_Abstract
{
    protected $_name = 'pg_bancas_qualificacoes';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
    	$this->_name = 'v_pg_bancas_qualificacoes';
    	$this->_primary = 'id';
    	return($this->fetchAll($this->select()
        	->order('nome')
        )->toArray());
    }
    function CRUDreadOne($array)
    {
    	return ($this->fetchRow($this->select()
    		->where('id_qualificacao = '.$array['id_qualificacao'].' 
    		AND id_professor ='.$array['id_professor'])
    	));
    }
    function CRUDupdate($array, $id)
    {
    	return($this->update($array, 'id_banca='.$id));
    }
    function CRUDQLdelete($id)
    {
    	return($this->delete('id_qualificacao='.$id));
    }
 	function CRUDdelete($id)
    {
    	return($this->delete('id_banca='.$id));
    }
    
}
?>