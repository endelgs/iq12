<?php
class Models_Procurar extends Zend_Db_Table_Abstract
{
	protected $_primary = 'id';
	protected $_name    = 'v_busca_pessoas';

	function buscaPessoa($tipo, $query)
	{
		if($tipo == 1)
		{
			$a = "dados LIKE '%{$query}%'";
		}
		else
		{
			$a = "( CONVERT(upper(`nome`) using utf8)  LIKE '%" . strtoupper($query) . "%' OR upper(`nome`) LIKE '%" . strtoupper($query) . "%' )";
		}
		$this->_name = 'v_busca_pessoas';
		//( CONVERT(upper(`cidade`) using utf8)  LIKE '%" . strtoupper($cidade) . "%' OR
		//  upper(`cidade`) LIKE '%" . strtoupper($cidade) . "%' )

		//        echo $this->select()->where($a." AND deletado<>1");
		$rows = $this->fetchAll($this->select()->where($a." AND (deletado=0 OR ISNULL(deletado))"));
		$arr = $rows->toArray();

		if($arr)
		foreach ($arr as $index=>$a){

			if($arr[$index]["RA"]=="" ||
			$arr[$index]["RA"]==NULL ||
			empty($arr[$index]["RA"])){
				$arr[$index]["RA"]=$this->raByIdPessoa($arr[$index]["id"]);
				//$arr[$index]["RA"]="TESTE";
				//var_dump($arr[$index]["RA"]);
				//exit;
			}


		}
		return $arr;
	}

	function raByIdPessoa($id_pessoa){
		//$id_pessoa=78;
		//echo $id_pessoa;
		$dbAdapter1 = $this->getAdapter();
		$sql1 = "SELECT id_pos_graduacao
				FROM `pos_graduacoes`
				WHERE `id_pessoa` =$id_pessoa AND id_tipo_curso=3";
		$result1 = $dbAdapter1->query($sql1);
		$result1=$result1->fetchAll();

		//var_dump($result1);

		$sql2 = "SELECT id_pos_graduacao
				FROM `pos_graduacoes`
				WHERE `id_pessoa` =$id_pessoa AND id_tipo_curso=5";
		$result2 = $dbAdapter1->query($sql2);
		$result2=$result2->fetchAll();

		// echo "2";
		//]var_dump($result2);
		//exit;

		$id_pos_mes=$result1[0]["id_pos_graduacao"];
		if($id_pos_mes!=""){
		$sql3="SELECT *
				FROM `ingressos`
				WHERE `id_pos_graduacao` =$id_pos_mes";


		$result3 = $dbAdapter1->query($sql3);
		$result3=$result3->fetchAll();
		$ra_mes=$result3[0][RA];
		}
		
		
		
		
		$id_pos_mes2=$result2[0]["id_pos_graduacao"]; 
		if($id_pos_mes2!=""){
		$sql4="SELECT *
				FROM `ingressos`
				WHERE `id_pos_graduacao` =$id_pos_mes2";
		$result4 = $dbAdapter1->query($sql4);
		$result4=$result4->fetchAll();
		$ra_dout=$result4[0][RA];
		}
		
		
		if($ra_mes!="")
		return $ra_mes;
		
		return $ra_dout;


	}
}
?>