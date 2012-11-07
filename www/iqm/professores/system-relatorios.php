<div class="header">
	<p class="spacer"></p>
	<h1>Histórico de Relatórios</h1>
</div>
<!-- Relatórios ainda não entregues -->
<h2>Relatórios Não Entregues</h2>
<p class="relDescription">Este relatório mostra os relatórios não entregues por seus orientandos</p>
<?php $relatorios = IQ_Relatorio::getRelatoriosNaoEntregues();
if (empty($relatorios)){?>
<h3>Não há registro de relatórios pendentes</h3>
<p class="margin-12"></p>
<?php }else{
foreach($relatorios as $relatorio):
	if (strtotime($relatorio['aluno_data_previsto']) <= strtotime(date('d-m-Y'))) 
		$span = "<span class=\"red\">";
	else
		$span = "<span>";
	?>
	<h3>Relatório de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($relatorio['id_pessoa'])?>" title="Perfil do Aluno"> 
	<?php echo $relatorio['nome']?></a>, data prevista <?php echo $span.$relatorio['data_prevista']."</span>" ?> <a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relatório completo">[veja mais]</a>
	</h3>
	<p class="margin-12"></p>
<?php endforeach;
}//end-else?>
<p class="margin-15"></p>
<!-- Relatórios entregues -->
<h2>Relatórios Entregues</h2>
<p class="relDescription">Este relatório mostra os relatórios já entregues em ordem cronológica. É possível ver os detalhes do relatorio clicando em [veja mais]</p>
<?php $relatorios = IQ_Relatorio::getRelatoriosEntregues();
if (empty($relatorios)){?>
<h3>Não há registro de relatórios entregues</h3>
<p class="margin-12"></p>
<?php }else{
$ordenados = array();
$prevAno = StringUtil::ano($relatorios[0]['data_entrega']);

foreach($relatorios as $relatorio)
{
	if ($prevAno != StringUtil::ano($relatorio['data_entrega'])){
		$ordenados[] = array('relatorios' => $aux, 'ano' => $prevAno);
		$aux = array();
		$prevAno = StringUtil::ano($relatorio['data_entrega']);
	}
	$aux[] = $relatorio;
}

if (!empty($aux))
	$ordenados[] = array('relatorios' => $aux, 'ano' => $prevAno);


foreach($ordenados as $ano):?>
	<h2 class='ano'><?php echo $ano['ano'] ?></h2>
	<p class="margin-12"></p>
	<?php foreach($ano['relatorios'] as $relatorio) {?>
		<h3>&nbsp;Relatório de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($relatorio['id_pessoa'])?>" title="Perfil do Aluno"> 
		<?php echo $relatorio['nome']?></a>, entregue em <?php echo $relatorio['data_entrega'] ?> <a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relatório completo">[veja mais]</a>
		</h3>
		<p class="margin-12"></p>
	<?php }?>
<?php endforeach;
}//end-else?>
<p class="margin-15"></p>
