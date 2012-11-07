<h1 class="header">Hist�rico de Relat�rios</h1>
<!-- Relat�rios ainda n�o entregues -->
<h2>Relat�rios N�o Entregues</h2>
<p class="margin-15"></p>
<?php $relatorios = IQ_Relatorio::getRelatoriosNaoEntregues();
if (empty($relatorios)){?>
<h3>N�o h� relat�rios n�o entregues</h3>
<p class="margin-12"></p>
<?php }else{
foreach($relatorios as $relatorio):
	if (strtotime($relatorio['aluno_data_previsto']) <= strtotime(date('d-m-Y'))) 
		$span = "<span class=\"red\">";
	else
		$span = "<span>";
	?>
	<h3>Relat�rio de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($relatorio['id_pessoa'])?>" title="Perfil do Aluno"> 
	<?php echo $relatorio['nome']?></a>, data prevista <?php echo $span.$relatorio['data_prevista']."</span>" ?> <a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relat�rio completo">[veja mais]</a>
	</h3>
	<p class="margin-12"></p>
<?php endforeach;
}//end-else?>
<p class="margin-15"></p>
<!-- Relat�rios entregues -->
<h2>Relat�rios Entregues</h2>
<p class="margin-15"></p>
<?php $relatorios = IQ_Relatorio::getRelatoriosEntregues();
if (empty($relatorios)){?>
<h3>N�o h� relat�rios entregues</h3>
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
	<h2><?php echo $ano['ano'] ?></h2>
	<p class="margin-12"></p>
	<?php foreach($ano['relatorios'] as $relatorio) {?>
		<h3>&nbsp;Relat�rio de <a href="<?php echo Config::HOMEURL."?q=".Config::PROFILES."&amp;u=".base64_encode($relatorio['id_pessoa'])?>" title="Perfil do Aluno"> 
		<?php echo $relatorio['nome']?></a>, entregue em <?php echo $relatorio['data_entrega'] ?> <a href="<?php echo Config::HOMEURL."?q=".Config::UMRELATORIO."&amp;r=".base64_encode($relatorio['id_relatorio'])?>" title="Ver relat�rio completo">[veja mais]</a>
		</h3>
		<p class="margin-12"></p>
	<?php }?>
<?php endforeach;
}//end-else?>
<p class="margin-15"></p>
