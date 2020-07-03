<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i')."00";
$horaMinuto     = date('H').":".date('i');
include("BoletoBB_funcoes.php");

if ($_POST['frmOperacao'] == "INCLUIR"){
	$cmdSQL = "SELECT MAX(ctb_id) as UltimoRegistro FROM sce_contribuinte";
	$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	while ($row = mysql_fetch_assoc($exec))	{ $Contribuinte = $row['UltimoRegistro']; };
	$Contribuinte += 1;
	$cpf = "";
	$cnpj= "";
	if ($_POST['frmTipoPes'] == "F" or "f") {$cpf  = $_POST['frmCPF'];};
	if ($_POST['frmTipoPes'] == "J" or "j") {$cnpj = $_POST['frmCNPJ'];};
//	$Banco = $_POST['frmBanco'];
//	if ($Banco == "") {$Banco = "0";};
	
//	$ContaPoupanca = "N";
//	if ($_POST['frmTipoDeConta'] == "P") {$ContaPoupanca = "S";};
	
	$Nome      = $_POST['frmNome']; 
	$Endereco  = $_POST['frmEndereco'];
	$Bairro    = $_POST['frmBairro'];
	$Municipio = $_POST['frmCidade'];
	$Uf        = $_POST['frmEstado'];

	$Nome      = retiraAcentos(utf8_decode($Nome));
	$Endereco  = retiraAcentos(utf8_decode($Endereco));
	$Bairro    = retiraAcentos(utf8_decode($Bairro));
	$Municipio = retiraAcentos(utf8_decode($Municipio));
	$Uf        = retiraAcentos(utf8_decode($Uf));

	$Nome      = strtoupper($Nome);
	$Endereco  = strtoupper($Endereco);
	$Bairro    = strtoupper($Bairro);
	$Municipio = strtoupper($Municipio);
	$Uf        = strtoupper($Uf);

	$Vld_CPF = validaCPF($cpf);
	
	if ($Vld_CPF == false){
			echo("<table>");
			echo "CPF Inválido";
			echo("</table>");
			exit();}
	else{
		$cmdSQL = "insert into sce_contribuinte (
		ctb_id,
		ctb_tipo,
		ctb_nome,
		ctb_rg,
		ctb_endereco,
		ctb_bairro,
		ctb_municipio,
		ctb_uf,
		CTB_CEP,
		ctb_fone_residencial,
		ctb_fone_mobile,
		ctb_email,
		ctb_dia_mes_aniversario,
		ctb_cpf,
		ctb_cnpj
		)
		values ("
		.$Contribuinte
		.", '".$_POST['frmTipoPes']."'"
		.", '".$Nome."'"
		.", '".$_POST['frmRG']."'"
		.", '".$Endereco."'"
		.", '".$Bairro."'"
		.", '".$Municipio."'"
		.", '".$Uf."'"
		.", '".$_POST['frmCep']."'"
		.", '".$_POST['frmTelResidencial']."'"
		.", '".$_POST['frmTelCelular']."'"
		.", '".$_POST['frmEmail']."'"
		.", '".$_POST['frmDiaMesAniversario']."'"
		.", '".$cpf."'"
		.", '".$cnpj."'"
		.")";

		$rs_Incluir = mysql_query($cmdSQL, $conn);

		if (mysql_error() == ""){
			echo("<table>");
			echo("<tr><td>OK - Contribuinte ".$Contribuinte." incluido com sucesso... <input type='hidden' class='input' id='CodigoDocontribuinte' maxlength='20' size='10' value='".$Contribuinte."'> </td></tr>");
			echo("</table>");
			exit();
		}
		else{
			echo("<table>");
			echo("<tr><td>ERRO:".mysql_error()."</td></tr>");
			echo("</table>");
			exit();
		}
	}	
}

if ($_POST['frmOperacao'] == "ALTERAR"){
	$cpf = "";
	$cnpj= "";
	if ($_POST['frmTipoPes'] == "F") {$cpf  = $_POST['frmCPF'];};
	if ($_POST['frmTipoPes'] == "J") {$cnpj = $_POST['frmCNPJ'];};
//	$Banco = $_POST['frmBanco'];
//	if ($Banco == "") {$Banco = "0";};
//	$ContaPoupanca = "N";
//	if ($_POST['frmTipoDeConta'] == "P") {$ContaPoupanca = "S";};
	
	$Nome      = $_POST['frmNome']; 
	$Endereco  = $_POST['frmEndereco'];
	$Bairro    = $_POST['frmBairro'];
	$Municipio = $_POST['frmCidade'];
	$Uf        = $_POST['frmEstado'];

	$Nome      = retiraAcentos(utf8_decode($Nome));
	$Endereco  = retiraAcentos(utf8_decode($Endereco));
	$Bairro    = retiraAcentos(utf8_decode($Bairro));
	$Municipio = retiraAcentos(utf8_decode($Municipio));
	$Uf        = retiraAcentos(utf8_decode($Uf));

	$Nome      = strtoupper($Nome);
	$Endereco  = strtoupper($Endereco);
	$Bairro    = strtoupper($Bairro);
	$Municipio = strtoupper($Municipio);
	$Uf        = strtoupper($Uf);

	$Vld_CPF = validaCPF($cpf);
	
	if ($Vld_CPF == false){
		echo("<table>");
		echo "CPF Inválido";
		echo("</table>");
		exit();}
	else{	
		$cmdSQL = "UPDATE sce_contribuinte SET "
		."  ctb_tipo 				= '".$_POST['frmTipoPes']."'"
		." ,ctb_nome 				= '".$Nome."'"
		.", ctb_rg 					= '".$_POST['frmRG']."'"
		.", ctb_endereco 			= '".$Endereco."'"
		.", ctb_bairro 				= '".$Bairro."'"
		.", ctb_municipio 			= '".$Municipio."'"
		.", ctb_uf 					= '".$Uf."'"
		.", CTB_CEP 				= '".$_POST['frmCep']."'"
		.", ctb_fone_residencial 	= '".$_POST['frmTelResidencial']."'"
		.", ctb_fone_mobile 		= '".$_POST['frmTelCelular']."'"
		.", ctb_email 				= '".$_POST['frmEmail']."'"
		.", ctb_dia_mes_aniversario = '".$_POST['frmDiaMesAniversario']."'"
		.", ctb_cpf 				= '".$cpf."'"
		.", ctb_cnpj 				= '".$cnpj."'"
		." WHERE ctb_id = ".$_POST['frmContribuinte'];
		$rs = mysql_query($cmdSQL, $conn);
		if (mysql_error() == ""){ 
			echo("<table width='100%'>");
            echo("<tr><td>Mensagem:</td></tr>");
			echo("<tr><td bgcolor='f8f8f8'>OK - Contribuinte ".$Contribuinte." alterado.</td></tr>");
			echo("</table>");
			exit();
		}
		else{echo $_POST['frmTelCelular'];
			echo("<table>");
			echo("<tr><td>ERRO na alteração do contribuinte:".mysql_error()."SQL=".$cmdSQL."</td></tr>");
			echo("</table>");
			exit();
		}
	}	
}

?>