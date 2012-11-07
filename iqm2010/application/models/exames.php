<?php 
class Application_Models_Exames extends Zend_Db_Table_Abstract
{
    protected $_name = 'exames';
    
    public function getExames($id_posgraduacao) 
    {
    	$this->_name = 'v_exames';
    	$this->_primary = 'id_exame';
        $row = $this->fetchAll(
            $this->select()
            ->where('id_pos_graduacao = '.$id_posgraduacao)
            ->order('id_exame DESC')
            ->limit('1')
        );
        return $row->toArray();
    }
    
    public function addExames($data){
    	$this->_name = 'exames';
        return $this->insert($data);
    }
    
 	public function updateExame($data, $id)
    {
    	$this->_name = 'nota_exames';
    	$this->delete('id_exame='. $id);
    	$this->_name = 'exames';		
        return $this->update($data, 'id_exame=' . $id);
    }
    
    public function updatePosGraduacaoExame($data,$id)
    {
        $this->_name = 'pos_graduacoes';
         return $this->update($data, 'id_pos_graduacao=' . $id);
    }
    
    public function updateVerificaGravacaoPos($id)
    {
        $this->_name = 'pos_graduacoes';
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT n_exame FROM pos_graduacoes WHERE id_pos_graduacao={$id};";
        $a = $dbAdapter1->query($sql1);
        $b = $a->fetchAll();
        
        return $b[0]['n_exame'];
    }
    
    public function addNotasExames($data)
    {
    	$this->_name = 'nota_exames';
    	return $this->insert($data);
    }
    
    public function getNotasExame($id_exame) 
    {
    	$this->_name = 'nota_exames';
        $row = $this->fetchAll(
            $this->select()
            ->where('id_exame = '.$id_exame)
        );
        return $row->toArray();
    }
    
    public function deleteExames($id)
    {
    	if($id)
    	{
		    $this->_name = 'nota_exames';
		    $this->delete('id_exame='. $id);
		    $this->_name = 'exames';
		    $this->delete('id_exame =' . (int)$id);
    	}    
    }
    
    public function gravaOrientador($id)
    {
    	
    }
}


?>