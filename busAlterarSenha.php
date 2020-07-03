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

$usuario = $_POST['alterarSenhaUsuario'];
$senhaCodificadaAtua = sha1($_POST['alterarSenhaSenhaAtual']);
$senhaCodificadaNova = sha1($_POST['alterarSenhaSenhaNova']);

if ($_POST['Operacao'] == "ALTERARSENHA"){
	$sql = "SELECT * FROM sce_usuario WHERE usuario = '".$usuario."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$usuarioNabase = "";
	$senhaNabase = "";
	while ($row = mysql_fetch_assoc($exe)){
		$usuarioNaBase = $row['usuario'];
		$senhaNaBase = $row['senha'];
	}
	if ($usuario != $usuarioNaBase){echo "Usuario sem permissão para alterar senha!";exit;}
	if ($senhaCodificadaAtua != $senhaNaBase) {echo "Senha não é valida!";exit;}
	$sql = "UPDATE sce_usuario SET senha = '".$senhaCodificadaNova."'"." WHERE usuario = '".$usuario."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	echo mysql_affected_rows()." Senha alterada...<br>";
	exit;
}

?>