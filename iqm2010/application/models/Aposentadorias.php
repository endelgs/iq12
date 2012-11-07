<?php
class Application_Models_Aposentadorias extends Zend_Db_Table_Abstract
{
    protected $_name = 'aposentadorias';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_aposentadoria <> 1')->order('aposentadoria'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_aposentadoria='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE aposentadorias SET deletado=1 WHERE id_aposentadoria={$id};";
        $dbAdapter2->query($sql2);
    	
    	return($this->update(array('deletado'=>'1'), 'id_aposentadoria='.$id));
    }
}
?>