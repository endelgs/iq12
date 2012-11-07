<?php
class Application_Models_TipoCursos extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipo_cursos';
    
    function CRUDreadByNome($tipoCurso)
    {
        return($this->fetchAll($this->select()->where("deletado=0 AND tipo_curso LIKE '%$tipoCurso%'"))->toArray());
    }
    
}
?>