<?php
class Application_Models_Docente extends Zend_Db_Table_Abstract
{
    protected $_name = 'professores';
	function addDocente($array)
	{
		
		if(!$this->existeProfessor($array['id_pessoa']))
    		return($this->insert($array));
    	else
        	$this->update(array('deletado'=>'0'), 'id_pessoa='.$array['id_pessoa']);

//    	var_dump($this->getByIdprofessorByPessoa($array['id_pessoa']));
//    	exit;
    	return $this->getByIdprofessorByPessoa($array['id_pessoa']);
	}
	
    function existeProfessor($id)
    {
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT count(*) as 'Total' FROM professores WHERE id_pessoa=".$id;
        $result = $dbAdapter1->query($sql1);
       	$result=$result->fetchAll();
       	$result=$result[0]['Total'];

       	if($result>0)
       		return true;
    	
       	return false;
    }
    
    
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_pessoa='.$id));
    }
    
    function getByIdprofessorByPessoa($id)
    {
    	$result = $this->fetchAll($this->select()->where('deletado=0 AND id_pessoa='.$id))->toArray();
		
    	if(is_array($result))
    	{
 			$result=$result[0]['id_professor'];
    	}
       	
    	return $result;
    }
    
    function getByIdPessoa($id)
    {
    	$result = $this->fetchRow($this->select()->where('id_pessoa='.$id.' AND deletado=0'));
    	if($result)
    	   return($result->toArray());
    	else
    	   return false;
    }
	
}

?>