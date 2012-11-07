<?php
class Application_Models_Defesa extends Zend_Db_Table_Abstract
{
	protected $_name = 'defesa';

	function CRUDcreate($array)
	{
		return($this->insert($array));
	}
	function CRUDread($id)
	{
		$row = $this->fetchAll(
		$this->select()
		->where('id_pos_graduacao = '.$id)
		);
		return $row->toArray();
	}

	function readPorData($id_pos,$dataDATETIME)
	{
		 
		$row = $this->fetchAll(
		$this->select()
		->where('id_pos_graduacao = '.$id." AND con_data_entrega=>'".$dataDATETIME."'")
		);
		return $row->toArray();
	}

	function CRUDupdate($array, $id)
	{
//		        print_r($array);
//		        echo '<br>';
//		        echo 'ID:'.$id;
//		        exit;
            
		$this->update($array, 'id_defesa='.$id);
	}
	function CRUDdelete($id)
	{
		return($this->delete('id_defesa='.$id));
	}

	function viewRead($id)
	{
		$this->_name = 'v_defesa';
		$this->_primary = 'id_defesa';
		return($this->fetchAll($this->select()->where('id_pos_graduacao='.$id))->toArray());
	}
}
?>