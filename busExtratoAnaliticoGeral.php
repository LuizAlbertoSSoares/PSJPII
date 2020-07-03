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
$numeroCampanha = $globalEvento;

//***** PARCELAS PAGAS DE 01 A 12
$sql = ""
."SELECT ParcelasPagas, SUM(1) Quotas, (ParcelasPagas * SUM(1)) * 50 as ValorRecebido "
."FROM ( "
."	SELECT TIT_QUO_ID, SUM(1) ParcelasPagas "
."	FROM sce_titulo "
."	WHERE TIT_EVN_ID = ".$numeroCampanha
."	  AND TIT_VALOR_RECEBIDO > 1 "
."	  AND TIT_VALOR_RECEBIDO < 51 "
."	  GROUP BY TIT_QUO_ID ORDER BY ParcelasPagas "
.") Parcela "
."GROUP BY ParcelasPagas";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
$totalDeParcelasPagas = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
$valorDeParcelasPagas = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
while ($row = mysql_fetch_assoc($exec))
{
 $totalDeParcelasPagas[sprintf("%00.0f",$row['ParcelasPagas'])] = $row['Quotas'];
 $valorDeParcelasPagas[sprintf("%00.0f",$row['ParcelasPagas'])] = $row['ValorRecebido'];
}
//***** PARCELAS PAGAS UNICA
$sql = ""
."SELECT ParcelasPagas, SUM(1) Quotas, (ParcelasPagas * SUM(1)) * 540 as ValorRecebido "
."FROM ( "
."	SELECT TIT_QUO_ID, SUM(1) ParcelasPagas "
."	FROM sce_titulo "
."	WHERE TIT_EVN_ID = ".$numeroCampanha
."	  AND TIT_VALOR_RECEBIDO > 50 "
."	  GROUP BY TIT_QUO_ID ORDER BY ParcelasPagas "
.") Parcela "
."GROUP BY ParcelasPagas ";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
$totalDeParcelaUnicaPagas = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
$valorDeParcelaUnicaPagas = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
while ($row = mysql_fetch_assoc($exec))
{
 $totalDeParcelasPagas[0] = $row['Quotas'];
 $valorDeParcelasPagas[0] = $row['ValorRecebido'];
}

//***** PARCELAS PENDENTES DE 01 A 12
$sql = ""
."SELECT ParcelasPendentes, SUM(1) Quotas, (ParcelasPendentes * SUM(1)) * 55 as ValorPendente "
."FROM ( "
."	SELECT TIT_QUO_ID, SUM(1) ParcelasPendentes "
."	FROM sce_titulo "
."	WHERE TIT_EVN_ID = ".$numeroCampanha
."	  AND TIT_VALOR_RECEBIDO = 0 "
."	  AND TIT_VALOR_TITULO < 51 "
."	  GROUP BY TIT_QUO_ID ORDER BY ParcelasPendentes "
.") Parcela "
."GROUP BY ParcelasPendentes";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
$totalDeParcelasPendentes = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
$valorDeParcelasPendentes = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
while ($row = mysql_fetch_assoc($exec)){
 $totalDeParcelasPendentes[sprintf("%00.0f",$row['ParcelasPendentes'])] = $row['Quotas'];
 $valorDeParcelasPendentes[sprintf("%00.0f",$row['ParcelasPendentes'])] = $row['ValorPendente'];
}

//***** PARCELAS PENDENTES UNICA
$sql = ""
."SELECT ParcelasPendentes, SUM(1) Quotas, (ParcelasPendentes * SUM(1)) * 540 as ValorPendente "
."FROM ( "
."	SELECT TIT_QUO_ID, SUM(1) ParcelasPendentes "
."	FROM sce_titulo "
."	WHERE TIT_EVN_ID = ".$numeroCampanha
."	  AND TIT_VALOR_RECEBIDO = 0 "
."	  AND TIT_VALOR_TITULO > 51 "
."	  GROUP BY TIT_QUO_ID ORDER BY ParcelasPendentes "
.") Parcela "
."GROUP BY ParcelasPendentes";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec))
{
 $totalDeParcelasPendentes[0] = $row['Quotas'];
 $valorDeParcelasPendentes[0] = $row['ValorPendente'];
}

echo("<div id='divTitulo'><h4>EXTRATO ANALITICO - GERAL em ".$dia." de ".$mesExtenso." de ".$ano."</h4></div>");
echo("<table class='tbConsulta' align='center' border=1>");
echo("<thead>");
echo("<tr><th align='right'>Parcelas</th>");
echo("<th align='right'>Pagas</th>");
echo("<th align='right'>Soma Pagas</th>");
echo("<th align='right'>Pendentes</th>");
echo("<th align='right'>Soma Pendentes</th>");
echo("</tr>");
echo("</thead>");

for ( $i = 0; $i <= 12; $i += 1) {
	
	echo("<tr>");
	
	if ($i == 0){echo "<td class='tdLista' align='right'>UNICA</td>";};
	if ($i != 0){echo "<td class='tdLista' align='right'>".$i."</td>";};

	echo "<td class='tdLista' align='right'>".$totalDeParcelasPagas[$i]."</td>";
	echo "<td class='tdLista' align='right'>".number_format($valorDeParcelasPagas[$i],'2',',','.')."</td>";

	echo "<td class='tdLista' align='right'>".$totalDeParcelasPendentes[$i]."</td>";
	echo "<td class='tdLista' align='right'>".number_format($valorDeParcelasPendentes[$i],'2',',','.')."</td>";
	
	echo("</tr>");
}
echo("</table>");

?>

