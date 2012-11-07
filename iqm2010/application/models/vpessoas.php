<?php
class Application_Models_VPessoas extends Zend_Db_Table_Abstract
{
    protected $_name = 'v_pessoas';
    protected $_primary = 'id';
     
	public function getPessoa($id_pessoa)
	{
		$row = $this->fetchRow(
		
			$this->select()
			->where('id = '.$id_pessoa)
		);
		return $row->toArray();
	}
}
?>