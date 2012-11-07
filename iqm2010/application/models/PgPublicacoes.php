<?php
class Application_Models_PgPublicacoes extends Zend_Db_Table_Abstract
{
    protected $_name = 'pg_publicacoes';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }

    function CRUDread($id, $tipoPessoa)
    {
    	$this->_name="v_pg_puglicacoes";
    	$this->_primary="id_pg_publicacoes";
    	return($this->fetchAll($this->select()
        	->where('id_pos_ou_docente = '.$id.' AND tipo='.$tipoPessoa)
        	->order('data DESC')
        )->toArray());
    }
    
    function CRUDupdate($array, $id)
    {
    	return($this->update($array, 'id_pg_publicacoes='.$id));
    }
    function CRUDdelete($id)
    {
    	return($this->delete('id_pg_publicacoes='.$id));
    }
    
}
?>