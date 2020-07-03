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
echo("<table class='tbCaixa'><thead>");
echo("<th align='left'  width='85%'>PARCELAS EM ATRASO</th>");
echo("<th align='right' width='15%'>".$dia."/".$mes."/".$ano."  ".$horaMinuto."</th>");
echo("</thead></table>");
echo "<hr>";
echo "<br>";	

$sql = "SELECT CTB_ID, CTB_NOME, CTB_EMAIL, ctb_fone_residencial, CTB_FONE_COMERCIAL, CTB_FONE_MOBILE, TIT_QUO_ID, TIT_ANO_MES_DOCUMENTO, TIT_VALOR_TITULO 
FROM sce_titulo T, sce_contribuinte C 
WHERE TIT_VALOR_RECEBIDO = 0 AND TIT_ANO_MES_DOCUMENTO <= ".$ano.$mes." AND TIT_EVN_ID = ".$globalEvento. " AND CTB_ID = TIT_CLN_ID ";

	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo("<table class='tbCaixa'>");
	echo("<tr><td></td></tr>");
	echo("<thead>");
	echo("<th align='left'>Contribuinte</td>");	
	echo("<th align='left'>Quota</td>");	
	echo("<th align='left'>Parcela</td>");
	echo("<th align='right'>Valor</td>");
	echo("<th align='left'>Fone Residencial</td>");	
	echo("<th align='left'>Fone Comercial</td>");	
	echo("<th align='left'>Fone Celular</td>");	
	echo("<th align='left'>Email</td>");	
	echo("</thead>");
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;
		$cor1 = 'cor1';
		$cor2 = 'cor2';
		( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo("<tr class='". $cor ."'>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("<td align='left'>".$row['TIT_QUO_ID']."</td>");
		echo("<td align='left'>".$row['TIT_ANO_MES_DOCUMENTO']."</td>");
		echo("<td align='right'>".$row['TIT_VALOR_TITULO']."</td>");
		echo("<td align='left'>".$row['ctb_fone_residencial']."</td>");
		echo("<td align='left'>".$row['CTB_FONE_COMERCIAL']."</td>");
		echo("<td align='left'>".$row['CTB_FONE_MOBILE']."</td>");
		echo("<td align='left'>".$row['CTB_EMAIL']."</td>");		
		echo("</tr>");
	}
	echo("</table>");
	echo("</form></body></html>");
	exit;

?>
