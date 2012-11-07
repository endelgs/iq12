<?php
class Table extends Zend_Db_Table_Abstract {

    function read($sql) {

        $dbAdapter1 = $this->getAdapter();
        $result = $dbAdapter1->query($sql);
        $arr=$result->fetchAll();
        foreach($arr as $index=>$a){
            
            foreach ($a as $index2=>$b){
                
                $arr[$index][$index2]=$b;
            }
        }
        
        return $arr;
    }

}

?>