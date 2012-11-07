<?php
class Application_Models_MotivoSuspensaoBolsa extends Zend_Db_Table_Abstract
{
    protected $_name = 'bolsa_motivo_suspensao';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {	 	
        return($this->fetchAll($this->select()->where('deletado=0 and id_bolsa_suspensao_motivo<>1')->order('suspensao_motivo'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
       $this->update($array, 'id_bolsa_suspensao_motivo='.$id);
    }
    function CRUDdelete($id)
    {
  		
    	return($this->update(array('deletado'=>'1'), 'id_bolsa_suspensao_motivo='.$id));
    }
}
?>