<?php
class Application_Models_Cursos extends Zend_Db_Table_Abstract
{
    protected $_name = 'cursos';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_curso <> 1')->order('curso'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_curso='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE cursos SET deletado=1 WHERE id_curso={$id};";
        $dbAdapter2->query($sql2);
    	
    	return($this->update(array('deletado'=>'1'), 'id_curso='.$id));
    }
}
?>