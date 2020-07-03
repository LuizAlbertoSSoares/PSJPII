<?php
require_once('busConsistirPemissao.php');
include "Conn.php";

echo("<!doctype html>");
echo("<html lang='en'><head><title></title>");
echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
echo("<style>");
echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
echo(".tdCaixa		{BACKGROUND-COLOR:#F8F8F8;}");
echo("</style>");
echo("</head><body><form>");
echo("<table class='tbCaixa'>");
echo("<tr><td>Selecione o arquivo para exibir detalhe...</td></tr>");
echo("<tr><td class='tdCaixa'>");

echo("<table class='tbCaixa'>");
echo("<tr><td></td></tr>");

$sqlArquivo = "SELECT * FROM sce_remessa ORDER BY REMESSA_ID DESC LIMIT 25";
$exeArquivo = mysql_query($sqlArquivo, $conn) or die(mysql_error());
$rowArquivo = mysql_num_rows($exeArquivo);

echo("<thead>");
echo("<th>ID</td>");
echo("<th align='left'>Remessa</td>");
echo("<th align='left'>Data</td>");
echo("<th align='left'>Nome</td>");
echo("<th align='right'>Registros</td>");
echo("<th align='right'>Valor</td>");
echo("<th align='center'>Detalhe</td>");
echo("</thead>");

$i = 0;
while ($rowArquivo = mysql_fetch_assoc($exeArquivo))
{
    $i += 1;
	$cor1 = 'cor1';
	$cor2 = 'cor2';
	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
	echo("<tr  class='". $cor ."'>");
	echo("<td>".$rowArquivo['REMESSA_ID']."</td>");
	echo("<td>".$rowArquivo['SEQUENCIAL']."</td>");
	$data = substr($rowArquivo['REMESSA_DATA'],8,2)
	   ."/".substr($rowArquivo['REMESSA_DATA'],5,2)
	   ."/".substr($rowArquivo['REMESSA_DATA'],0,4);
	echo("<td>".$data."</td>");
	echo("<td>".$rowArquivo['NOME_ARQ']."</td>");
	echo("<td align='right'>".$rowArquivo['TOTAL_REGISTROS']."</td>");
	echo("<td align='right'>".$rowArquivo['VALOR_TOTAL_REGISTROS']."</td>");
	echo("<td id = '".$rowArquivo['NOME_ARQ']."' align='center'><img src='img/btn_detalhe.gif' width='10'></td>");
	echo("</tr>");
}
echo("</table>");
echo("</form></body></html>");

?>














