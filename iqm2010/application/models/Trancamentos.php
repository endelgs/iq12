<?php
class Application_Models_Trancamentos extends Zend_Db_Table_Abstract
{
    protected $_name = 'trancamentos';
    protected $_primary = 'id_trancamento';
    
    public function addTrancamento($data)
    {
        $this->insert($data);
    }    
    public function getByIngresso($idIngresso)
    {
        $rows = $this->fetchAll($this->select()->where('id_ingresso='.$idIngresso));
        return $rows->toArray();
    }
    
    public function getTrancamentoByID($id)
    {
        $rows = $this->fetchAll($this->select()->where('id_trancamento='.$id));
        return $rows->toArray();
    }
    
    function deleteTrancamento($id)
    {
    	$result=$this->delete('id_trancamento='.(int)$id);
    }
    
    function getSituacoesTrancamentoByIDIngresso($id)
    {
    	$this->_name='v_situacoes_trancamento';
    //	$this->_primary = 'id';
    	
        $rows=$this->fetchAll($this->select()->where('id_ingresso='.$id));
        return $rows->toArray();
    }
    
    function getTrancamentosByIDIngresso($id)
    {
    	$this->_name='v_trancamentos';
    	$this->_primary = 'id';
    	
        $rows=$this->fetchAll($this->select()->where('id_ingresso='.$id));
        return $rows->toArray();
    
    }
    
    public function updateTrancamento($data,$id)
    {
         return $this->update($data, 'id_trancamento=' . $id);
    }
    
    
}