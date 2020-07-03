<?php
require_once('busConsistirPemissao.php');
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";

$arquivoOrigem = $_POST['arquivoRetorno'];
//$tipoDeArquivo = substr($arquivoOrigem,-3);

$arquivoRetorno = "uploads//".$arquivoOrigem;
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}

if (!unlink($arquivoRetorno))
{
  echo ("Erro ao excluir o arquivo $arquivoRetorno");
}
else
{
  echo ("Excluido o arquivo $arquivoRetorno com sucesso!");
}

?>