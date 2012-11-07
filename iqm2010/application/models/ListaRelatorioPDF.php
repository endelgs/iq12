<?php
require_once(APPLICATION_PATH.'/../library/relatorios/PDF.php');
class Application_Models_ListaRelatiorPDF extends Zend_Db_Table_Abstract
{
	protected $_name = '';
	public $where="";
	public $orderby="";
	public $_field;
	public $groupby="";

	function table($table_name)
	{
		$this->_name=$table_name;
	}

	function primary($primary)
	{
		$this->_primary=$primary;
	}

	function fields($fields)
	{
		$this->_field;
	}

	function participacaoEmBanca($where)
	{
		$sql="SELECT `nome` , `id_professor`
			  FROM `v_docente_banca`
			  WHERE $where
			  GROUP BY `nome` , `id_professor`";

		$dbAdapter1 = $this->getAdapter();
		$result = $dbAdapter1->query($sql);
		return $result->fetchAll();
	}

	function CRUDread()
	{
		if(($this->orderby!="") && ($this->where=="")) {
			$row = $this->fetchAll(
			$this->select($this->_field)->order($this->orderby)
			);
		} elseif($this->where!="" && $this->orderby=="")
		{
			$row = $this->fetchAll(
			$this->select($this->_field)
			->where($this->where)

			);
			
//			echo $this->select($this->_field)
//			->where($this->where);
//			exit;

		}
		elseif(($this->orderby!="") && ($this->where!=""))
		{
			$row = $this->fetchAll(
			$this->select($this->_field)
			->where($this->where)->order($this->orderby)

			);
//
//			echo $this->select($this->_field)
//			->where($this->where)->order($this->orderby);

		}

		else
		$row = $this->fetchAll(
		$this->select($this->_field)
		);



		return $row->toArray();
	}


}
?>