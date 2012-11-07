<?php  
class Application_Models_Graduacoes extends Zend_Db_Table_Abstract
{
    protected $_primary = 'id_pos_graduacao';
	protected $_name    = 'pos_graduacoes';

	function getPessoaByPos($id_pos_graduacao)
    {
    	$this->_name = 'pos_graduacoes';
        $rows = $this->fetchAll(
        	$this->select()
        	->where("id_pos_graduacao =$id_pos_graduacao")
        );
        return($rows->toArray());
    }
    
  
}   
?>