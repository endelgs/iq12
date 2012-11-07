<?php
class Application_Models_TipoAtividades extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipo_atividades';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_tipo_atividade <> 1')->order('tipo_atividade'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_tipo_atividade='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE tipo_atividades SET deletado=1 WHERE id_tipo_atividade ={$id};";
        $dbAdapter2->query($sql2);
    	
    	return($this->update(array('deletado'=>'1'), 'id_tipo_atividade='.$id));
    }
}
?>