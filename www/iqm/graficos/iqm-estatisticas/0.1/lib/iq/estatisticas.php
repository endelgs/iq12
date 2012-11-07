<?php
class IQ_Estatisticas extends QueryObject
{
	public $titulo;
	public $descricao;
	public $headers = array();
	
	public $mainAxisField;
	public $secondaryAxisField;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function alunosPorSemestre()
	{
		$this->titulo 		= "Alunos Por Semestre";
		$this->descricao 	= "Este relatório mostra a quantidade de alunos por período (dados cadastrados na base)"; 
		
		$this->mainAxisField = 'qtd';
		$this->secondaryAxisField = 'periodo';
		
		$qBuilder = new SQL_QueryBuilder(array(
			'fields' 		=> "p.id_periodo, IF(ISNULL(p.periodo),'Nenhum',p.periodo) AS periodo, count(i.id_periodo) AS qtd",
			'tables' 		=> 'ingressos i, periodos p',
			'conditions' 	=> 'i.id_periodo = p.id_periodo',
			'append' 		=> 'GROUP BY i.id_periodo ORDER BY p.id_periodo'
		));
		
		$this->headers = array('qtd' => 'Quantidade');
		
		return $this->query($qBuilder->getQuery(),true,array('force_array' => true));
	}
	
	public function defesasPorAno()
	{
		$this->titulo 		= "Defesas por Ano";
		$this->descricao 	= "Este relatório mostra a quantidade de teses e dissertações defendidas em cada ano";
		$this->mainAxisField = 'qtd';
		$this->secondaryAxisField = 'ano';
		
		$qBuilder = new SQL_QueryBuilder(array(
			'fields' 		=> 'YEAR(d.data_defesa) AS ano, COUNT(d.id_defesa) AS qtd',
			'tables' 		=> 'defesa d, pos_graduacoes pg, pessoas p',
			'conditions' 	=> 'd.id_pos_graduacao = pg.id_pos_graduacao AND pg.id_pessoa = p.id_pessoa AND p.deletado = 0',
			'append' 		=> 'GROUP BY ano ORDER BY ano'
		));
		
		$detailedSearch = false;
		
		$sql = '';
		if($detailedSearch)
		{
			$qBuilder->addCondition("pg.id_tipo_curso =".IQ_BRules::TC_MESTRADO."","AND");
			$sql = $qBuilder->getQuery();
		 	$qBuilder = new SQL_QueryBuilder(array(
				'fields' 		=> 'YEAR(d.data_defesa) AS ano, COUNT(d.id_defesa) AS data_defesa',
				'tables' 		=> 'defesa d, pos_graduacoes pg, pessoas p',
				'conditions' 	=> 'd.id_pos_graduacao = pg.id_pos_graduacao AND pg.id_pessoa = p.id_pessoa AND p.deletado = 0',
				'append' 		=> 'GROUP BY ano ORDER BY ano'
				));
			 
		}
		
		$this->headers = array(	
			'qtd' => 'Quantidade',
			'ano' => 'Ano'
		);
		
		
		return $this->query($qBuilder->getQuery(),true,array('force_array' => true));
	}
}