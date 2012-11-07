<?php
class Application_Models_Publicacoes extends Zend_Db_Table_Abstract
{
    protected $_name = 'publicacoes';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_publicacao <> 1')->order('publicacao'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_publicacao='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE publicacoes SET deletado=1 WHERE id_publicacao={$id};";
        $dbAdapter2->query($sql2);
    	
    	return($this->update(array('deletado'=>'1'), 'id_publicacao='.$id));
    }
}
?>