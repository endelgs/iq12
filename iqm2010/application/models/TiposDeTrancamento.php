<?php
class Application_Models_TiposDeTrancamento extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipo_trancamentos';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        $dbAdapter1 = $this->getAdapter();
    	$sql1 = "SELECT id_tipo_trancamento, tipo_trancamento, soma_integralizacao, deletado, if(soma_integralizacao=1, 'Modifica', 'N&atilde;o modifica') AS modifica FROM tipo_trancamentos WHERE deletado=0 AND id_tipo_trancamento <> 1 ORDER BY tipo_trancamento;";
        $result = $dbAdapter1->query($sql1);
        return $result->fetchAll();
        //echo $this->select(array('id_tipo_trancamento'))->where('deletado=0 AND id_tipo_trancamento <> 1')->order('tipo_trancamento');
    	/*$a = $this->fetchAll()->toArray();
    	print_r($a);*/
    	//return();
    }
//SELECT  FROM `tipo_trancamentos`;    
    function CRUDupdate($array, $id)
    {
    	$this->update($array, 'id_tipo_trancamento='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "UPDATE trancamentos SET id_tipo_trancamento=1 WHERE id_tipo_trancamento={$id};";
        $dbAdapter1->query($sql1);
        
    	return($this->update(array('deletado'=>'1'), 'id_tipo_trancamento='.$id));
    }
}
?>