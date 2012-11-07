<?php 
class ParamChecker
{
	public function __construct()
	{
		
	}
	public function getParam($params)
	{
		// Campos do array necessarios pra funcionar:
		// rel_table 	- nome da tabela onde existe o relacionamento das chaves
		// need			- nome do campo que eu preciso
		// got			- nome do campo que eu tenho
		// where		- se eu quiser uma condicao especifica
		// value		- valor do campo que eu tenho
    // policy   - indica se devo pegar o primeiro ou ultimo
		// ==============================================================================
		// ainda nao funciona
		// redirect		- true|false indica se deve redirecionar ou nao 
		// redirect_to	- pra onde deve redirecionar. So valido quando redirect eh true
		
		// Caso venha algo na clausula where, nao importa o que vier em got ou value
		// simplesmente uso ela pra comparar no banco
		$where = (isset($params['where']))?$params['where']:$params['got'].' = '.$params['value'];
		
		// Crio a tabela
		$table = new Zend_Db_Table($params['rel_table']);
		// E dou um fetch
		$result = $table->fetchAll($table->select($params['need'])->where($where))->toArray();
		
		// No final, retorno so o campo de que preciso
    if(isset($params['policy'])){
      if($params['policy'] == 'last')
        $result[0][$params['need']] = $result[count($result)-1][$params['need']];
    }
		return $result[0][$params['need']];
	}
}
?>