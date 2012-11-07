<?php
class IQ_Relatorio extends QueryObject
{
	public static function getRelatoriosEntregues()
	{
		return IQ_Relatorio::getRelatorios('', array('add_clauses' => "r.aluno_data_entrega != '0000-00-00 00:00:00' ORDER BY r.aluno_data_entrega DESC"));
	}
	public static function getRelatoriosNaoEntregues()
	{
		return IQ_Relatorio::getRelatorios('', array('add_clauses' => "r.aluno_data_entrega = '0000-00-00 00:00:00' AND r.aluno_data_previsto < NOW() "));
	}
	
	public static function getRelatorios($relatorioID = '', $options = array())
	{
		$qObject = new QueryObject();
		
		//Pegando os relatórios do usuário corrente de acordo com os parametros recebidos
		$sql = "SELECT
					p.id_pessoa,
					p.nome,
					DATE_FORMAT(r.aluno_data_previsto,'%d/%m/%Y') AS data_prevista,
					DATE_FORMAT(r.aluno_data_entrega,'%d/%m/%Y') AS data_entrega,
					r.aluno_data_previsto,
					r.id_relatorio ";
		if(!empty($options['add_fields']))
			$sql .= ','.$options['add_fields'].' ';
					
		$sql .=	"FROM
					pessoas p,
					pos_graduacoes pg,
					ingressos i,
					relatorios r ";
		if(!empty($options['add_tables']))
			$sql .= ','.$options['add_tables'].' ';

		$sql .= "WHERE
					p.id_pessoa = pg.id_pessoa AND
					pg.id_pos_graduacao = i.id_pos_graduacao AND
					r.id_ingresso = i.id_ingresso AND
					p.deletado = 0 AND
					r.id_professor = '{$_SESSION['user']['id_professor']}' ";
		if(!empty($options['add_clauses']))
			$sql .= ' AND '.$options['add_clauses'].' ';
		
		//Se foi pedido apenas 1 relatorio em especifico
		if ($relatorioID)
			$sql .= " AND r.id_relatorio = '$relatorioID' ";

		$out = $qObject->query($sql,true,array('force_array' => true));

		return $out;
	}
	
	public static function getRelatorio($relatorioID, $restrictMore = '')
	{
		$qObject = new QueryObject();
		
		//Pegando as informações sobre o relatório especificado 
		$sql = "SELECT
					r.id_relatorio,
					DATE_FORMAT(r.aluno_data_inicio,'%d/%m/%Y') AS aluno_data_inicio,
					aluno_data_inicio AS adi,
					aluno_data_previsto AS adp,
					aluno_data_entrega AS ade,
					r.parecer,
					p.id_pessoa,
					p.nome
					
				FROM
					relatorios r,
					ingressos i,
					pessoas p,
					pos_graduacoes pg
					
				WHERE
					p.id_pessoa = pg.id_pessoa AND
					pg.id_pos_graduacao = i.id_pos_graduacao AND
					i.id_ingresso = r.id_ingresso AND
					r.id_relatorio = '$relatorioID' ";
		//Caso haja mais alguma restricao
		if ($restrictMore)
			$sql .= $restrictMore;

		$out = $qObject->query($sql,true);
		
		return $out;
	}
	
	public static function getRelatorioPendente($idProfessor, $options = array())
	{
		$qObject = new QueryObject();
		
		/*$qBuilder = new SQL_QueryBuilder(array(
			'fields' => "p.id_pessoa,p.nome,r.aluno_data_previsto,DATE_FORMAT(r.aluno_data_previsto,'%d/%m/%Y') AS data_prevista_f,	r.id_relatorio",
			'tables' => "relatorios r,	ingressos i, pos_graduacoes pg,	pessoas p, orientadores o",
			'conditions' => "r.id_ingresso = i.id_ingresso AND i.id_pos_graduacao = pg.id_pos_graduacao AND	pg.id_pessoa = p.id_pessoa AND o.id_ingresso NOT IN (SELECT	j.id_ingresso FROM ingressos j,	orientadores ori WHERE j.id_ingresso = ori.id_ingresso AND ori.atual = 1 AND ori.id_professor = '{$idProfessor}') AND p.deletado = 0 AND r.aluno_data_entrega = '0000-00-00 00:00:00' AND r.aluno_data_previsto < NOW()"
		));*/

		$sql = "SELECT
					p.id_pessoa,
					p.nome,
					r.aluno_data_previsto,
					DATE_FORMAT(r.aluno_data_previsto,'%d/%m/%Y') AS data_prevista_f,
					r.id_relatorio ";
		if(!empty($options['add_fields']))
			$sql .= ','.$options['add_fields'].' ';
					
		$sql .= "FROM
					relatorios r,
					ingressos i,
					pos_graduacoes pg,
					pessoas p,
					orientadores o ";
		if(!empty($options['add_tables']))
			$sql .= ','.$options['add_tables'].' ';			
		$sql .= "WHERE
					r.id_ingresso = i.id_ingresso AND
					i.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					o.id_ingresso NOT IN (
						SELECT 
							j.id_ingresso 
						FROM 
							ingressos j, 
							orientadores ori 
						WHERE 
							j.id_ingresso = ori.id_ingresso AND 
							ori.atual = 1 AND 
							ori.id_professor = '{$idProfessor}'
						) AND
					p.deletado = 0 AND
					r.aluno_data_entrega = '0000-00-00 00:00:00' AND
					r.aluno_data_previsto < NOW() ";
		if(!empty($options['add_clauses']))
			$sql .= ' AND '.$options['add_clauses'].' ';
		
		$out = $qObject->query($sql,true, array('force_array' => true));
		
		return $out;
	}
	// Esse relatorio pega os relatorios nao entregues exclusivamente pelos 
	// orientandos atuais do professor
	public static function getRelatorioPendenteOrientador($professorID = '', $options = array())
	{
		$qObject = new QueryObject();
		
		//Pegando todos os relatórios pendentes dos orientandos do professor corrente
		$sql = "SELECT
					p.id_pessoa,
					p.nome,
					r.aluno_data_previsto,
					DATE_FORMAT(r.aluno_data_previsto,'%d/%m/%Y') AS data_prevista_f,
					r.id_relatorio ";
		if(!empty($options['add_fields']))
			$sql .= ','.$options['add_fields'].' ';
		$sql .= "FROM
					relatorios r,
					ingressos i,
					pos_graduacoes pg,
					pessoas p,
					orientadores o ";
		if(!empty($options['add_tables']))
			$sql .= ','.$options['add_tables'].' ';
		$sql .=	"WHERE
					r.id_ingresso = i.id_ingresso AND
					i.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					o.id_ingresso = i.id_ingresso AND
					o.id_professor = '{$professorID}' AND
					p.deletado = 0 AND
					r.aluno_data_entrega = '0000-00-00 00:00:00' AND
					r.aluno_data_previsto < NOW() ";
		if(!empty($options['add_clauses']))
			$sql .= ' AND '.$options['add_clauses'].' ';

		$out = $qObject->query($sql,true);

		return $out;
	}
	
	public static function getPareceresPendenteOrientador($professorID = '', $options = array())
	{
		$qObject = new QueryObject();
		
		//Pegando todos os pareceres pendentes dos orientandos do professor corrente
		$sql = "SELECT
					p.id_pessoa,
					p.nome,
					r.aluno_data_previsto,
					DATE_FORMAT(r.aluno_data_previsto,'%d/%m/%Y') AS data_prevista_f,
					r.id_relatorio ";
		if(!empty($options['add_fields']))
			$sql .= ','.$options['add_fields'].' ';
		$sql .= "FROM
					relatorios r,
					ingressos i,
					pos_graduacoes pg,
					pessoas p,
					orientadores o ";
		if(!empty($options['add_tables']))
			$sql .= ','.$options['add_tables'].' ';
		$sql .=	"WHERE
					o.id_professor = '{$professorID}' AND
					r.id_ingresso = i.id_ingresso AND
					i.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					o.id_ingresso = i.id_ingresso AND
					o.atual = 1 AND
					r.parecer = '' AND
					p.deletado = 0 AND
					r.aluno_data_entrega = '0000-00-00 00:00:00' AND
					r.aluno_data_previsto < NOW() ";
		if(!empty($options['add_clauses']))
			$sql .= ' AND '.$options['add_clauses'].' ';

		$out = $qObject->query($sql,true);

		return $out;
	}

	public static function getAlunos($alunoID = '')
	{
		$qObject = new QueryObject();
		
		//Pegando a pessoa requisitada, ou caso $alunoID não seja especificado, pega todo mundo
		$sql = "SELECT
					p.id_pessoa AS id_pessoa,
					p.nome AS nome,
					IF(p.sexo = 1,'M','F') AS sexo,
					p.email_principal AS email_principal,
					p.email_secundario AS email_secundario,
					c.id_cidade AS cidade,
					i.ra AS ra
				FROM
					pessoas p,
					cidades c,
					pos_graduacoes pg,
					ingressos i
				WHERE
					p.id_pessoa = pg.id_pessoa AND
					pg.id_pos_graduacao = i.id_pos_graduacao AND
					c.id_cidade = p.id_local_nascimento ";
		//Especificando apenas 1 pessoa
		if ($alunoID != '')
			$sql .= " AND p.id_pessoa = '{$alunoID}' ";
	
		//ordenando
		$sql .= " ORDER BY `p`.`id_pessoa`"; 

		$out = $qObject->query($sql,true);
		
		return $out;
	}
	
	public static function getAlunosDoProfessor($idProfessor,$restrictMore = '')
	{
		$qObject = new QueryObject();
				
		//Pegando todos os orientandos do professor corrente 
		$sql = "SELECT
					p.id_pessoa,
					p.nome
				FROM
					orientadores o,
					ingressos i,
					pos_graduacoes pg,
					pessoas p
				WHERE
					p.id_pessoa = pg.id_pessoa AND
					pg.id_pos_graduacao = i.id_pos_graduacao AND
					i.id_ingresso = o.id_ingresso AND
					p.deletado = 0 AND
					o.atual = 1 AND
					o.id_professor = '$idProfessor' ";
		
		$sql .= $restrictMore;

		$out = $qObject->query($sql,true);
		
		return $out;
	
	}
	
	public static function getOrientadoresDoProfessor($restrictMore = '')
	{
		$qObject = new QueryObject();
		
		
		//Pegando a pessoa requisitada, ou caso $alunoID não seja especificado, pega todo mundo 
		$sql = "SELECT
					p.id_pessoa AS id_professor_pessoa,
					p.nome AS nome_professor
					
				FROM
					orientadores o,
					pessoas p,
					professores pr,
					ingressos i,
					pos_graduacoes pg
					
				WHERE
					o.id_professor = pr.id_professor AND
					pr.id_pessoa = p.id_pessoa AND
					o.id_ingresso = i.id_ingresso AND
					i.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = '{$_SESSION['user']['id_pessoa']}' AND
					p.deletado = 0 ";
		$sql .= $restrictMore;
		
		$out = $qObject->query($sql,true);
		
		return $out;
	
	}
	
	public static function getDisciplinasProfessoresOrdenados($professorID = '')
	{
		return IQ_Relatorio::getDisciplinasProfessores($professorID, "ORDER BY ano DESC, periodo ASC, turma ASC, codigo ASC");
	}
	
	public static function getDisciplinasProfessores($professorID = '', $restrictMore = '')
	{
		if ($professorID == '')
			$professorID = $_SESSION['user']['id_professor'];
			
		$qObject = new QueryObject();
		
		//Pegando as disciplinas do professor corrente
		$sql = "SELECT
					d.id_disciplina,
					d.codigo,
					d.titulo,
					d.subtitulo,
					t.ano,
					t.horario,
					t.turma,
					od.oferecimento_disciplina,
					p.periodo,
					IF(t.coordenador = dt.id_docente,1,0) AS coordenador

				FROM
					disciplinas d,
					turmas t,
					docente_turmas dt,
					oferecimentos_disciplinas od,
					periodos p

				WHERE
					d.id_disciplina = t.materia AND
					dt.id_turma = t.id_turma AND
					dt.id_docente = '{$professorID}' AND
					p.id_periodo = t.periodo AND
					od.id_oferecimento_disciplina = d.oferecimento AND
					YEAR(NOW()) = ano AND
					od.deletado = 0 AND
					dt.deletado = 0 AND
					d.deletado = 0 AND
					t.deletado = 0 ";
		$sql .= $restrictMore;
		
		$out = $qObject->query($sql,true, array('force_array'=>true));
		
		/*foreach($out as $key => $value)
		{
			$arr[$value['id_disciplina']]['titulo'] = $value['titulo'];
			$arr[$value['id_disciplina']][] = $value;
		}*/

		return $out;
	}
	
	public static function getAlunosDisciplina($id_disciplina, $periodo = '', $turma = '', $ano = '')
	{
		$qObject = new QueryObject();
		
		//Pegando os alunos da disciplina requisitada
		$sql = "SELECT id_pessoa, nome, especial, conceito FROM v_rel_posgraduandos_disciplinas WHERE id_disciplina = '{$id_disciplina}' ";
		if ($periodo) $sql .= "AND periodo = '{$periodo}' ";
		if ($turma) $sql .= "AND turma = '{$turma}' ";
		if ($ano) $sql .= "AND ano = '{$ano}' ";
		
		$out = $qObject->query($sql,true);
		
		return $out;
		
	}
	
	public static function getBolsasProfessorSemParecer($professorID = '', $bolsaID = '')
	{
		return IQ_Relatorio::getBolsasProfessor($professorID, $bolsaID, "AND b.processo = '' ");
	}
	
	public static function getBolsasProfessorComParecer($professorID = '', $bolsaID = '')
	{
		return IQ_Relatorio::getBolsasProfessor($professorID, $bolsaID, " AND b.processo != '' ");
	}
	
	public static function getBolsasProfessor($professorID = '', $bolsaID = '', $restrictMore = '')
	{
		if ($professorID == '')
			$professorID = $_SESSION['user']['id_professor'];
	
		$qObject = new QueryObject();
		
		//Pegando as bolsas do professor corrente
		$sql = "SELECT
					p.id_pessoa,
					p.nome,
					b.id_bolsa,
					DATE_FORMAT(b.data_inicio,'%d/%m/%Y') AS data_inicio,
					DATE_FORMAT(b.data_fim,'%d/%m/%Y') AS data_fim,
					b.processo,
					b.observacao,
					ag.agencia
					
				FROM
					bolsas b,
					pessoas p,
					pos_graduacoes pg,
					agencias ag
					
				WHERE
					ag.id_agencia = b.id_agencia AND
					b.id_posgraduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					b.deletado = 0 AND
					p.deletado = 0 AND
					cancelada = 0 AND
					b.id_professor = '{$professorID}' ";
		
		if ($bolsaID != '') $sql .= "AND b.id_bolsa = '{$bolsaID}' ";
		$sql .= $restrictMore;

		$out = $qObject->query($sql,true,array('force_array' => true));
		
		return $out;
	}
	
	public static function getBancasProfessor($professorID = '')
	{
		if ($professorID == '')
			$professorID = $_SESSION['user']['id_professor'];
	
		$qObject = new QueryObject();
		
		//Pegando as bancas do professor corrente
		$sql = "SELECT
					YEAR(str_to_date(d.data_defesa,'%d/%m/%Y')) AS ano,
					d.data_defesa AS `data`,
					d.id_defesa AS id,
					p.nome,
					p.id_pessoa,
					a.atribuicao,
					'defesa' as tipo
					
				FROM
					defesa_banca db,
					defesa d,
					pos_graduacoes pg,
					pessoas p,
					v_atribuicoes a
					
				WHERE
					db.id_professor = '{$professorID}' AND
					d.data_defesa < NOW() AND
					db.id_defesa = d.id_defesa AND
					d.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					p.deletado = 0 AND
					db.id_atribuicao = a.id
					
				
				UNION
				
				SELECT
					YEAR(q.`data`) AS ano,
					DATE_FORMAT(q.data,'%d/%m/%Y'),
					q.id_qualificacoes,
					p.nome,
					p.id_pessoa,
					a.atribuicao,
					'qualificação' as tipo
					
				FROM
					pg_qualificacoes q,
					pos_graduacoes pg,
					pessoas p,
					pg_bancas_qualificacoes bq,
					v_atribuicoes a
					
				WHERE
					bq.id_professor = '{$professorID}' AND
					q.data < NOW() AND
					bq.id_qualificacao = q.id_qualificacoes AND
					q.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					p.deletado = 0 AND
					bq.id_atribuicao = a.id
					
				ORDER BY
					ano DESC,
					nome ASC";
		
		$out = $qObject->query($sql,true,array('force_array' => true));

		return $out;
	}
	
	public static function ordenaBancas($bancas)
	{
		$out = array();
		//percorre o vetor de bancas agrupando as bancas de mesmo ano
		foreach($bancas as $banca)
		{
			$ano = $banca['ano'];
			$out[$ano][] = $banca;
		}

		return $out;
	}
	
	public static function getParecerPendente($idProfessor)
	{
		$qObject = new QueryObject();
		
		//Pegando todos os pareceres pendentes do usuario corrente
		$sql = "SELECT
					p.id_pessoa,
					p.nome,
					r.aluno_data_previsto,
					r.aluno_data_entrega,
					DATE_FORMAT(r.aluno_data_entrega,'%d/%m/%Y') AS data_entrega_f,
					DATE_FORMAT(r.aluno_data_previsto,'%d/%m/%Y') AS data_prevista_f,
					r.id_relatorio ";
					
		$sql .=	"FROM
					relatorios r,
					ingressos i,
					pos_graduacoes pg,
					pessoas p,
					orientadores o ";
					
		$sql .=	"WHERE
					r.id_ingresso = i.id_ingresso AND
					i.id_pos_graduacao = pg.id_pos_graduacao AND
					pg.id_pessoa = p.id_pessoa AND
					o.id_ingresso NOT IN (SELECT j.id_ingresso FROM ingressos j, orientadores ori WHERE j.id_ingresso = ori.id_ingresso AND ori.atual = 1 AND ori.id_professor = '{$idProfessor}')AND
					p.deletado = 0 AND
					r.aluno_data_entrega != '0000-00-00 00:00:00' AND
					r.parecer = ''";
		$out = $qObject->query($sql,true);
		
		return $out;
	}
}