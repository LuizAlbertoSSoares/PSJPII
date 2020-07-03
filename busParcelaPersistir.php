<?php
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";

require_once('busConsistirPemissao.php');
include "Conn.php";

if ($_POST['frmOperacao'] == "BAIXAR"){
	$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
	$Usuario			 = $_SESSION["usuario"];	
	if ($perfilDoUsuario != "Master"){
		if ($perfilDoUsuario != "Junior"){ 
		echo "Usuário sem permissão para esta transação!";exit;};
		}
	$Descontos 	= funcao_ValorSemVirgula($_POST['frmValorDesconto']);
	$Juros 		= funcao_ValorSemVirgula($_POST['frmValorJuros']);
	$Recebido	= funcao_ValorSemVirgula($_POST['frmValorPago']);
	
	$Observacao	= $_POST['frmObservacao'];
	$dataPgtoDBA  = "'".substr($_POST['frmDataBaixar'],6,4)."-".substr($_POST['frmDataBaixar'],3,2)."-".substr($_POST['frmDataBaixar'],0,2)."'";
    if ($dataPgtoDBA == "'--'"){$dataPgtoDBA = $dataReferencia;};
	
	$Documento	= $_POST['frmDocumento'];


    $Comando  = "UPDATE sce_titulo SET ";
    $Dados    = "  TIT_DATA_COMPENSACAO = ".$dataPgtoDBA
          	  . ", TIT_VALOR_DESCONTOS = ".$Descontos
          	  . ", TIT_VALOR_JUROS_MULTA = ".$Juros
          	  . ", TIT_VALOR_RECEBIDO = ".$Recebido
          	  . ", TIT_OBS = '".$Usuario." - ".$Observacao."'"
          	  . ", TIT_STATUS = 9";
    $Condicao = " WHERE TIT_NUMERO_DOCUMENTO = ".$Documento;

	$cmdSQL = $Comando.$Dados.$Condicao;

	$rs_Incluir = mysql_query($cmdSQL, $conn);

	if (mysql_error() == ""){
		echo "O documento ".$Documento." foi baixado.";
		exit();
	}
	else{
		echo "ocorreu um erro...<br>";
		echo mysql_error()."<br>";
		echo $cmdSQL;
		exit();
	}
	exit;
}
if ($_POST['frmOperacao'] == "EXCLUIRBAIXADEPARCELA"){
	$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
	if ($perfilDoUsuario != "Master"){
		if ($perfilDoUsuario != "Junior"){ 
			echo "Usuário sem permissão para esta transação!";exit;};
		}
		
	$Documento	= $_POST['frmDocumento'];

    $Comando  = "UPDATE sce_titulo SET ";
    $Dados    = "  TIT_DATA_COMPENSACAO = ''"
          	  . ", TIT_VALOR_DESCONTOS = ''"
          	  . ", TIT_VALOR_JUROS_MULTA = ''"
          	  . ", TIT_VALOR_RECEBIDO = ''"
          	  . ", TIT_OBS = ''"
          	  . ", TIT_STATUS = 0";
    $Condicao = " WHERE TIT_NUMERO_DOCUMENTO = ".$Documento;
	$cmdSQL = $Comando.$Dados.$Condicao;
	$rs_Incluir = mysql_query($cmdSQL, $conn);
	if (mysql_error() == ""){echo "O documento ".$Documento." foi excluido a baixa da parcela.";exit();}
	else{echo "ocorreu um erro...<br>";echo mysql_error()."<br>";echo $cmdSQL;exit();
	}
	exit;
}
?>