<?php
$hostname_conn = "mysql.paroquiacristorei.kinghost.net";
$database_conn = "nomeDoBanco";
$username_conn = "nomeDoUsuario";
$password_conn = "senhaDoUsuario";

if (PHP_OS == "WIN32" || PHP_OS == "WINNT" || PHP_OS == "WIN64") {
	$hostname_conn = "127.0.0.1";
	$database_conn = "psjpii";
	$username_conn = "root";
	$password_conn = "root";
}

if(!($conn = mysql_connect($hostname_conn,$username_conn,$password_conn)))
{
   echo "Erro ao conectar com MySQL.";
   exit;
}
if(!($con = mysql_select_db($database_conn,$conn)))
{
   echo "Erro ao abrir o banco MySQL.";
   exit;
}
mysql_set_charset('utf8');

$sql = "SELECT * FROM sce_configuracao";
$exe = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exe);
while ($row = mysql_fetch_assoc($exe)){
	$globalEvento              = $row['campanha'];
	$globalValorDaParcela      = $row['valor_parcela'];
	$globalTotalDeParcelas     = $row['total_parcelas'];
	$globalValorDaParcelaUnica = $row['valor_parcela_unica'];
}

function funcao_ValorSemVirgula($get_valor) {  

$valor = $get_valor;
	//trocando virgula por ponto e ponto por ponto
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	return $valor;
}  
?>
