<?php
class Application_Models_DocenteTurmas extends Zend_Db_Table_Abstract
{
    protected $_name = 'docente_turmas';
    
    function CRUDcreate($array)
    {
    	$this->_name = 'docente_turmas';
    	return($this->insert($array));
    }
    function CRUDread()
    {
    	$this->_name='v_turmas_docentes';
    	$this->_primary = 'id_turma';
        $rows = $this->fetchAll($this->select()->order('nome')); 
        return($rows->toArray());
    }
    
    function CRUDdelete($id)
    {
         $dbAdapter1 = $this->getAdapter();
         $sql1 = "DELETE FROM docente_turmas WHERE id_turma={$id};";
         $dbAdapter1->query($sql1);
    }
    
    function CRUDdeleteProfessor($id_professor, $id_disciplina)
	{	
		if ((isset($id_professor))&& (isset($id_disciplina)))
		{
	     $dbAdapter1 = $this->getAdapter();
         $sql1 = "DELETE  FROM docente_turmas WHERE id_docente='{$id_professor}' AND id_turma='{$id_disciplina}';";
         $dbAdapter1->query($sql1);
		}
    }
}
?>