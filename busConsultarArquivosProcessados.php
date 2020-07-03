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

$sqlArquivo = "SELECT * FROM sce_arquivo ORDER BY ARQ_ID DESC ";
$exesce_arquivo = mysql_query($sqlArquivo, $conn) or die(mysql_error());
$rowArquivo = mysql_num_rows($exesce_arquivo);

echo("<thead>");
echo("<th>ID</td>");
echo("<th>NSA</td>");
echo("<th align='left'>Processado</td>");
echo("<th align='left'>Nome</td>");
echo("<th align='right'>Registros</td>");
echo("<th align='right'>Valor</td>");
echo("<th align='right'>Creditado</td>");
echo("<th align='right'>Desconto</td>");
echo("<th align='right'>Erros</td>");
echo("<th align='right'>Valor Erros</td>");
echo("<th align='center'>Detalhe</td>");
echo("</thead>");

$i = 0;
while ($rowArquivo = mysql_fetch_assoc($exesce_arquivo))
{

    $i += 1;
	$cor1 = 'cor1';
	$cor2 = 'cor2';
	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;

	echo("<tr  class='". $cor ."'>");
	echo("<td>".$rowArquivo['ARQ_ID']."</td>");
	echo("<td>".$rowArquivo['ARQ_NSA']."</td>");
	$data = substr($rowArquivo['ARQ_DATA_PROCESSAMENTO'],8,2)
	   ."/".substr($rowArquivo['ARQ_DATA_PROCESSAMENTO'],5,2)
	   ."/".substr($rowArquivo['ARQ_DATA_PROCESSAMENTO'],0,4);
	echo("<td>".$data."</td>");
	echo("<td>".$rowArquivo['ARQ_NOME']."</td>");
	echo("<td align='right'>".$rowArquivo['ARQ_REGISTROS']."</td>");
	echo("<td align='right'>".$rowArquivo['ARQ_VALOR']."</td>");
	echo("<td align='right'>".$rowArquivo['ARQ_VALOR_CREDITADO']."</td>");
	echo("<td align='right'>".$rowArquivo['ARQ_VALOR_DESCONTOS']."</td>");
	echo("<td align='right'>".$rowArquivo['ARQ_REGISTROS_ERRO']."</td>");
	echo("<td align='right'>".$rowArquivo['ARQ_VALOR_ERRO']."</td>");
	echo("<td id = '".$rowArquivo['ARQ_NOME']."' align='center'><img src='img/btn_detalhe.gif' width='10'></td>");
	echo("</tr>");
}
echo("</table>");
echo("</form></body></html>");

?>














