<?php
class Application_Models_MotivosDeCancelamento extends Zend_Db_Table_Abstract
{
    protected $_name = 'motivos_cancelamento';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado = 0')->order('motivo_cancelamento'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
    	$this->update($array, 'id_motivo_cancelamento='.$id);
    	$this->view->alert = print_r($array,true);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_motivo_cancelamento='.$id));
    }
    
}
?>