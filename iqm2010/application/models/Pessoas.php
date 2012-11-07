<?php
class Application_Models_Pessoas extends Zend_Db_Table_Abstract
{
	protected $_name = 'pessoas';
        
  private function escapaArray($array){
 
    foreach($array as $index=>$v){
      $array[$index]=str_replace("'", "", $v);
    }
    return $array;      
  }
	public function addPessoa($array)	{       
    $array=$this->escapaArray($array);
		return($this->insert($array));
	}
	public function updatePessoa($array, $id)
	{       
    $array=$this->escapaArray($array);
		return $this->update($array, 'id_pessoa=' . $id);
		//return $this->lastInsertId();
	}

	public function atualizaPessoa($array){
    
		$id_pessoa=$this->getIdPessoa($array['nome'], $array['cpf'],$array['nascimento']);

		if($id_pessoa==0){       
      $array=$this->escapaArray($array);
			return($this->insert($array));
		}else{   
      $array=$this->escapaArray($array);
			$this->update($array, 'id_pessoa=' . $id_pessoa);

			return $id_pessoa;
		}
			
	}

	public function getIdPessoa($nome,$cpf,$nascimento){
		
		$sql=$this->select()
		->where("nome = '$nome' AND (cpf='$cpf' OR nascimento='$nascimento')");
		$row = $this->fetchAll(
			$sql
		);

		try {
			$a=$row->toArray();
			return $a[0]['id_pessoa'];
				
		} catch (Exception $e) {
			return 0;
		}
	}

	public function getPessoa($id_pessoa){
		$row = $this->fetchRow(
		$this->select()
		->where('id_pessoa = '.$id_pessoa)
		);
		//return $row->toArray();
		return $row;
	}

	public function deletePessoa($id){
		return $this->update(array('deletado' => '1'), 'id_pessoa=' . $id);
	}
}