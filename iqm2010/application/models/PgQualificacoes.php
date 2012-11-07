<?php
class Application_Models_PgQualificacoes extends Zend_Db_Table_Abstract
{
    protected $_name = 'pg_qualificacoes';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread($id, $tipo)
    {
    	return($this->fetchAll($this->select()
        	->where('id_pos_graduacao = '.$id.' AND tipo = 0 AND tipo_qualificacao='.$tipo)
        	->order('data DESC')
        )->toArray());
    }
	function CRUDPDread($id)
    {
    	return($this->fetchRow($this->select()
        	->where('id_pos_graduacao = '.$id.' AND tipo = 1')
        	->order('data DESC')
        ));
    }
	function CRUDIDread($id)
    {
    	return($this->fetchRow($this->select()
        	->where('id_pos_graduacao = '.$id.' AND tipo = 2')
        	->order('data DESC')
        ));
    }
    
    function CRUDupdate($array, $id)
    {
    	return($this->update($array, 'id_qualificacoes='.$id));
    }
    function CRUDdelete($id)
    {
    	return($this->delete('id_qualificacoes='.$id));
    }
    
}
?>