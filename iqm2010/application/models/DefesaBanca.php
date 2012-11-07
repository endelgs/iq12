<?php
class Application_Models_DefesaBanca extends Zend_Db_Table_Abstract
{
    protected $_name = 'defesa_banca';
    
    function CRUDcreate($array)
    {	
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select())->toArray());
    }
    
    function viewNomeAtribuicao()
    {        
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT * FROM v_banca_defesa_professor";
        $result = $dbAdapter1->query($sql1);
        return $result->fetchAll();
    }
    
    function CRUDQLdelete($id)
    {
    	return($this->delete('id_defesa='.$id));
    }
    
    function CRUDupdate($array, $id)
    {
        //print_r($array);
    	$this->update($array, 'id_defesa='.$id);
    }
    function CRUDdelete($dis)
    {
    	return($this->delete('id_defesa_banca='.$dis));
    }
   
    function CRUDreadOne($array)
    {
    	return ($this->fetchRow($this->select()
    		->where('id_defesa = '.$array['id_defesa'].' 
    		AND id_professor ='.$array['id_professor'])
    	));
    }
    
    function CRUDdeletebyDe($dis)
    {
    	return($this->delete('id_defesa='.$dis));
    }
   
    function totalProfAtri()
    {
    	$dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT count(*) as Total FROM v_defesa_professor_atribuicao";
        $result = $dbAdapter1->query($sql1);
        $result=$result->fetchAll();
//        var_dump($result[0]['Total']);
//        exit;
        return $result[0]['Total'];
        
    }
    
    function viewRead($id)  
    {	
    	$this->_name = 'v_defesa';
    	$this->_primary = 'id_defesa';
        return($this->fetchAll($this->select()->where('id_pos_graduacao='.$id))->toArray());
    }
}
?>