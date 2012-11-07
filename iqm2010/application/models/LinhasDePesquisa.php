<?php
class Application_Models_LinhaDePesquisa extends Zend_Db_Table_Abstract
{
    protected $_name = 'linhas_de_pesquisa';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_linha_de_pesquisa <> 1')->order('linha_de_pesquisa'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_linha_de_pesquisa='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "UPDATE ingressos SET id_linha_de_pesquisa=1 WHERE id_linha_de_pesquisa={$id};";
        $dbAdapter1->query($sql1);
    	
    	return($this->update(array('deletado'=>'1'), 'id_linha_de_pesquisa='.$id));
    }
    
}
?>