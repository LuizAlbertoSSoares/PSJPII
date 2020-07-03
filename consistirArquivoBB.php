<?php
set_time_limit(0); 
include "Conn.php";
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i').date('s');

$arquivo = fopen ("BB000688.REM", "r");
while (!feof($arquivo)){
	$linha = fgets($arquivo, 4096);
	if (substr($linha,0,1) == "E"){
		$quota = substr($linha,4,5);
		$sql = ""
		." SELECT T.TIT_EVN_ID, T.TIT_ID, C.CTB_ID_BANCO FROM sce_titulo T"
		." INNER JOIN sce_contribuinte C ON (C.CTB_ID = T.TIT_CLN_ID)"
		." WHERE T.TIT_QUO_ID = ".$quota;
		$exe = mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_num_rows($exe);
		$row = mysql_fetch_assoc($exe);	
		if (mysql_error() != ""){echo "ERRO";}
		$banco = $row['CTB_ID_BANCO'];
		if ($banco != 1){
			echo $quota." --> ".$banco."<br>";
		}
	}
	
}
fclose($arquivo);

?>