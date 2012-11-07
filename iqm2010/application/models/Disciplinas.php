<?php
class Application_Models_Disciplinas extends Zend_Db_Table_Abstract
{
    protected $_name = 'disciplinas';
    
    function CRUDcreate($array)
    {
      return $this->insert($array);
     //$lastID= $this->getAdapter()->lastInsertId();
        
    }
    function CRUDread()
    {
    	//return($this->fetchAll($this->select()->where('deletado=0 AND id_disciplina <> 1')->order('titulo'))->toArray());
        return($this->fetchAll($this->select()->where('deletado=0')->order('titulo'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_disciplina='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_disciplina='.$id));
    }
    public function duplicar($id)
    {
    	$result = $this->fetchRow("id_disciplina = '$id'");
    	$result = (!empty($result))?$result->toArray():array();
    	
    	$result['titulo'] .= ' DUP';
    	unset($result['id_disciplina']);

    	// to criando um registro via activeRecord
    	$newRow = $this->createRow($result);
    	$newRow->save();
    }
    
    function anosPossiveis()
    {
       	
    	$this->_name='disciplinas';
    	$dbAdapter1 = $this->getAdapter();
        $sql="SELECT `ano` FROM `turmas` group by `ano` order by ano";
        $result = $dbAdapter1->query($sql);
       
        return $result->fetchAll();		
    }
    
    function turmaByAnoPeriodoDisciplina($ano, $periodo, $disciplina)
    {
    	$dbAdapter1 = $this->getAdapter();
    	
    	$sql="SELECT `id_turma` as id,`turma` 
    		  FROM `turmas` 
    		  WHERE `ano`=$ano 
    		  AND  `periodo`= $periodo
    		  AND `materia`= $disciplina";
    	
    	$result = $dbAdapter1->query($sql);
        
        return $result->fetchAll();	
    	
    }
    
    function disciplinasByAnoPeriodo($ano, $periodo)
    {
       	
    	$this->_name='disciplinas';
    	$dbAdapter1 = $this->getAdapter();
    	
    	$sql="SELECT `id_disciplina`, `codigo` 
    		  FROM `turmas` t , disciplinas d 
    		  WHERE `periodo`=$periodo  AND `ano`=$ano
    		  AND d.`deletado`=0";
    	//	  GROUP BY `codigo`, `id_disciplina`";

        
        $result = $dbAdapter1->query($sql);
        
        return $result->fetchAll();	
    }
    
    
    public function getDisciplina($id_disciplina)
    {
        $row = $this->fetchRow(
            $this->select()
            ->where('id_disciplina = '.$id_disciplina)
        );
      
        if(is_array($row))
      		return $row->toArray();
       	else
       		return '';
    
      
    }
}
?>