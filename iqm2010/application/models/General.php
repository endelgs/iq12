<?php
class Models_General extends Zend_Db_Table_Abstract
{
    protected $_primary = 'id';
    protected $_name    = 'pessoas';
    
    public function listTable($table, $fields = array()) 
    {
        $tbl = new Zend_Db_Table($table);
        $row = $tbl->fetchAll();
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        $arr = $row->toArray();
		
        foreach($arr as $r)
		{
			$result[$r[$fields[0]]] = $r[$fields[1]];	
		}
        return $result;
    }

    public function listView($table, $fields = array()) 
    {
        $this->_name = $table;
        //echo $this->select()->where('deletado=0')->assemble();
        $rows = $this->fetchAll();
        $arr = $rows->toArray();
        //print_r($arr);
        foreach($arr as $r)
        {
            $result[$r[$fields[0]]] = $r[$fields[1]]; 
        }
        return $result;
    }
    
 	public function listViewHash($table, $fields = array()) 
    {
        $this->_name = $table;
        $rows = $this->fetchAll();
        $arr = $rows->toArray();
        $i=0;
        
        foreach($arr as $r)
        {
        	$i++;
            $result[$i] = '#'.$r[$fields[0]].'#'.$r[$fields[1]].'#'.$r[$fields[2]]; 
        }
        return $result;
    }
    
    public function getById($field, $value)
    {
        $this->_name = 'v_ids';
        $this->_primary = 'id_pessoa';
        //echo $this->select()->where("{$field}='{$value}'")->assemble();//die();
        $rows = $this->fetchRow($this->select()->where("{$field}='{$value}'"));
        //echo $this->select()->where("{$field}='{$value}'")->assemble();
        if($rows)
        {
        	return ($rows->toArray());
        	
        }
        else
        {
        	return false;
        }
        
    }
}

?>