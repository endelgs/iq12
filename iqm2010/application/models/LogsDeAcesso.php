<?php
class Application_Models_LogsDeAcesso extends Zend_Db_Table_Abstract
{
    protected $_name = 'logs_acesso';
    
    function CRUDcreate($array)
    {	
    	return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->order('data desc'))->toArray());
    }
    
}
?>