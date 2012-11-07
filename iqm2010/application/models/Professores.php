<?php
class Application_Models_Professores extends Zend_Db_Table_Abstract
{
    protected $_name = 'v_professores';
    
    function CRUDreadbyProfessor($professores)
    {
        $this->_primary="id";
    	return($this->fetchAll($this->select()->where(" nome LIKE '$professores' ")->order('nome'))->toArray());
    }
}
?>