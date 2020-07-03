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

if ($_POST['Operacao'] == 'DebitoEspecial'){
    echo("<!doctype html>");
	echo("<html lang='en'><head><title></title>");
	echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
	echo("<style>");
	echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: black; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
	echo("</style>");
	echo("</head><body><form>");
	
	echo("<table class='tbCaixa'><thead>");
	echo("<th align='left'  width='45%'>DÉBITOS EM DATA ESPECIAL</th>");
	echo("<th align='right' width='15%'>".$dia."/".$mes."/".$ano."  ".date('H').":".date('i')."</th>");
	echo("</thead></table>");	
	
	echo("<table class='tbCaixa'><thead>");
	echo("<th align='left' ></th>");
	echo("</thead></table>");
	echo "<br><br>";

	echo("<table class='tbCaixa'><thead>");
	echo("<th align='left'  width='45%'>Contribuintes</th>");
	echo("</thead></table>");
	echo "<hr>";	

	$sql = " SELECT c.CTB_NOME, c.CTB_ID_BANCO, b.BAN_NUMERO, b.BAN_NOME, c.CTB_NM_AGENCIA, c.CTB_NM_CONTA_CORRENTE, c.diaParaDebito from sce_contribuinte c"
         . "  INNER JOIN sce_banco b   ON (b.BAN_ID = c.CTB_ID_BANCO)"
		 . "  INNER JOIN sce_quota q  ON (c.CTB_ID = q.QUO_ID_CONTRIBUINTE)"
         . "  WHERE c.diaParaDebito > 0"
		 . "  	AND q.quo_forma_pagamento = 'D'"
		 ."   ORDER BY diaParaDebito ASC";
	
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);

	echo("<table id='tbDebitoEspecial' class='tbCaixa'>");
	echo("<thead>");
	echo("<th align='left'>Nome</td>");
    echo("<th align='right'>Banco</td>");
	echo("<th align='right'></td>");
	echo("<th align='right'>Agencia</td>");
	echo("<th align='right'>Conta</td>");
	echo("<th align='right'>Dia para débito</td>");
    echo("</thead>");
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$cor1 = 'cor1';	$cor2 = 'cor2';	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
        echo("<tr class='". $cor ."'>");
		echo("<td align='left'>".$row['CTB_NOME']."</td>");
		echo("<td align='right'>".$row['BAN_NUMERO']."</td>");
		echo("<td align='left'>".$row['BAN_NOME']."</td>");
		echo("<td align='right'>".$row['CTB_NM_AGENCIA']."</td>");
		echo("<td align='right'>".$row['CTB_NM_CONTA_CORRENTE']."</td>");
		echo("<td align='right'>".$row['diaParaDebito']."</td>");
		echo("</tr>");
	}
	echo("</table>");
	
	echo "<script type='text/javascript'>";
	echo "	$('table#tbDebitoEspecial tr').hover( ";
	echo "		function(){ $(this).addClass('destaque'); }, ";
	echo "		function(){ $(this).removeClass('destaque'); } ";
	echo "	);  	";
	echo "</script>	";
	
	echo("</form></body></html>");
	exit;	
}
?>
