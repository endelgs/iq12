<?php
class Application_Models_OferecimentosDisciplinas extends Zend_Db_Table_Abstract
{
    protected $_name = 'oferecimentos_disciplinas';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
    }
    function CRUDread()
    {
        return($this->fetchAll($this->select()->where('deletado=0 AND id_oferecimento_disciplina <> 1')->order('oferecimento_disciplina'))->toArray());
    }
    function CRUDupdate($array, $id)
    {
       $this->update($array, 'id_oferecimento_disciplina='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_oferecimento_disciplina='.$id));
    }
    
    public function getOferecimentosDisciplinas($id)
    {
        $row = $this->fetchRow(
            $this->select()
            ->where('id_oferecimento_disciplina = '.$id)
        );
      
        return $row->toArray();
    }
    
}
?>