<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i')."00";
$horaMinuto     = date('H').":".date('i');
echo("<!doctype html>");
echo("<html lang='en'><head><title></title>");
echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
echo("<style>");
echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
echo(".tdCaixa		{BACKGROUND-COLOR:#F8F8F8;}");
echo("</style>");
echo("</head><body><form>");

$TextoConsulta = $_POST['TextoConsulta'];
$Campanha = $globalEvento;

if (substr($TextoConsulta,6,2) != ""){$Campanha = substr($TextoConsulta,6,2);};
if ($Campanha == ""){$Campanha = $globalEvento;};
$wTextoConsulta = substr($TextoConsulta,0,5);
$TextoConsulta = $wTextoConsulta;

if ($_POST['Consulta'] == "consultarQuotasAssociadasPorPeriodo"){
	$dataInicio    = $_POST['DataInicio']; if ($dataInicio == ''){$dataInicio = $dia."/".$mes."/".$ano;};
	$dataFim       = $_POST['DataFim'];    if ($dataFim    == ''){$dataFim    = $dataInicio;};
	$dataInicioINV = substr($dataInicio,6,4)."-".substr($dataInicio,3,2)."-".substr($dataInicio,0,2);
	$dataFimINV    = substr($dataFim,6,4)."-".substr($dataFim,3,2)."-".substr($dataFim,0,2);
	echo("<table class='tbCaixa'><thead>");
	echo("<th align='left'  width='85%'>QUOTAS ASSOCIADAS NO PERIODO (".$dataInicio." a ".$dataFim.")</th>");
	echo("<th align='right' width='15%'>".$dia."/".$mes."/".$ano."  ".$horaMinuto."</th>");
	echo("</thead></table>");
	echo "<hr>";
	echo "<br>";	
	
	$totalGeralQuotas = 0;	
	
# IDENTIFICAR BOLETO	
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>BOLETO</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'B' "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br>";
# IDENTIFICAR DEBITOS BANCO DO BRASIL
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>BANCO DO BRASIL</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'D' "
	." AND CTB_ID_BANCO = 1 "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br>";
# IDENTIFICAR DEBITOS BRB
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>BRB</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'D' "
	." AND CTB_ID_BANCO = 2 "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br>";
# IDENTIFICAR DEBITOS CAIXA ECONOMICA
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>CAIXA ECONOMICA</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'D' "
	." AND CTB_ID_BANCO = 3 "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br>";	
# IDENTIFICAR DEBITOS BRADESCO
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>BRADESCO</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'D' "
	." AND CTB_ID_BANCO = 4 "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br>";	
# IDENTIFICAR DEBITOS ITAU
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>ITAÚ</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'D' "
	." AND CTB_ID_BANCO = 5 "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br>";	
# IDENTIFICAR CARTAÕ DE CRÉDITO/DÉBITO
	echo "<table class='tbCaixa'><thead><th align='left'  width='85%'>CARTÃO DE CRÉDITO/DÉBITO</th></thead></table>";	
	$sql = "SELECT CTB_NOME, QUO_ID_QUOTA, QUO_TOTAL_PARCELAS, QUO_FORMA_PAGAMENTO, QUO_ID_CONTRIBUINTE, QUO_DATA_ENTREGA, CTB_ID_BANCO "
	." FROM sce_quota "
	." INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE "
	." WHERE QUO_DATA_ENTREGA >= '".$dataInicioINV."' AND QUO_DATA_ENTREGA <= '".$dataFimINV."'"
	." AND QUO_FORMA_PAGAMENTO = 'C' "
	." ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Data Associação</td>");	
	echo("<th align='right'>Quota</td>");
	echo("<th align='right'>Parcelas</td>");
	echo("<th align='right'>Contribuinte</td>");
	echo("<th align='left'>Nome</td>");
	echo("</thead>");
	$totalQuotas = 0;
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		$data = substr($row['QUO_DATA_ENTREGA'],8,2)."/".substr($row['QUO_DATA_ENTREGA'],5,2)."/".substr($row['QUO_DATA_ENTREGA'],0,4);
		echo("<td id='data' align='left'>".$data."</td>");		
		echo("<td align='right'>".$row['QUO_ID_QUOTA']."</td>");
		echo("<td align='right'>".$row['QUO_TOTAL_PARCELAS']."</td>");
		echo("<td align='right'>".$row['QUO_ID_CONTRIBUINTE']."</td>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("</tr>");
		$totalQuotas += 1;
		$totalGeralQuotas += 1;
	}
	echo("</table>");
	echo "<hr>";
	echo str_pad($totalQuotas,4,"0", STR_PAD_LEFT)." Quotas<br><br><br><br>";	
	
	echo "<b>TOTAL DE QUOTAS ASSOCIADAS: </b>".str_pad($totalGeralQuotas,4,"0", STR_PAD_LEFT);	
	echo("</form></body></html>");
	exit;
}

if ($_POST['Consulta'] == "consultarQuotasNaoAssociadas"){
	echo("<table class='tbCaixa'><thead>");
	echo("<th align='left'  width='85%'>QUOTAS NÃO ASSOCIADAS</th>");
	echo("<th align='right' width='15%'>".$dia."/".$mes."/".$ano."  ".$horaMinuto."</th>");
	echo("</thead></table>");
	echo "<hr>";
	echo "<br>";	

	$sql = "SELECT QUO_ID_QUOTA FROM sce_quota ORDER BY QUO_ID_QUOTA ASC";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='right'>Quota</td>");
	echo("</thead>");
	$quotasDaCampanha = 0;
	$totalQuotas = 0;
	$i = 0;
	
	while ($quotasDaCampanha < 2000){
		$quotasDaCampanha += 1;
		$sql = "SELECT QUO_ID_QUOTA FROM sce_quota WHERE QUO_ID_QUOTA = ".$quotasDaCampanha;
		$exe =  mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_num_rows($exe);
		if ($row == 0){
			$i += 1;
			$cor1 = 'cor1';
			$cor2 = 'cor2';
			( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
			echo("<tr class='". $cor ."'>");
			echo("<td align='left'>".$quotasDaCampanha."</td>");
			echo("</tr>");
			$totalQuotas += 1;			
		}
	}
	echo("</table>");
	echo "<hr>";
	echo $totalQuotas." Quotas não associadas a contribuinte";
	echo("</form></body></html>");
	exit;
}

# Consultar quota
echo("<table class='tbCaixa'>");
echo("<tr><td></td></tr>");

$sql = ""
#."SELECT "
#."  QUO_ID_QUOTA AS Quota "
#." ,QUO_DATA_ENTREGA AS DataEntrega "
#." ,QUO_TOTAL_PARCELAS AS TotalParcelas "
#." ,QUO_ID_CONTRIBUINTE AS Contribuinte "
#." ,QUO_FORMA_PAGAMENTO AS FormaPagamento "
#." ,CTB_NOME AS Nome "
#." ,CTB_ID_BANCO AS Banco "
#."FROM sce_quota "
#."INNER JOIN sce_contribuinte ON CTB_ID = QUO_ID_CONTRIBUINTE WHERE QUO_ID_QUOTA = ".$TextoConsulta." AND QUO_ID_EVENTO = ".$Campanha;

."SELECT "
."  A.QUO_ID_QUOTA AS Quota "
." ,A.QUO_DATA_ENTREGA AS DataEntrega "
." ,A.QUO_TOTAL_PARCELAS AS TotalParcelas "
." ,A.QUO_ID_CONTRIBUINTE AS Contribuinte "
." ,A.QUO_FORMA_PAGAMENTO AS FormaPagamento "
." ,B.CTB_NOME AS Nome "
." ,B.CTB_ID_BANCO AS Banco "
."FROM sce_quota AS A , sce_contribuinte AS B "
."WHERE A.QUO_ID_CONTRIBUINTE = B.CTB_ID "
."AND QUO_ID_QUOTA = ".$TextoConsulta." AND QUO_ID_EVENTO = ".$Campanha;

$exec =  mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exec);

echo("<thead>");
echo("<th align='right'>Quota</td>");
echo("<th align='right'>Entrega</td>");
echo("<th align='right'>Parcelas</td>");
echo("<th align='right'>Contribuinte</td>");
echo("<th align='left'>Nome</td>");
echo("<th align='left'>Forma de Pagamento</td>");
echo("<th align='left'>Observação</td>");
echo("</thead>");

$i = 0;
while ($row = mysql_fetch_assoc($exec))
{
    $i += 1;
	$cor1 = 'cor1';
	$cor2 = 'cor2';
	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
	echo("<tr class='". $cor ."'>");
	echo("<td align='right'>".$row['Quota']."</td>");
	$data = substr($row['DataEntrega'],8,2)."/".substr($row['DataEntrega'],5,2)."/".substr($row['DataEntrega'],0,4);
	echo("<td align='right'>".$data."</td>");
	echo("<td align='right'>".$row['TotalParcelas']."</td>");
	echo("<td align='right'>".$row['Contribuinte']."</td>");
	echo("<td align='left'>".$row['Nome']."</td>");
	$formaPagamento = "Boleto";
	$bancoParaDebito = "";
	if ($row['FormaPagamento'] == "D"){
		$formaPagamento = "Débito em conta";
		if ($row['Banco'] == 1){$bancoParaDebito = " (BB - Banco do Brasil)";};
		if ($row['Banco'] == 2){$bancoParaDebito = " (BRB - Banco de Brasilia)";};
		if ($row['Banco'] == 3){$bancoParaDebito = " (CEF - Caixa Economica Federal)";};
		if ($row['Banco'] == 4){$bancoParaDebito = " (Bradesco)";};
		if ($row['Banco'] == 5){$bancoParaDebito = " (Itaú)";};
	};
	if ($row['FormaPagamento'] == "C"){
		$formaPagamento = "Cartão (crédito/débito)";
	};
	echo("<td align='left'>".$formaPagamento.$bancoParaDebito."</td>");
	$descricao = $row['Observacao'];
	echo("<td align='right'>".$descricao."</td>");
	echo("</tr>");
}
echo("</table>");
echo("<br>");
$sqlTitulos = ""
." SELECT * FROM sce_titulo "
." WHERE TIT_QUO_ID = ".$TextoConsulta." AND TIT_EVN_ID = ".$Campanha." ORDER BY TIT_PARCELA ASC";
$exeTitulos = mysql_query($sqlTitulos, $conn) or die(mysql_error());
$rowTitulos = mysql_num_rows($exeTitulos);

echo("<table  id='tbConsultarQuota' class='tbCaixa'>");
echo("<thead>");
echo("<th align='right'>Documento</th>");
echo("<th align='right'>Valor da Parcela</th>");
echo("<th align='left'>Situação</th>");
echo("<th align='right'>Valor Recebido</th>");
echo("<th align='right'>Tarifa</th>");
echo("<th align='right'>Data Pagamento</th>");
echo("<th align='LEFT'>Observações</th>");
echo("</thead>");
$i = 0;
while ($rowTitulos = mysql_fetch_assoc($exeTitulos)){
    $i += 1;
	$cor1 = 'cor1';
	$cor2 = 'cor2';
	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
	echo("<tr class='". $cor ."'>");
	echo("<td align='right'>".$rowTitulos['TIT_NUMERO_DOCUMENTO']."</td>");
	echo("<td align='right'>".$rowTitulos['TIT_VALOR_TITULO']."</td>");
	$situacao = "Pendente";
	if ($rowTitulos['TIT_VALOR_RECEBIDO'] > 0){$situacao = "Liquidado";};
	echo("<td align='left'>".$situacao."</td>");
	$valor = $rowTitulos['TIT_VALOR_RECEBIDO'] + $rowTitulos['TIT_VALOR_DESCONTOS'];
	echo("<td align='right'>".sprintf("%01.2f",$valor)."</td>");
	echo("<td align='right'>".$rowTitulos['TIT_VALOR_DESCONTOS']."</td>");
	$data = substr($rowTitulos['TIT_DATA_COMPENSACAO'],8,2)."/".substr($rowTitulos['TIT_DATA_COMPENSACAO'],5,2)."/".substr($rowTitulos['TIT_DATA_COMPENSACAO'],0,4);
	if ($data == "//"){$data = "";};
	echo("<td id='data' align='right'>".$data."</td>");
	$observacao = $rowTitulos['TIT_OBS'];
	if ($rowTitulos['TIT_STATUS'] == 1){$observacao = "Aguardando retorno(".$rowTitulos['REMESSA_ID'].")";};
	if ($rowTitulos['TIT_VALOR_RECEBIDO'] > 0){$observacao = $rowTitulos['TIT_OBS'];};
	echo("<td align='left'>".$observacao."</td>");	
	echo("</tr>");
}
echo("</table>");

# IDENTIFICAR RETORNOS DA QUOTA PARA AUDITORIA NOS ARQUIVOS PROCESSADOS
echo "<hr><br><br><h3>Historico de Retorno</h3><hr>";
echo "<table class='tbCaixa'>";
echo "<thead>";
echo "<th align='left'>Arquivo</th>";
echo "<th align='left'>Parcela</th>";
echo "<th align='right'>Valor</th>";
echo "<th align='LEFT'>Observações</th>";
echo "</thead>";
$sql = "SELECT ARQ_NOME FROM sce_arquivo";
$exe = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exe);
while ($row = mysql_fetch_assoc($exe)){
	$arquivoOrigem = $row['ARQ_NOME']."-PROCESSADO";
	$arquivoRetorno = "uploads//".$arquivoOrigem;
	if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}
	if(file_exists($arquivoRetorno)) {
		$arquivo = fopen ($arquivoRetorno, "r");
		while (!feof($arquivo)){
			$linha = fgets($arquivo, 4096);
#---------- COBRANCA BB			
			if (substr($arquivoOrigem,0,6) == "IEDCBR"){
				if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === "T") {
					$quota = substr($linha,48-1,5);
					$Parcela    = substr($linha,53-1,2);
				}
				if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === "U") {
					if ($quota == $TextoConsulta){
						$Valor_Pago = substr($linha,78-1,15) / 100;
						$observacao = 'Pago';
						echo "<tr>";
						echo "<td align='left'>".$arquivoOrigem."</td>";
						echo "<td align='left'>".$Parcela."</td>";
						echo "<td align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>";
						echo "<td align='left'>".$observacao."</td>";
						echo "</tr>";
					}
				}
			}
#---------- DEBITO EM CONTA BB			
			if (substr($arquivoOrigem,0,6) == "DBT BB"){
				if (substr($linha,1-1,1) === "F") {
					$quota = substr($linha,5-1,5);
					if ($quota == $TextoConsulta){
						$Valor_Pago = substr($linha,53-1,15) / 100;
						$codigoRetorno = substr($linha,68-1,2);
						$observacao = 'Não Debitado';
						if ($codigoRetorno == '00'){$observacao = 'Pago';}
						echo "<tr>";
						echo "<td align='left'>".$arquivoOrigem."</td>";
						echo "<td align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>";
						echo "<td align='left'>".$observacao."</td>";
						echo "</tr>";
					}
				}
			}
#---------- DEBITO EM CONTA BRB
			if (substr($arquivoOrigem,0,7) == "DBT BRB"){
				$TipoDeRegistro = substr($linha,8-1,1);
				if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === "A") {
					$NossoNumero = substr($linha,84-1,17);
					$quota = substr($NossoNumero,4-1,5);
					$codigoRetorno = substr($linha,230-1,3);
					$observacao = 'Pago';
					if ($codigoRetorno != '000'){
						$observacao = 'Não Debitado';
						if ($quota == $TextoConsulta){
							echo "<tr>";
							echo "<td align='left'>".$arquivoOrigem."</td>";
							echo "<td align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>";
							echo "<td align='left'>".$observacao."</td>";
							echo "</tr>";
						}
					}
				}
				if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === "B") {
					if ($quota == $TextoConsulta){
						$Valor_Pago = substr($linha,136-1,15) / 100;
						echo "<tr>";
						echo "<td align='left'>".$arquivoOrigem."</td>";
						echo "<td align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>";
						echo "<td align='left'>".$observacao."</td>";
						echo "</tr>";
					}
				}
			}
#---------- DEBITO EM CONTA CAIXA ECONOMICA FEDERAL
			if (substr($arquivoOrigem,0,7) == "DBT CEF"){
				$TipoDeRegistro = substr($linha,1-1,1);
				if ($TipoDeRegistro === 'F'){
					$quota = substr($linha,73-1,5);
					$codigoRetorno = substr($linha,68-1,2);
					$observacao = 'Não Debitado';
					if ($codigoRetorno == '00'){$observacao = 'Pago';}
					if ($quota == $TextoConsulta){
						$Valor_Pago = substr($linha,53-1,15) / 100;
						echo "<tr>";
						echo "<td align='left'>".$arquivoOrigem."</td>";
						echo "<td align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>";
						echo "<td align='left'>".$observacao."</td>";
						echo "</tr>";
					}
				}
			}			
		}
		fclose($arquivo);
	}
}
echo "</table>";
echo("</form></body></html>");
?>
<script type='text/javascript'>
	$('table#tbConsultarQuota tr').hover( 
		function(){ $(this).addClass('destaque'); }, 
		function(){ $(this).removeClass('destaque'); } 
	);  
</script>

