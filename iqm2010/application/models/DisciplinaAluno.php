<?php
class Application_Models_DisciplinaAluno extends Zend_Db_Table_Abstract
{
    protected $_name = 'pg_disciplinas';
    
    function CRUDcreate($array){
      return $this->insert($array);        
    }
    
    function CRUDread($id, $tipo=0){	
        return($this->fetchAll($this->select()->where('id_pos_graduacao='.$id))->toArray());
    }
    
    function gridRead($id){
      
    	$dbAdapter1 = $this->getAdapter();
        $sql="SELECT *  FROM `v_disciplina_aluno` WHERE id_pos_graduacao='$id'";
        $result = $dbAdapter1->query($sql);
        return $result->fetchAll();		
    }
    
    function CRUDupdate($array, $id){
    	$this->update($array,'id_pg_disciplina='.$id);
    }
    
    
    function CRUDdelete($id){    	
    	return ($this->delete('id_pg_disciplina='.$id));
    }
    
}
?>