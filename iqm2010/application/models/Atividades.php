<?php
class Application_Models_Atividades extends Zend_Db_Table_Abstract
{
    protected $_name = 'atividades';
    
    function CRUDcreate($array)
    {
        return($this->insert($array));
     
    }
    function CRUDread($id)
    {       
    	$this->_name='v_atividades';
    	$this->_primary = 'id';
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT id, atividade, id_pos_graduacao, id_disciplina, DATE_FORMAT(inicio,'%d/%m/%Y') AS inicio, DATE_FORMAT(termino,'%d/%m/%Y') AS termino, DATE_FORMAT(data_cancelamento,'%d/%m/%Y') AS data_cancelamento, observacoes, professor, codigo,  cancelado, id_tipo_atividade, IF(cancelado = 1, 'Sim', 'N&atilde;o') AS cancelada, id_professor FROM `v_atividades` WHERE id_pos_graduacao=$id;";
        $result = $dbAdapter1->query($sql1);
        return $result->fetchAll();
    	
    }
    function CRUDupdate($array, $id)
    {
       $this->update($array, 'id_atividade='.$id);
    }
    function CRUDdelete($id)
    {
    	return($this->update(array('deletado'=>'1'), 'id_atividade='.$id));
    }
}
?>