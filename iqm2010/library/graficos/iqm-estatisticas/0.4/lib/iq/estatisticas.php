<?php
class IQ_Estatisticas extends QueryObject
{
	public $titulo;
	public $descricao;
	public $headers = array();
	
	public $mainAxisField;
	public $secondaryAxisFields = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	public function crossover($functions = array())
	{
		$data = array();
		$fields = array();
		
		foreach($functions as $f)
		{
			if(method_exists($this,$f))
			{	
				$result = $this->$f();
				$data = array_merge($data,$result);
				if(count($result) > 0)
					$fields = array_merge($fields,array_keys($result[0]));
			}
		}
		$fields = array_unique($fields);
		foreach($fields as $field)
			foreach($data as $key => $value)
				if(!isset($data[$key][$field]))
					$data[$key][$field] = 0;
		
		$checked = array();		
		foreach($data as $key => $value)
		{
			foreach($data as $k => $v)
			{
				if($key != $k && $value[$this->mainAxisField] == $v[$this->mainAxisField] && !in_array($value[$this->mainAxisField],$checked))
				{
					foreach($this->secondaryAxisFields as $saf)
						$data[$key][$saf] = $data[$k][$saf]+$data[$key][$saf];//($data[$key][$saf] == 0)?$data[$k][$saf]:$data[$key][$saf];
					$checked[] = $value[$this->mainAxisField];
					unset($data[$k]);
				}
				
			}
		}
		return $data;
	}
	
	// Alunos e defesas por ano
	public function adpa()
	{
		$result = $this->crossover(array('alunosPorAno','defesasPorAno'));
		$this->mainAxisField = 'ano';
		$this->titulo 		= "Alunos e Defesas Por Ano";
		$this->descricao 	= "Este relatório mostra a quantidade de alunos e defesas por ano";
		$this->headers = array(
			array('name' => 'qtdAPA', 'value' => 'Alunos por ano'),
			array('name' => 'qtdDPA', 'value' => 'Defesas por ano'),
			array('name' =>'ano', 'value' => 'Ano'));
		
		return $result;
	}
	public function alunosPorAgencia()
	{
		$this->titulo 		= "Alunos Por Agência";
		$this->descricao 	= "Quantidade de alunos por agência na data atual";
		
		$this->mainAxisField = 'agencia';
		$this->secondaryAxisFields[] = 'qtdAPAg';
		
		$sql = "SELECT
					COUNT(id_pos_graduacao) AS qtdAPAg,
					a.agencia AS agencia
					
				FROM
					pos_graduacoes pg,
					bolsas b,
					pessoas p,
					agencias a

				WHERE
				-- relacionamentos
					a.id_agencia = b.id_agencia AND
					b.id_posgraduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					
				-- condicoes
					b.data_fim > NOW() AND
					b.data_inicio < NOW() AND
					b.id_motivo_cancelamento = 1 AND
					
				-- deletados
					b.deletado = 0 AND
					a.deletado = 0 AND
					p.deletado = 0
					
				GROUP BY
					b.id_agencia";

		$this->headers = array(
			array('name' => 'agencia','value' => 'Agência'),
			array('name' => 'qtdAPAg','value' => 'Quantidade'));
		
		return $this->query($sql,true,array('force_array' => true));
		
	}
	public function alunosSemBolsa()
	{
		$this->titulo 		= "Alunos Sem Bolsa";
		$this->descricao 	= "Este relatório mostra a quantidade de alunos sem bolsa por orientador (dados cadastrados na base)";
		
		$this->mainAxisField = 'nome';
		$this->secondaryAxisFields[] = 'qtdASB';
		
		$sql = "SELECT
					COUNT(pg.id_pos_graduacao) AS qtdASB,
					IFNULL(prp.nome,'-') AS nome
					
				FROM
					pos_graduacoes pg,
					pessoas p,
					pessoas prp,
					ingressos i,
					professores pr,
					orientadores o
					
				WHERE
				-- relacionamento
					p.id_pessoa = pg.id_pessoa AND
					pg.id_pos_graduacao = i.id_pos_graduacao AND
					i.id_ingresso = o.id_ingresso AND
					o.id_professor = pr.id_professor AND
					pr.id_pessoa = prp.id_pessoa AND
					pr.id_professor = o.id_professor AND
					
				-- condicoes
					o.atual = 1 AND
					pg.id_pos_graduacao NOT IN
					(
						SELECT
							b.id_posgraduacao
					
						FROM
							bolsas b,
							pos_graduacoes pg,
							pessoas p
							
						WHERE
						-- relacionamento
							b.id_posgraduacao = pg.id_pos_graduacao AND
							pg.id_pessoa = p.id_pessoa AND
							
						-- deletado
							b.deletado = 0 AND
							p.deletado = 0
					) AND
					
				-- deletado
					p.deletado = 0 AND
					pr.deletado = 0
					
				GROUP BY
					pr.id_professor";
		
		$this->headers = array(
			array('name' => 'nome','value' => 'Orientador'),
			array('name' => 'qtdASB', 'value' => 'Quantidade'));
		
		return $this->query($sql,true,array('force_array' => true));
		
	}
	
	public function alunosPorOrientador()
	{
		$this->titulo 		= "Alunos Por Orientador";
		$this->descricao 	= "Este relatório mostra a quantidade de alunos por orientador (dados cadastrados na base)";
		
		$this->mainAxisField = 'nome';
		$this->secondaryAxisFields[] = 'qtdAPO';
		
		$sql = "SELECT
					COUNT(p.id_pessoa) AS qtdAPO,
					IFNULL(prp.nome,'-') AS nome
					
				FROM
					pessoas prp,
					pessoas p,
					pos_graduacoes pg,
					professores pr,
					orientadores o,
					ingressos i
					
				WHERE
				-- relacionamentos
					p.id_pessoa = i.id_ingresso AND
					p.id_pessoa = pg.id_pessoa AND
					o.id_ingresso = i.id_ingresso AND
					pr.id_professor = o.id_professor AND
					prp.id_pessoa = pr.id_pessoa AND
					
				-- condicoes
					o.atual = 1 AND
					
				-- deletado
					p.deletado = 0 AND
					pr.deletado = 0 AND
					prp.deletado = 0
					
				GROUP BY
					pr.id_professor";
		
		$this->headers = array(
			array('name' => 'nome','value' => 'Orientador'),
			array('name' => 'qtdAPO', 'value' => 'Quantidade'));
		
		return $this->query($sql,true,array('force_array' => true));
		
	}
	public function alunosPorSemestre()
	{
		$this->titulo 		= "Alunos Por Semestre";
		$this->descricao 	= "Este relatório mostra a quantidade de alunos por período (dados cadastrados na base)";
		
		$this->mainAxisField = 'periodo';
		$this->secondaryAxisFields[] = 'qtdAPS';
		
		$qBuilder = new SQL_QueryBuilder(array(
			'fields' 		=> "p.id_periodo, IF(ISNULL(p.periodo),'Nenhum',p.periodo) AS periodo, count(i.id_periodo) AS qtdAPS",
			'tables' 		=> 'ingressos i, periodos p',
			'conditions' 	=> 'i.id_periodo = p.id_periodo',
			'append' 		=> 'GROUP BY i.id_periodo ORDER BY p.id_periodo'
		));
		
		$this->headers = array(
			array('name' => 'periodo','value' => 'Período'),
			array('name' => 'qtdAPS', 'value' => 'Quantidade'));
		
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
		$this->headers = array(
			array('name' => 'qtdAPA', 'value' => 'Quantidade'), 
			array('name' => 'ano', 'value' => 'Ano'));
		
		$this->secondaryAxisFields[] = 'qtdAPA';
		$this->mainAxisField = 'ano';
		
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
		$sql = "SELECT COUNT(1) AS qtdAPA, ano FROM tmp_count GROUP BY ano";
		$result = $this->query($sql,true,array('force_array'));
		
		// Salvo os dados do resultado da consulta
		$affectedRows = $this->lastResultMeta->affectedRows;
		$fieldCount = $this->lastResultMeta->fieldCount;
		
		// Por fim, dropo a tabela temporaria
		$sql = "DROP TABLE tmp_count";
		$this->query($sql);

		$this->lastResultMeta->affectedRows = $affectedRows;
		$this->lastResultMeta->fieldCount = $fieldCount;

		// E retorno o resultado
		return $result;
	}
	public function defesasPorAno()
	{
		$this->titulo 		= "Defesas por Ano";
		$this->descricao 	= "Este relatório mostra a quantidade de teses e dissertações defendidas em cada ano";
		$this->mainAxisField 	= 'ano';
		$this->secondaryAxisFields[] = 'qtdDPA';
		
		$qBuilder = new SQL_QueryBuilder(array(
			'fields' 		=> 'YEAR(d.data_defesa) AS ano, COUNT(d.id_defesa) AS qtdDPA',
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
				'fields' 		=> 'YEAR(d.data_defesa) AS ano, COUNT(d.id_defesa) AS qtd',
				'tables' 		=> 'defesa d, pos_graduacoes pg, pessoas p',
				'conditions' 	=> 'd.id_pos_graduacao = pg.id_pos_graduacao AND pg.id_pessoa = p.id_pessoa AND p.deletado = 0',
				'append' 		=> 'GROUP BY ano ORDER BY ano'
				));
			 
		}
		//echo $qBuilder->getQuery();
		$this->headers = array(
			array('name' => 'qtdDPA', 'value' => 'Quantidade'),
			array('name' => 'ano', 'value'=> 'Ano')
		);
		
		return $this->query($qBuilder->getQuery(),true,array('force_array' => true));
	}
}