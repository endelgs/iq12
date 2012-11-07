<?php
class Application_Models_Paises extends Zend_Db_Table_Abstract
{
	protected $_name = 'paises';

	public function getPaises($id = null)
	{
		$id = (int)$id;
		$row = $this->fetchAll();
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}

	public function getId($pais)
	{
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT * 
				FROM  `paises` 
				WHERE  `pais` LIKE  '$pais'";
        $a = $dbAdapter1->query($sql1);
        $b = $a->fetchAll();
        return $b[0]['id_pais'];
	}

	public function addPaises($pai)
	{
		$data = array(
            'pais' => $pais,
		);
		$this->insert($data);
	}

	public function updatePaises($id, $pais)
	{
		$data = array(
            'pais' => $pais,
		);
		$this->update($data, 'id = '. (int)$id);
	}

	public function deletePaises($id)
	{
		$this->delete('id =' . (int)$id);
	}
}

?>