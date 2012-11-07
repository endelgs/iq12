<?php
class Application_Models_Frequencia extends Zend_Db_Table_Abstract
{
    protected $_name = 'frequencia';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread($id_pos_graduacao)
    {
        return($this->fetchAll($this->select()->where(' nao_ok=1 AND id_pos_graduacao='.$id_pos_graduacao)->order('mes'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_frequencia ='.$id);
    }
}
?>