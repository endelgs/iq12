<?php
class Application_Models_Instituicoes extends Zend_Db_Table_Abstract
{
    protected $_name = 'instituicoes';
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        /*$this->_name = 'v_instituicoes';
        $this->_primary = 'id';*/
    	return($this->fetchAll($this->select()->where('deletado=0 AND id_instituicao <> 1')->order('instituicao'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_instituicao='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "UPDATE instituicoes SET deletado=1 WHERE id_instituicao={$id};";
        $dbAdapter1->query($sql1);

        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE professores SET id_instituicao=1 WHERE id_instituicao={$id};";
        $dbAdapter2->query($sql2);
        
        return($this->update(array('deletado'=>'1'), 'id_instituicao='.$id));
    }
    
    
}

?>