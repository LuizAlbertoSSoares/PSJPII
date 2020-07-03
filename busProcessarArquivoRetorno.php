<?php
require_once('busConsistirPemissao.php');
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";

include "Conn.php";

$arquivoOrigem = $_POST['arquivoRetorno'];
//$tipoDeArquivo = substr($arquivoOrigem,-3);

$arquivoRetorno = "uploads//".$arquivoOrigem;
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}
$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
$linha = fgets($arquivo, 4096);
fclose($arquivo);
#----- RETORNO - COBRANÇA BANCO DO BRASIL -----#
if (substr($arquivoOrigem,0,4) == "4332")    { include "busProcessarArquivoBoletoSICOOB.php";   exit; }
if (substr($arquivoOrigem,0,3) == "IED")    { include "busProcessarArquivoBoletoBB.php";   exit; }
#----- RETORNO - DEBITO BB  -----#
if (substr($arquivoOrigem,0,6) == "DBT BB") { include "busProcessarArquivoRetornoBB.php";  exit; }
#----- RETORNO - DEBITO BRB -----#
if (substr($arquivoOrigem,0,7) == "DBT BRB"){ include "busProcessarArquivoRetornoBRB.php"; exit; }
#----- RETORNO - DEBITO CEF -----#
if (substr($arquivoOrigem,0,7) == "DBT CEF"){ include "busProcessarArquivoRetornoCEF.php"; exit; }

echo "O tipo de arquivo selecionado não e compativel com o Banco!";
exit;
function mensagemDeErro($erro){
	$mensagem = "Debito não efetuado";
	if ($erro == "00"){$mensagem = "Débito efetuado";}
	if ($erro == "01"){$mensagem = "Débito não efetuado - Insufissiencia de saldo";}
	if ($erro == "02"){$mensagem = "Débito não efetuado - Conta não cadastrada";}
	if ($erro == "04"){$mensagem = "Débito não efetuado - Outras restrições";}
	if ($erro == "30"){$mensagem = "Débito não efetuado - Sem contrato de débito";}
	if ($erro == "31"){$mensagem = "Debito efetuado em data diferente - Feriado na praça de débito";}
	return $mensagem;
}
?>