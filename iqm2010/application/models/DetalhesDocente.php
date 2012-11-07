<?php
class Application_Models_DetalhesDocente extends Zend_Db_Table_Abstract
{
    protected $_name = 'professor_detalhes';
    
    function CRUDcreate($array, $idprofessor)
    {

    	if ($this->existeDetalheProfessor($idprofessor))
    	{   		
    		return $this->update($array, 'id_professor='.$idprofessor);
    	}
    		
    	return $this->insert($array);
    }
    function CRUDread($idpessoa)
    {
     	$this->_name="v_docente_dados";
    	$this->_primary="id_professor_detalhes";
    	
    	return($this->fetchAll($this->select()
        	->where('id_pessoa = '.$idpessoa)
        )->toArray());
    }
    
    function existeDetalheProfessor($idprofessor)
    {

    	$dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT count(*) as 'Total' FROM professor_detalhes WHERE id_professor='$idprofessor'";
        $result = $dbAdapter1->query($sql1);
       	$result=$result->fetchAll();
       	$result=(int)$result[0]['Total'];
       	
       	if($result>0)
       		return true;
    	
       	return false;
    }
    
    function CRUDdelete($id)
    {
    	return($this->delete('id_professor='.$id));
    }
}
?>