<?php
class Application_Models_Pos_Graduacoes extends Zend_Db_Table_Abstract
{
	protected $_primary = 'id_pos_graduacao';
	protected $_name    = 'pos_graduacoes';


	public function getPosGraduacoes($id = null)
	{
		$id = (int)$id;
		$row = $this->fetchAll();
		if (!$row) {
			throw new Exception("Count not find row $id");
		}
		return $row->toArray();
	}

	function atualizaPosGraduacao($id_pessoa, $id_tipo_curso)
	{
			
		$arr1=$this->getPosByPessoa($id_pessoa, $id_tipo_curso);

		if(count($arr1)==0)
		{
			$params3=array('id_tipo_curso'=>$id_tipo_curso,
							'id_pessoa'=>$id_pessoa
			);
			return $this->insert($params3);
		}else {
			
			return $arr1[0]['id_pos_graduacao'];
		}
	}


        public function getMestradoByPessoa($id_pessoa)
        {
            $sql=$this->select()->where(' id_pessoa="'.$id_pessoa.'" AND id_tipo_curso="3" ');
            $row = $this->fetchRow($sql);

            return $row->toArray();
        }


	function getPosByPessoa($id_pessoa,$curso){
			
		$this->_name = 'pos_graduacoes';
		$rows = $this->fetchAll(
		$this->select()
		->where("id_pessoa =$id_pessoa AND id_tipo_curso=$curso")
    ->order("id_pos_graduacao DESC")
    ->limit("1")
		);
		return($rows->toArray());
	}

	function getPessoaByPos($id_pos_graduacao,$curso)
	{
		$this->_name = 'pos_graduacoes';
		$rows = $this->fetchAll(
		$this->select()
		->where("id_pos_graduacao =$id_pos_graduacao AND id_tipo_curso=$curso")
		);
		return($rows->toArray());
	}

	function getPessoaByPosSemCurso($id_pos_graduacao)
	{
		$this->_name = 'pos_graduacoes';
		$rows = $this->fetchAll(
		$this->select()
		->where("id_pos_graduacao =$id_pos_graduacao")
		);
		return($rows->toArray());
	}


	public function updatePosGraduacao($data,$id_pos_graduacao)
	{
		$this->_name = 'pos_graduacoes';
		$this->update($data,'id_pos_graduacao='.(int)$id_pos_graduacao);
	}

	public function addPosGraduacao($data)
	{
		$this->_name = 'pos_graduacoes';
		return $this->insert($data);
	}
}
?>