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

session_start('cpfcnpj'); $cpf_cnpj = $_SESSION["cpfcnpj"];
session_start('usuario'); $usuario  = $_SESSION["usuario"];

if ($_POST['Operacao'] == "Consultar"){
	echo("<!doctype html>");
	echo("<html lang='en'><head><title></title>");
	echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
	echo("<style>");
	echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: black; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
	echo("</style>");
	echo("</head><body><form>");
	echo "<div class='ui-widget-header'>Configuração</div>";
	echo "<table id='tbConfiguracao' class='tbCaixa' border=0>";
	
	$sql = "select * from sce_configuracao";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$i += 1;$cor1 = 'cor1';	$cor2 = 'cor2';( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
		echo "<tr class='". $cor ."'>";
		echo "<td align='left'>".$row['campanha']."</td>";		
		echo "<td align='left'>".$row['descricao']."</td>";		
		echo "<td align='left'>".$row['total_parcelas']."</td>";		
		echo "<td align='left'>".$row['valor_parcela']."</td>";		
		echo "<td align='left'>".$row['valor_parcela_unica']."</td>";		
		echo "<td align='left'>".$row['instituicao']."</td>";		
		echo "<td align='left'>".$row['agencia']."</td>";	
		echo "<td align='left'>".$row['conta']."</td>";	
		echo "<td align='left'>".$row['convenio']."</td>";	
		echo "</tr>";
	}
	echo "</table>";
	
	echo "<table id='tbPremios' class='tbCaixa' border=0>";
	$sql = "select * from sce_premios";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	while ($row = mysql_fetch_assoc($exe)){	
		echo "<tr>";
		echo "<td align='left'>".$row['PRM_PARCELA']."</td>";		
		echo "<td align='left'>".$row['PRM_NM_PREMIO']."</td>";	
		$data = substr($row['PRM_DT_SORTEIO'],8,2)."/".substr($row['PRM_DT_SORTEIO'],5,2)."/".substr($row['PRM_DT_SORTEIO'],0,4);
		echo "<td align='left'>".$data."</td>";		
		$data = substr($row['PRM_DT_QUITACAO'],8,2)."/".substr($row['PRM_DT_QUITACAO'],5,2)."/".substr($row['PRM_DT_QUITACAO'],0,4);
		echo "<td align='left'>".$data."</td>";		
		echo "</tr>";
	}
	echo "</table>";
	
	echo "</form></body></html>";	
	echo "<script type='text/javascript'>";
	echo "$('table#tbConfiguracao tbody tr').hover(";
  	echo "function(){ $(this).addClass('destaque'); },";
	echo "	function(){ $(this).removeClass('destaque'); }";
	echo ");";
	echo "</script>";
	exit;
}
if ($_POST['Operacao'] == "Alterar"){

	$sql = "update sce_configuracao set "
	     . " campanha = ".$_POST['Campanha']
		 . ",descricao = '".$_POST['Descricao']."'"
		 . ",total_parcelas = ".$_POST['TotalDeParcelas']
		 . ",valor_parcela = ".$_POST['ValorDaParcela']
		 . ",valor_parcela_unica = ".$_POST['ValorDaParcelaUnica']
		 . ",instituicao = '".$_POST['Instituicao']."'"
		 . ",agencia = ".$_POST['Agencia']
		 . ",conta = ".$_POST['Conta']
		 . ",convenio = ".$_POST['Convenio'];
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	echo mysql_affected_rows()." registros alterados.";
	
		$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar01']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar01']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar01'],6,4)."-".substr($_POST['DtaPar01'],3,2)."-".substr($_POST['DtaPar01'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta01'],6,4)."-".substr($_POST['DtaQta01'],3,2)."-".substr($_POST['DtaQta01'],0,2)."'"
		 . " where PRM_PARCELA = 01";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 01 alterado.<br>";		 
	
	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar02']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar02']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar02'],6,4)."-".substr($_POST['DtaPar02'],3,2)."-".substr($_POST['DtaPar02'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta02'],6,4)."-".substr($_POST['DtaQta02'],3,2)."-".substr($_POST['DtaQta02'],0,2)."'"
		 . " where PRM_PARCELA = 02";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 02 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar03']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar03']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar03'],6,4)."-".substr($_POST['DtaPar03'],3,2)."-".substr($_POST['DtaPar03'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta03'],6,4)."-".substr($_POST['DtaQta03'],3,2)."-".substr($_POST['DtaQta03'],0,2)."'"
		 . " where PRM_PARCELA = 03";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 03 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar04']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar04']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar04'],6,4)."-".substr($_POST['DtaPar04'],3,2)."-".substr($_POST['DtaPar04'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta04'],6,4)."-".substr($_POST['DtaQta04'],3,2)."-".substr($_POST['DtaQta04'],0,2)."'"
		 . " where PRM_PARCELA = 04";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 04 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar05']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar05']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar05'],6,4)."-".substr($_POST['DtaPar05'],3,2)."-".substr($_POST['DtaPar05'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta05'],6,4)."-".substr($_POST['DtaQta05'],3,2)."-".substr($_POST['DtaQta05'],0,2)."'"
		 . " where PRM_PARCELA = 05";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 05 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar06']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar06']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar06'],6,4)."-".substr($_POST['DtaPar06'],3,2)."-".substr($_POST['DtaPar06'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta06'],6,4)."-".substr($_POST['DtaQta06'],3,2)."-".substr($_POST['DtaQta06'],0,2)."'"
		 . " where PRM_PARCELA = 06";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 06 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar07']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar07']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar07'],6,4)."-".substr($_POST['DtaPar07'],3,2)."-".substr($_POST['DtaPar07'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta07'],6,4)."-".substr($_POST['DtaQta07'],3,2)."-".substr($_POST['DtaQta07'],0,2)."'"
		 . " where PRM_PARCELA = 07";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 07 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar08']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar08']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar08'],6,4)."-".substr($_POST['DtaPar08'],3,2)."-".substr($_POST['DtaPar08'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta08'],6,4)."-".substr($_POST['DtaQta08'],3,2)."-".substr($_POST['DtaQta08'],0,2)."'"
		 . " where PRM_PARCELA = 08";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 08 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar09']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar09']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar09'],6,4)."-".substr($_POST['DtaPar09'],3,2)."-".substr($_POST['DtaPar09'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta09'],6,4)."-".substr($_POST['DtaQta09'],3,2)."-".substr($_POST['DtaQta09'],0,2)."'"
		 . " where PRM_PARCELA = 09";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 09 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar10']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar10']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar10'],6,4)."-".substr($_POST['DtaPar10'],3,2)."-".substr($_POST['DtaPar10'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta10'],6,4)."-".substr($_POST['DtaQta10'],3,2)."-".substr($_POST['DtaQta10'],0,2)."'"
		 . " where PRM_PARCELA = 10";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 10 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar11']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar11']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar11'],6,4)."-".substr($_POST['DtaPar11'],3,2)."-".substr($_POST['DtaPar11'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta11'],6,4)."-".substr($_POST['DtaQta11'],3,2)."-".substr($_POST['DtaQta11'],0,2)."'"
		 . " where PRM_PARCELA = 11";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 11 alterado.<br>";

	$sql = "update sce_premios set "	
	     . " PRM_NM_PREMIO = '".$_POST['DesPar12']."'"
		 . ",PRM_DS_PREMIO = '".$_POST['DesPar12']."'"
		 . ",PRM_DT_SORTEIO = '".substr($_POST['DtaPar12'],6,4)."-".substr($_POST['DtaPar12'],3,2)."-".substr($_POST['DtaPar12'],0,2)."'"
		 . ",PRM_DT_QUITACAO = '".substr($_POST['DtaQta12'],6,4)."-".substr($_POST['DtaQta12'],3,2)."-".substr($_POST['DtaQta12'],0,2)."'"
		 . " where PRM_PARCELA = 12";
		 $exe =  mysql_query($sql, $conn) or die(mysql_error());
    echo mysql_affected_rows()." Premio 12 alterado.<br>";
	exit;
}
?>