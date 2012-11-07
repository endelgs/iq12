<?php
class Application_Models_AreasDeConcentracao extends Zend_Db_Table_Abstract
{
    protected $_name = 'areas_de_concentracao';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_area_de_concentracao <> 1')->order('area_de_concentracao'))->toArray());
    }
	function CRUDreadByNome($areaConcentracao)
    {
    	//$areaConcentracao=utf8_encode($areaConcentracao);
    	$sql=$this->select()->where("deletado=0 AND area_de_concentracao LIKE '%$areaConcentracao%'");
        return($this->fetchAll($sql)->toArray());
    }
    function CRUDupdate($array, $id)
    {
    	$this->update($array, 'id_area_de_concentracao='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "UPDATE inscricoes SET id_area_de_concentracao=1 WHERE id_area_de_concentracao={$id};";
        $dbAdapter1->query($sql1);

        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE orientadores SET id_area_de_concentracao=1 WHERE id_area_de_concentracao={$id};";
        $dbAdapter2->query($sql2);
    	        
    	return($this->update(array('deletado'=>'1'), 'id_area_de_concentracao='.$id));
    }
    
}
?>