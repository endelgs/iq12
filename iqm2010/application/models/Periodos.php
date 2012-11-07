<?php
class Application_Models_Periodos extends Zend_Db_Table_Abstract
{
    protected $_name = 'periodos';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_periodo <> 1')->order('periodo'))->toArray());
    }
    
    function CRUDreadByNome($periodo)
    {
        return($this->fetchAll($this->select()->where("deletado=0 AND periodo LIKE '$periodo%'"))->toArray());
    }
    
    
    function CRUDupdate($array, $id)
    {
       $this->update($array, 'id_periodo='.$id);
    }
    function CRUDdelete($id)
    {
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "UPDATE periodos SET deletado=1 WHERE id_periodo={$id};";
        $dbAdapter1->query($sql1);

        return($this->update(array('deletado'=>'1'), 'id_periodo='.$id));
    }
    
    public function getPeriodo($id_periodo)
    {
        $row = $this->fetchRow(
            $this->select()
            ->where('id_periodo = '.$id_periodo)
        );
        
        if(is_array($row))
      		return $row->toArray();
       	else
       		return '';
      
    }
}
?>