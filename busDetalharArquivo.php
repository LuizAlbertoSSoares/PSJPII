<?php
$nomeArquivo = $_POST['nomeArquivo'].".html";
$tipoArquivo = $_POST['tipoArquivo'];

$caminhoArquivo = "uploads";
if ($tipoArquivo == "REMESSA"){$caminhoArquivo = "downloads"; };
$barras = "//";
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$barras = "\\";};
$arquivoHtml = $caminhoArquivo.$barras.$nomeArquivo;

if(!file_exists($arquivoHtml))  {echo("Consulta inexistente para o arquivo");  exit;}

$nomeArquivoModelo = $arquivoHtml;
$arquivoModelo = fopen($nomeArquivoModelo , "rb");
$arquivoNovo   = fread($arquivoModelo,1000000);
fclose($arquivoModelo);
echo($arquivoNovo);

?>