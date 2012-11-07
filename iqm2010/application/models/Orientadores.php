<?php

class Application_Models_Orientadores extends Zend_Db_Table_Abstract {

    protected $_name = 'orientadores';
    protected $_primary = 'id_orientador';

    public function addOrientadores($data) {
        $this->_name = 'orientadores';
        $this->insert($data);
    }

    public function replicaOrientadores($id_ingres_velho, $id_ingres_novo) {
        $arrOrientadores = $this->getByIngressoSemView($id_ingres_velho);
        //unset($arrOrientadores[""]);
       
       // if (!$arrOrientadores)
        foreach($arrOrientadores as $index=>$a){
                
            unset($arrOrientadores[$index]["id_orientador"]);
              $arrOrientadores[$index]["id_ingresso"]=$id_ingres_novo;
             // var_dump($arrOrientadores[$index]);
              $this->addOrientadores($arrOrientadores[$index]);
             
        }
       // echo 'REPLICA';
    }

    public function selectAll() {
        $result = $this->fetchAll($this->select());
        return $result;
    }

    public function getByIngressoSemView($id) {
        $this->_name = 'orientadores';
        $row = $this->fetchAll($this->select()->where('id_ingresso=' . $id));

        return $row->toArray();
    }

    public function getByIngresso($idIngresso) {
        $this->_name = 'v_orientadores';
        $rows = $this->fetchAll($this->select()->where('id_ingresso=' . $idIngresso)->order('nome'));
        return $rows->toArray();
    }

    public function orientadoresPorData($datainicio, $datafim, $id_tipo_curso="") {
        $sql1 = "SELECT `id_orientador_professor` as id, `nome_orientador` as nome
			  FROM `v_defesa_aluno_orientador`
			  WHERE `data_defesa` > '$datainicio'
		AND `data_defesa` < '$datafim' AND id_orientador_professor<>1 AND id_tipo_curso='$id_tipo_curso'
			  GROUP BY `id_orientador_professor` , `nome_orientador`";

        $dbAdapter1 = $this->getAdapter();
        $result = $dbAdapter1->query($sql1);
        $result = $result->fetchAll();
        return $result;
    }

    public function removeAtuais($idIngresso) {
        $set = array('atual' => '0');
      	$where = "id_ingresso='".$idIngresso."'";  
	//echo $idIngresso;
    //  	echo $where;
     // 	exit;
        $this->update($set, $where);
    }

    public function deleteOrientador($id) {
        $result = $this->delete('id_orientador=' . (int) $id);
    }

    public function updateOrientadores($data, $id) {
        $this->_name = 'orientadores';
        return $this->update($data, 'id_orientador=' . $id);
    }

    public function updateOrientador($data, $id) {
        return $this->update($data, 'id_professor=' . $id);
    }

    public function getOrientadorByPosEData($id_pos_graduacao, $datetime_sql) {
        $sql = "SELECT `nome`, `id_professor` FROM `v_orientadores_posgraduacao`  vo
		WHERE vo.`data_semmascara`<='$datetime_sql'
		AND vo.`id_pos_graduacao`='$id_pos_graduacao'
		ORDER BY vo.`data_semmascara` LIMIT 1";

        $dbAdapter1 = $this->getAdapter();
        $result = $dbAdapter1->query($sql);
        $result = $result->fetchAll();
        return $result[0];
    }

}

?>