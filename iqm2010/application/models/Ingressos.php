<?php
class Application_Models_Ingressos extends Zend_Db_Table_Abstract
{
    protected $_name = 'ingressos';
    public function addIngressos($data)
    {
       // echo '<pre>';
    //	print_r($data);
        //die();
    	return $this->insert($data);
    }

    public function updateIngressos($data, $id)
    {
    	$this->_name = 'ingressos';
        return $this->update($data, 'id_ingresso='.$id);
    }
    
    public function getIngresso($id)
    {
    	$row = $this->fetchRow($this->select()->where('id_ingresso='.$id));
    	return $row->toArray();
    }



    public function getIngressosByIDPosGraduacao($id_pos_graduacao)
    {
    	   $result=$this->fetchAll($this->select()->where('id_pos_graduacao='.$id_pos_graduacao));
    	   return $result->toArray();
    }
    
   public function getIngressosAll()
   {
    	   $result=$this->fetchAll($this->select());
    	   return $result->toArray();
   }
   
   public function updateIngressosEspecial($dataIngresso)
   {
    		$sql = "UPDATE ingressos set 'data_ingresso'='$dataIngresso';";
			$result = $dbAdapter->query($sql);
			$result = $result->fetchAll();
   }
   
   public function duplicaIngressoParaDoutorado($id_pos,$id_pessoa, $novoIdPOs){
       
       $arrIngresso=$this->getIngressosByIDPosGraduacao($id_pos);
       $ingresso_antigo=$arrIngresso[0]['id_ingresso'];
       unset($arrIngresso[0]['id_ingresso']);
       unset($arrIngresso[0]['id_pos_graduacao']);
       unset($arrIngresso[0]['data_ingresso']);
       unset($arrIngresso[0]['data_integralizacao']);
       
       $arrIngressoNovo=$this->getIngressosByIDPosGraduacao($novoIdPOs);
       //var_dump($arrIngressoNovo);
      // exit;
       $arrIngresso[0]['id_pos_graduacao']=$novoIdPOs;
       if(!empty ($arrIngressoNovo)){
           $id_ingresso=$arrIngressoNovo[0]['id_ingresso'];
          // var_dump($arrIngresso[0]);
          // var_dump($id_ingresso);
          // exit;
          $this->updateIngressos($arrIngresso[0], $id_ingresso);
          // echo 'up';
       }else{
           
           //var_dump($arrIngresso);
           $id_ingresso=$this->insert($arrIngresso[0]);
            //echo 'in';
       }
       
       $array['id_ingres_antigo']=$ingresso_antigo;
       $array['id_ingres_novo']=$id_ingresso;
       return $array;
       
   }
}
?>