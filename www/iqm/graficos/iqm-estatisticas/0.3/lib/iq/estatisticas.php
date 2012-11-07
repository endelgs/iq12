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
	
	// De longe, essa eh a query mais complicada
	// Eh impossivel contar, pelas funcoes normais do mysql, os anos dentro de
	// um periodo. Por isso, essa query precisou de um trabalho extra no PHP

	// O que faco aqui eh buscar o aluno, o ano que ele entrou (ou reingressou)
	// e quanto tempo ele permaneceu na CPG a partir de entao
	
	// Feito isso, crio uma tabela temporaria e insiro a pessoa tantas vezes
	// quantas forem necessarias nessa tabela, onde cada entrada corresponde a
	// um ano que a pessoa esteve na CPG
	
	// Ao final disso tudo, posso contar quantas vezes essa pessoa aparece na 
	// tabela e contar
	
	
	public function alunosPorAno()
	{
		$this->titulo = 'Alunos por ano';
		$this->descricao = 'Quantidade de alunos matriculados por ano (incluindo os reingressantes)';
		$this->headers = array('qtd' => 'Quantidade', 'ano' => 'Ano');
		
		$this->mainAxisField = 'qtd';
		$this->secondaryAxisField = 'ano';
		
		// Query que pega o delta_t dos alunos normais e dos reingressantes
		$sql = "SELECT
					id_pessoa,
					ano_ingresso AS ano,
					(ano_conclusao - ano_ingresso) AS delta_t
					
				FROM
					(SELECT
						p.id_pessoa,
						YEAR(i.data_ingresso) AS ano_ingresso,
						IF(i.aluno_desligado = 0,YEAR(NOW()),YEAR(data_desligado)) AS ano_conclusao
					
					FROM
						pessoas p,
						pos_graduacoes pg,
						ingressos i
					
					WHERE
						p.id_pessoa = pg.id_pessoa AND
						pg.id_pos_graduacao = i.id_pos_graduacao AND
						(not isnull(data_ingresso)) AND
						data_ingresso != '0000-00-00 00:00:00' AND
						p.deletado = 0
					) AS t
				
				UNION
				
				SELECT
					id_pessoa,
					ano_ingresso AS ano,
					(ano_conclusao - ano_ingresso) AS delta_t

				FROM
					(SELECT
						p.id_pessoa,
						YEAR(i.data_reingresso) AS ano_ingresso,
						IF(i.aluno_desligado = 0,YEAR(NOW()),YEAR(data_desligamento)) AS ano_conclusao

					FROM
						pessoas p,
						pos_graduacoes pg,
						reingresso i

					WHERE
						p.id_pessoa = pg.id_pessoa AND
						pg.id_pos_graduacao = i.id_pos_graduacao AND
						(not isnull(data_reingresso)) AND
						data_reingresso != '0000-00-00 00:00:00' AND
						p.deletado = 0
					) AS t";

		// Capturo o resultado num array
		$result = $this->query($sql,true,array('force_array' => true));

		// Garanto que nao vou contar mais de uma vez o mesmo registro usando 
		// lixo de uma tabela que nao foi dropada antes
		$sql = "DROP TABLE IF EXISTS tmp_count";
		$this->query($sql);
		
		// Crio a tabela temporaria pra fazer os calculos
		$sql = "CREATE TEMPORARY TABLE tmp_count ( id_pessoa int, ano int)";
		$this->query($sql);
		
		// Array onde ficarao os inserts a serem feitos
		$inserts = array();
		// Array que lista as combinacoes id_pessoa/ano
		// Feito pra nao deixar inserir na tabela, mais de uma vez a mesma combinacao
		$combinacoes = array();
		
		foreach($result as $row)
		{
			$sql = "INSERT INTO tmp_count VALUES ";
			
			// Insiro todos os anos que o aluno estudou na tabela temporaria
			for($i = 0;$i < $row['delta_t']; $i++)
			{
				// Mas primeiro verifico se a combinacao nao foi inserida antes
				if(!isset($combinacoes[$row['id_pessoa']][$row['ano']]))
				{
					$inserts[] = "({$row['id_pessoa']},{$row['ano']})";
					$combinacoes[$row['id_pessoa']][$row['ano']] = 1;
				}
				$row['ano']++;
			}
			
			$sql .= implode(',',$inserts);

			$this->query($sql,false);
			$inserts = array();
		}
		
		// Agora sim, conto todos agrupados por ano
		$sql = "SELECT COUNT(1) AS qtd, ano FROM tmp_count GROUP BY ano";
		$result = $this->query($sql,true,array('force_array'));
		
		// Salvo os dados do resultado da consulta
		$affectedRows = $this->lastResultMeta->affectedRows;
		$numFields = $this->lastResultMeta->numFields;
		
		// Por fim, dropo a tabela temporaria
		$sql = "DROP TABLE tmp_count";
		$this->query($sql);

		$this->lastResultMeta->affectedRows = $affectedRows;
		$this->lastResultMeta->numFields = $numFields;

		// E retorno o resultado
		return $result;
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