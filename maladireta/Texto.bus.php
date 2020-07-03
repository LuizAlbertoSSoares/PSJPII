<?php
include "../Conn.php";
require_once('Seguranca.bus.php');
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i')."00";
$horaMinuto     = date('H').":".date('i');

session_start('cpfcnpj'); $cpf_cnpj = $_SESSION["cpfcnpj"];
session_start('usuario'); $usuario  = $_SESSION["usuario"];

$arquivoNome  = $_POST['NomeArquivo'];

if ($_POST['Operacao'] == 'Abrir'){
	$sql = "SELECT texto FROM sce_texto WHERE nome = '".$arquivoNome."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$row = mysql_fetch_assoc($exe);
	echo html_entity_decode($row['texto'], ENT_QUOTES);
	exit;
}
if ($_POST['Operacao'] == 'Salvar'){
	$sql = "DELETE FROM sce_texto WHERE nome = '".$arquivoNome."'";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$descricaoDoTexto = htmlEntities("<pre>".$_POST['Texto']."</pre>", ENT_QUOTES);
	$sql = "INSERT INTO sce_texto (nome, texto) VALUES ('".$arquivoNome."', '".$descricaoDoTexto."')";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	exit;
}
if ($_POST['Operacao'] == 'Listar'){
    echo("<!doctype html>");
	echo("<html lang='en'><head><title></title>");
	echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
	echo("<style>");
	echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: black; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
	echo("</style>");
	echo("</head><body><form>");

	$sql = "SELECT nome FROM sce_texto";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);

	echo("<table id='tbArquivos' class='tbCaixa'>");
	echo("<thead>");
    echo("<th align='left'>Nome</td>");
    echo("</thead>");
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
		$cor1 = 'cor1';	$cor2 = 'cor2';	( $i % 2 == 0 ) ? $cor = $cor1 : $cor = $cor2;
        echo("<tr class='". $cor ."'>");
        echo("<td align='left'>".$row['nome']."</td>");
		echo("</tr>");
	}
	echo("</table>");
	echo("</form></body></html>");

	echo "<script type='text/javascript'>";
	echo "$('table#tbArquivos tbody tr').hover( ";
	echo "	function(){ $(this).addClass('destaque'); }, ";
	echo "	function(){ $(this).removeClass('destaque'); } ";
	echo ");  ";
	echo "</script>";
	exit;
}

?>