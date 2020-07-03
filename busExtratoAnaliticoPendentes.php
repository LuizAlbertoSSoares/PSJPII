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

$sql = "SELECT TIT_PARCELA AS ParcelaNumero, COUNT(1) AS TotalParcelas, SUM(TIT_VALOR_TITULO) AS ValorTotal, SUM(TIT_VALOR_RECEBIDO) AS ValorPago, SUM(TIT_VALOR_JUROS_MULTA) AS Multa, SUM(TIT_VALOR_DESCONTOS) AS Descontos FROM sce_titulo GROUP BY TIT_PARCELA";
$exe = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exe);

echo("<table class='tbCaixa'><thead>");
echo("<th align='left'  width='85%'>EXTRATO ANALÍTICO - PENDENTES</th>");
echo("<th align='right' width='15%'>".$dia."/".$mes."/".$ano."  ".$horaMinuto."</th>");
echo("</thead></table>");
echo "<hr>";
echo "<br>";	

echo("<table  id='tbConsultarQuota' class='tbCaixa'>");
echo("<thead>");
echo("<th align='right'>Parcela</th>");
echo("<th align='right'>Valor Total</th>");
echo("<th align='right'>Valor Recebido</th>");
echo("<th align='right'>Valor Pendente</th>");
echo("</thead>");
$valorTotalTarifas = 0;
$valorTotalRecebido = 0;
$valorTotalLiquido = 0;
$i = 0;
while ($row = mysql_fetch_assoc($exe)){
    $i += 1;
	$cor1 = 'cor1';
	$cor2 = 'cor2';
	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
	echo("<tr class='". $cor ."'>");
	$parcela = $row['ParcelaNumero'];
	if ($parcela == '00'){$parcela = 'Única';};
	echo("<td align='right'>".$parcela."</td>");
	echo("<td align='right'>".$row['ValorTotal']." (".str_pad($row['TotalParcelas'],4,"0", STR_PAD_LEFT).")</td>");
	$valorPago = $row['ValorPago'] + $row['Descontos'];
	$totalPago = $valorPago / $globalValorDaParcela;
	if ($parcela == 'Única'){$totalPago = $valorPago / $globalValorDaParcelaUnica;};
	echo("<td align='right'>".sprintf("%01.2f",$valorPago)." (".str_pad($totalPago,4,"0", STR_PAD_LEFT).")</td>");
	$valorPendente = $row['ValorTotal'] - $valorPago;
	$totalPendente = $valorPendente / $globalValorDaParcela;
	if ($parcela == 'Única'){$totalPendente = $valorPendente / $globalValorDaParcelaUnica;};	
	echo("<td align='right'>".sprintf("%01.2f",$valorPendente)." (".str_pad($totalPendente,4,"0", STR_PAD_LEFT).")</td>");
	echo("</tr>");
	$valorTotalTarifas += $row['Descontos'];
	$valorTotalRecebido += $valorPago;
	$valorTotalLiquido = $valorTotalRecebido - $valorTotalTarifas;
}
echo("</table>");
echo "Valor Recebido = ".sprintf("%01.2f",$valorTotalRecebido)."<br>";
echo "Valor Tarifas.  &nbsp&nbsp = ".sprintf("%01.2f",$valorTotalTarifas)."<br>";
echo "Valor Líquido &nbsp&nbsp = ".sprintf("%01.2f",$valorTotalLiquido);
echo("</form></body></html>");
?>

