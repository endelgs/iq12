<?php
class Application_Models_Coorientadores extends Zend_Db_Table_Abstract
{
    protected $_name = 'coorientadores';
    protected $_primary = 'id_coorientador';
    
    public function addOrientadores($data)
    {
        $this->insert($data);
    }
    
    public function selectAll()
    {
    	$result=$this->fetchAll($this->select());
    	return  $result;
    }
      public function getByCoSemView($id) {
        $this->_name = 'coorientadores';
        $row = $this->fetchAll($this->select()->where('id_ingresso=' . $id));

        return $row->toArray();
    }
        public function replicaCoorientadores($id_ingres_velho, $id_ingres_novo) {
        $arrOrientadores = $this->getByCoSemView($id_ingres_velho);
        //unset($arrOrientadores[""]);
      // var_dump($arrOrientadores);
       // if (!$arrOrientadores)
        foreach($arrOrientadores as $index=>$a){
                
            unset($arrOrientadores[$index]["id_coorientador"]);
              $arrOrientadores[$index]["id_ingresso"]=$id_ingres_novo;
             // var_dump($arrOrientadores[$index]);
              $this->addOrientadores($arrOrientadores[$index]);
             
        }
        
    }
    
    public function getByIngresso($idIngresso)
    {
    	$this->_name = 'v_coorientadores';
    	$rows = $this->fetchAll($this->select()->where('id_ingresso='.$idIngresso));
        return $rows->toArray();

    }
    
  /*  public function orientadoresPorData($datainicio, $datafim,$id_tipo_curso="")
    {
    	$sql1="SELECT `id_orientador_professor` as id, `nome_orientador` as nome
			  FROM `v_defesa_aluno_coorientador`
			  WHERE `data_defesa` > '$datainicio'
			  AND `data_defesa` < '$datafim' AND id_orientador_professor<>1 AND id_tipo_curso='$id_tipo_curso'
			  GROUP BY `id_orientador_professor` , `nome_orientador`";
    	
    	$dbAdapter1 = $this->getAdapter();
        $result = $dbAdapter1->query($sql1);
       	$result=$result->fetchAll();     	
        return $result; 
    
    }*/
    public function removeAtuais($idIngresso)
    {   

        $set = array( 'atual' => '0');
        $where = 'id_ingresso='.$idIngresso;    	
        $this->update($set, $where);
    }
    public function deleteOrientador($id)
    {
    	$result=$this->delete('id_coorientador='.(int)$id);
    }
    
	public function updateOrientadores($data,$id)
    {
         return $this->update($data, 'id_coorientador=' . $id);
    }
    
    
  	public function updateOrientador($data,$id)
    {
         return $this->update($data, 'id_oreintador=' . $id);
    }
    
}
?>