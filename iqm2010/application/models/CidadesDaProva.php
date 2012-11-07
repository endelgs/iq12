<?php
class Application_Models_CidadesDaProva extends Zend_Db_Table_Abstract
{
    protected $_name = 'cidade_provas';
    
    function CRUDcreate($array)
    {
    	$this->_name = 'cidade_provas';
    	return($this->insert($array));
    }
    function CRUDread()
    {
    	$this->_name='v_cidade_provas';
    	$this->_primary = 'id';
        $rows = $this->fetchAll($this->select()->where('id <> 1')->order('cidade')); 
        return($rows->toArray());
    }
    
    function CRUDreadByCidade($cidade)
    {
    	$this->_name='v_cidade_provas';
    	$this->_primary = 'id';
        $rows = $this->fetchAll($this->select()->where("cidade LIKE '$cidade'")->order('cidade')); 
        return($rows->toArray());
    }
    
    function CRUDdelete($id)
    {
//        $dbAdapter1 = $this->getAdapter();
//        $sql1 = "UPDATE inscricoes SET id_cidade_prova=1 WHERE id_cidade_prova={$id};";
//        $dbAdapter1->query($sql1);
//    	    	
//        $dbAdapter2 = $this->getAdapter();
//        $sql2 = "UPDATE exames SET id_cidade_prova=1 WHERE id_cidade_prova={$id};";
//        $dbAdapter1->query($sql2);

         $dbAdapter1 = $this->getAdapter();
         $sql1 = "UPDATE cidade_provas SET deletado=1 WHERE id_cidade_prova={$id};";
         $dbAdapter1->query($sql1);
    	
         //return($this->delete('id_cidade_prova='.$id));
    }
}
?>