<?php
class Application_Models_Turmas extends Zend_Db_Table_Abstract
{
    protected $_name = 'turmas';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_turma <> 1')->order('turma'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_turma='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter2 = $this->getAdapter();
        $sql2 = "UPDATE turmas SET deletado=1 WHERE id_turma={$id};";
        $dbAdapter2->query($sql2);
    	
//    	return($this->update(array('turma'=>'1')));
    }
    
    function ultimoDocente($idMateria)
    {
    	$dbAdapter= $this->getAdapter();
    	$sql="SELECT coordenador FROM turmas  where materia = '$idMateria'  order By id_turma DESC LIMIT 1";
    	$result=$dbAdapter->query($sql);
    	$result=$result->fetchAll();
    	
    	return $result[0]['coordenador'];
    }
    
    
    function filtroTurma($ano, $materia, $periodo)
    {
    	$this->_name="v_turmas";
    	$this->_primary="id_turma";
    	
    	$sql=" 1=1 ";
    	
    	if($ano!="")
    		$sql.="AND ano='$ano'";
    	if($materia!="1")
    		$sql.="AND materia='$materia' ";
    	if($periodo!="1")
    		$sql.="AND periodo = '$periodo' ";
    		
    	$sql.=" AND deletado=0 AND id_turma <> 1";
    
    	return($this->fetchAll($this->select()->where("$sql"))->toArray());
    }
    
}
?>