<?php
class Application_Models_ProfessoresBancaDefesa extends Zend_Db_Table_Abstract
{
    protected $_name = 'v_professor_defesa';
    protected $_primary = 'id';

    function CRUDread($datainicio,$datafim)
    {
       	$dbAdapter1 = $this->getAdapter();
        $sql1 = "SELECT id, nome FROM v_professor_defesa 
        WHERE `data_defesa` > '$datainicio'
AND `data_defesa` < '$datafim' AND id<>1 GROUP BY id, nome ORDER BY nome";
        $result = $dbAdapter1->query($sql1);
       	$result=$result->fetchAll();     	
        return $result; 
    }

}

?>