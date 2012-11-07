<?php
class Application_Models_Proficiencia_Linguas extends Zend_Db_Table_Abstract
{
    protected $_name = 'proficiencia_lingua';
    
    public function CRUDcreate($array)
    {
        return($this->insert($array));
    }

    function CRUDreadView()
    {
    	$this->_name='v_proficiencia_lingua';
    	$this->_primary = 'id_proficiencia_lingua';
        //$rows = $this->fetchAll($this->select()->where('id <> 1')->order('cidade'));
        $rows = $this->fetchAll($this->select());
        
        return($rows->toArray());
    }
    
    public function getProficienciaByIdAluno($id)
    {	
        return($this->fetchAll($this->select()->where('id_pos_graduacao='.$id))->toArray()); 
    }
    
   	public function getProficienciaById($id)
    {	
        return($this->fetchAll($this->select()->where('id_proficiencia_lingua='.$id))->toArray()); 
    }
    
    public function CRUDupdate($array, $id)
    {
        //print_r($array);

    	$this->update($array, 'id_proficiencia_lingua='.$id);
    }
    
    public function CRUDdelete($id)
    {

    	return($this->delete('id_proficiencia_lingua='.$id));
    }
}
?>