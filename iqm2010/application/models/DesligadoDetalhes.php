<?php
class Application_Models_Desligado_Detalhes extends Zend_Db_Table_Abstract
{
    protected $_name = 'desligado_detalhes';

    public function getDesligadoDetalhes($id)
    {
    	   $result=$this->fetchAll($this->select()->where('id_desligado_detalhe='.$id));
    	   return $result->toArray();
    }
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_desligado_detalhe <> 1')->order('desligado_detalhe'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_desligado_detalhe='.$id);
    }
    function CRUDdelete($id)
    {

        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE desligado_detalhes SET deletado=1 WHERE id_desligado_detalhe={$id};";
        $dbAdapter2->query($sql2);
    	
    	return($this->update(array('deletado'=>'1'), 'id_desligado_detalhe='.$id));
    }
}
?>