<?php
set_time_limit(0);
require_once('busConsistirPemissao.php');
include "Conn.php";
$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
if ($perfilDoUsuario != "Master"){echo "Usu�rio sem permis�o para esta transa��o!";exit;};
$nomePasta = 'downloads//';
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$nomePasta = "downloads\\";}
$sqlArquivo = "SELECT * FROM sce_remessa ORDER BY REMESSA_ID DESC LIMIT 1";
$exeArquivo = mysql_query($sqlArquivo, $conn) or die(mysql_error());
$rowArquivo = mysql_num_rows($exeArquivo);
while ($rowArquivo = mysql_fetch_assoc($exeArquivo)){$nomeArquivo = $rowArquivo['NOME_ARQ'];}
$nomeArquivoDownload = $nomeArquivo;
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="'.$nomeArquivoDownload.'"');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($nomeArquivoDownload));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');
readfile($nomeArquivoDownload);
exit;
?>