<?php

class Application_Models_Cidades extends Zend_Db_Table_Abstract {

    protected $_name = 'cidades';

    public function getId($cidade, $uf) {

        //$cidade = utf8_decode($cidade);
        $dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT id_cidade 
				FROM  `cidades` 
				WHERE  ( CONVERT(upper(`cidade`) using utf8)  LIKE '%" . strtoupper($cidade) . "%' OR upper(`cidade`) LIKE '%" . strtoupper($cidade) . "%' ) AND uf='$uf'";
       // echo $sql1;
        try {

            $a = $dbAdapter1->query($sql1);
            $b = $a->fetchAll();
        } catch (Exception $e) {

            return 1;
        }


        return $b[0]['id_cidade'];
    }

}

?>