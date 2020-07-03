<?php
require_once('busConsistirPemissao.php');
require_once('Conn.php');	
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i')."00";

if ($_POST['Operacao'] == "Excluir"){
	if ($_POST['numeroQuota'] != ''){
		$sql = "UPDATE sce_titulo SET REMESSA_ID = 0, TIT_STATUS = 0 "
		." WHERE TIT_QUO_ID = ".$_POST['numeroQuota']
		." AND   TIT_VALOR_RECEBIDO = 0"
		." AND   TIT_EVN_ID = ".$globalEvento;
		$exe = mysql_query($sql, $conn) or die(mysql_error());
		echo mysql_affected_rows()." Remessa da Quota ".$_POST['numeroQuota']." excluida...<br>";		
		exit;
	}
	
	$codigoBanco = 0;
	if ($_POST['numeroBanco'] ==  1) {$codigoBanco = 1;}
	if ($_POST['numeroBanco'] == 70) {$codigoBanco = 2;}
	if ($_POST['numeroBanco'] == 104){$codigoBanco = 3;}

	$sqlLista = ""
	."SELECT  C.CTB_ID_BANCO, "
	."	C.CTB_ID, "
	."	TIT_QUO_ID, "
	."	TIT_VALOR_RECEBIDO, "
	."	REMESSA_ID, "
	."	TIT_STATUS "
	."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
	."WHERE Q.QUO_FORMA_PAGAMENTO = 'D' "
	." AND   Q.QUO_ID_EVENTO = ".$globalEvento
	." AND   Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
	." AND   C.CTB_ID_BANCO = ".$codigoBanco
	." AND   T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
	." AND   T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
	." AND   T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
	." AND   T.TIT_VALOR_RECEBIDO = 0 "
	." AND   T.REMESSA_ID = ".$_POST['numeroRemessa'];
	$exeLista = mysql_query($sqlLista, $conn) or die(mysql_error());
	$rowLista  = mysql_num_rows($exeLista);
	while ($rowLista = mysql_fetch_assoc($exeLista)){
		$sql = "UPDATE sce_titulo SET REMESSA_ID = 0, TIT_STATUS = 0 "
		." WHERE TIT_QUO_ID = ".$rowLista['TIT_QUO_ID']
		." AND   TIT_VALOR_RECEBIDO = 0"
		." AND   TIT_EVN_ID = ".$globalEvento;
		$exe = mysql_query($sql, $conn) or die(mysql_error());
		echo "Quota ".$rowLista['TIT_QUO_ID']." excluida...<br>";			
	}
	
	$sql = "DELETE FROM sce_remessa WHERE COD_BANCO = ".$_POST['numeroBanco']." AND SEQUENCIAL = ".$_POST['numeroRemessa'];
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	echo mysql_affected_rows()." Remessa excluida...<br>";
	exit;
}

?>