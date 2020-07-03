<?php
require_once('busConsistirPemissao.php');

date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
// teste
if ($_POST['parametroBanco'] == 1){$nomeBanco = "BB - Banco do Brasil";};
if ($_POST['parametroBanco'] == 2){$nomeBanco = "BRB - Banco de Brasília";};
if ($_POST['parametroBanco'] == 3){$nomeBanco = "CEF - Caixa Economica Federal";};

echo("<!doctype html>");
echo("<html lang='en'><head><title></title>");
echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
echo("<style>");
echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
echo(".tdCaixa		{BACKGROUND-COLOR:#F8F8F8;}");
echo("</style>");
echo("</head><body><form>");

echo("<table class='tbCaixa'><thead>");
echo("<th align='left'  width='85%'>AGUARDANDO RETORNO - ".$nomeBanco."</th>");
echo("</thead></table>");
echo "<hr>";

echo("<div style='background:transparent; padding:20px; overflow: auto; position: relative; height: 410px;'>");

include "Conn.php";
$sql=""
."SELECT C.CTB_ID, C.CTB_NOME, Q.QUO_ID_QUOTA,"
." TIT_ID,"
." TIT_CLN_ID,"
." TIT_QUO_ID,"
." TIT_ANO_MES_DOCUMENTO,"
." TIT_VALOR_TITULO,"
." TIT_NUMERO_DOCUMENTO,"
." TIT_NOSSO_NUMERO,"
." TIT_VALOR_RECEBIDO,"
." REMESSA_ID,"
." TIT_STATUS,"
." SUBSTR(TIT_NUMERO_DOCUMENTO,9,2) AS Parcela"
." FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T"
." WHERE Q.QUO_FORMA_PAGAMENTO = 'D'"
."	AND Q.QUO_ID_EVENTO = ".$globalEvento
."	AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID"
."	AND C.CTB_ID_BANCO = ".$_POST['parametroBanco']
."	AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA"
."	AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA"
."	AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO"
."  AND T.TIT_VALOR_RECEBIDO = 0"
."  AND T.TIT_STATUS = 1"
."  AND T.REMESSA_ID > 0 "
."  ORDER BY T.TIT_QUO_ID ASC";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);

echo("<table class='tbNormal' align='center' id='tbLista'>");
echo("<thead>");
echo(""
."<th class='tdLista'width='10%' align='left'>Quota</td>"
."<th class='tdLista'width='40%' align='left'>Contribuinte</td>"
."<th class='tdLista'width='10%' align='left'>Parcela</td>"
."<th class='tdLista'width='10%' align='left'>Valor</td>"
."<th class='tdLista'width='10%' align='left'>AnoMesDocumento</td>"
."<th class='tdLista'width='10%' align='left'>Titulo</td>"
."<th class='tdLista'width='10%' align='left'>NossoNumero</td>"
."<th class='tdLista'width='10%' align='left'>Situação</td>"
."<th class='tdLista'width='10%' align='left'>Remessa</td>"
."</th>");
echo("</thead>");
$quotaAnt = 0;
$totContribuintes = 0;
while ($row = mysql_fetch_assoc($exec)){
	$situacao = 'Pendente'; if ($row['TIT_STATUS'] == '1'){$situacao = 'Remessa';}
	$i += 1;$cor1 = 'cor1';	$cor2 = 'cor2';	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
	if ($quotaAnt == $row['TIT_QUO_ID']){$i -= 1;$cor1 = 'cor1';	$cor2 = 'cor2';	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;};
	echo("<tr class='". $cor ."'>"
	."<td class='tdLista' width='10%'>".$row['TIT_QUO_ID']."</td>"
	."<td class='tdLista' width='40%'>".$row['CTB_NOME']."</td>"
	."<td class='tdLista' width='10%'>".substr($row['TIT_NOSSO_NUMERO'],16,2)."</td>"
	."<td class='tdLista' width='10%'>".$row['TIT_VALOR_TITULO']."</td>"
	."<td class='tdLista' width='10%'>".$row['TIT_ANO_MES_DOCUMENTO']."</td>"
	."<td class='tdLista' width='10%'>".$row['TIT_ID']."</td>"
	."<td class='tdLista' width='10%'>".$row['TIT_NOSSO_NUMERO']."</td>"
	."<td class='tdLista' width='10%'>".$situacao."</td>"
	."<td class='tdLista' width='10%'>".$row['REMESSA_ID']."</td>"
	."</tr>");
	$totContribuintes += 1;
	$quotaAnt = $row['TIT_QUO_ID'];
}
echo("</table>");
echo("</div>");
echo("<table class='tbConsulta' align='center'>");
echo("<tr><td width='100%'><b>Total da lista : ".$totContribuintes."</b></td></tr>");
echo("</table>");
?>

