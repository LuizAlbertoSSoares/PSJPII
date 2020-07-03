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
$horaReferencia = date('H').date('i').date('s');

$diaAAAAMMDD = substr($_POST['dataParaDebito'],0,2);
$mesAAAAMMDD = substr($_POST['dataParaDebito'],3,2);
$anoAAAAMMDD = substr($_POST['dataParaDebito'],6,4);
$errData = 0;
if ($diaAAAAMMDD < 1)    {$errData = 1;};
if ($diaAAAAMMDD > 31)   {$errData = 1;};
if ($mesAAAAMMDD < 1)    {$errData = 1;};
if ($mesAAAAMMDD > 12)   {$errData = 1;};
if ($anoAAAAMMDD < 2011) {$errData = 1;};
if ($anoAAAAMMDD < $ano) {$errData = 1;};
if ($errData > 0) {echo "Data para debito invalida!";exit;};
$dataDebito = $anoAAAAMMDD.$mesAAAAMMDD.$diaAAAAMMDD;

$lista = $_POST['listaEmail'];
$listaTitulos = explode(';', $lista);
$gblCampanha = $globalEvento;

$destino = 'downloads//';
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
	$destino = "downloads\\";
}

$sql = "SELECT MAX(SEQUENCIAL) as Remessa FROM sce_remessa WHERE COD_BANCO = 001";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec))	{ $numeroDaRemessa = $row['Remessa'] + 1;};

$nomeArquivoBB = "BB".str_pad($numeroDaRemessa,6,"0", STR_PAD_LEFT).".REM";
$dataArquivoBB = $dia."/".$mes."/".$ano;
$arquivoAberto = fopen($destino.$nomeArquivoBB,"w");

$banco = "001";													// Codigo do banco 
$convennio = "0000000";											// Código do convenio para débito em conta
$regGrav = 0;
# compor header
$fixo1 = "A100558"; 											// A1 + CÓDIGO DO CONVÊNIO DO BANCO
$fixo2 = str_pad("",15," ", STR_PAD_LEFT);
$fixo3 = "MITRA ARQUIDIOCESANA001BANCO DO BRASIL S.A.";
$data = $ano.$mes.$dia;											// Data do arquivo no formato aaaammdd
$numeroArquivo = str_pad($numeroDaRemessa,6,"0", STR_PAD_LEFT); // Numero do arquivo "000441"
$fixo4 = "04DEBITO AUTOMATICO";
$fixo5 = str_pad("",52," ", STR_PAD_LEFT);
$linha = $fixo1.$fixo2.$fixo3.$data.$numeroArquivo.$fixo4.$fixo5."\r\n";
fwrite($arquivoAberto, $linha);$regGrav +=1;

//***** CABECALHO DO RELATORIO DA REMESSA
$arquivoHtml  = fopen($destino.$nomeArquivoBB.".html", "w");
fwrite($arquivoHtml, "<html><head><title></title>\r\n");
fwrite($arquivoHtml, "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\r\n");
fwrite($arquivoHtml, "<style>\r\n");
fwrite($arquivoHtml, "* { text-decoration: none;}\r\n");
fwrite($arquivoHtml, "a { color: #6B8D58; }\r\n");
fwrite($arquivoHtml, ".tbConsulta  	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:900px;}\r\n");
fwrite($arquivoHtml, "#divTitulo{\r\n");
fwrite($arquivoHtml, " text-align			: left;\r\n");
fwrite($arquivoHtml, " border				: 1px solid #f9f9f9;\r\n");
fwrite($arquivoHtml, " background			: #ccc;\r\n");
fwrite($arquivoHtml, "}\r\n");
fwrite($arquivoHtml, "</style>\r\n");
fwrite($arquivoHtml, "<div id='divTitulo'><h4>ARQUIVO DE REMESSA - BANCO DO BRASIL</h4></div>\r\n");
fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Remessa:</td><td>".$numeroDaRemessa."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Arquivo:</td><td>".$nomeArquivoBB."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Data:</td><td>".$dataArquivoBB."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Data para debito:</td><td>".$diaAAAAMMDD."/".$mesAAAAMMDD."/".$anoAAAAMMDD."</td><tr>\r\n");
fwrite($arquivoHtml, "</table>\r\n");
fwrite($arquivoHtml, "<br>\r\n");
fwrite($arquivoHtml, "<table class='tbNormal' align='center' id='tbLista'>\r\n");
fwrite($arquivoHtml, "<thead><th class='tdLista'width='10%' align='right'>Quota</td><th class='tdLista'width='30%' align='left'>Contribuinte</td><th class='tdLista'width='20%' align='left'>Numero Documento</td><th class='tdLista'width='10%' align='right'>Agencia</td><th class='tdLista'width='10%' align='right'>Conta</td><th class='tdLista'width='20%' align='right'>Valor</td></thead>\r\n");
//************************

# compor detalhe
for($x=0; $x< count($listaTitulos); $x++){
	if ($listaTitulos[$x] != ""){
	
		$sql = ""
		."SELECT"
		." TIT_ID,"
		." TIT_CLN_ID,"
		." TIT_QUO_ID,"
		." TIT_DATA_LANCAMENTO,"
		." TIT_ANO_MES_DOCUMENTO,"
		." TIT_VALOR_TITULO,"
		." TIT_NUMERO_DOCUMENTO,"
		." TIT_NOSSO_NUMERO,"
		." TIT_VALOR_RECEBIDO,"
		." REMESSA_ID,"
		." TIT_STATUS,"
		." SUBSTR(TIT_NUMERO_DOCUMENTO,9,2) AS Parcela,"
 		."  CTB_ID_BANCO,"
		."  CTB_NM_AGENCIA,"
		."  CTB_NM_CONTA_CORRENTE,"
		."  CTB_NOME,"
		."  QUO_ID_QUOTA, QUO_FORMA_PAGAMENTO"
		." FROM sce_titulo AS T, sce_contribuinte AS C, sce_quota AS Q"
		." WHERE TIT_ID = ".$listaTitulos[$x]
		."   AND CTB_ID = TIT_CLN_ID"
		."   AND QUO_ID_QUOTA = TIT_QUO_ID AND QUO_FORMA_PAGAMENTO = 'D' AND CTB_ID_BANCO = 1";
	
#		$sql = ""
#		."SELECT"
#		." TIT_ID,"
#		." TIT_CLN_ID,"
#		." TIT_QUO_ID,"
#		." TIT_DATA_LANCAMENTO,"
#		." TIT_ANO_MES_DOCUMENTO,"
#		." TIT_VALOR_TITULO,"
#		." TIT_NUMERO_DOCUMENTO,"
#		." TIT_NOSSO_NUMERO,"
#		." TIT_VALOR_RECEBIDO"
#		." REMESSA_ID,"
#		." TIT_STATUS,"
#		." SUBSTR(TIT_NUMERO_DOCUMENTO,9,2) AS Parcela,"
#		."  CTB_NM_AGENCIA,"
#		."  CTB_NM_CONTA_CORRENTE,"
#		."  CTB_NOME"
#		." FROM sce_titulo AS T, sce_contribuinte as C"
#		." WHERE TIT_ID = ".$listaTitulos[$x]
#		."   AND CTB_ID = TIT_CLN_ID";

 		$exec = mysql_query($sql, $conn) or die(mysql_error());
		$row  = mysql_num_rows($exec);
		$contador = 0;
		while ($row = mysql_fetch_assoc($exec)){
			$fixo1 = "E".str_pad($globalEvento,3,"0", STR_PAD_LEFT);
			$quota = $row['TIT_QUO_ID'];
			$numeroQuota = str_pad($quota,5,"0", STR_PAD_LEFT);
			$fixo2 = str_pad("",17," ", STR_PAD_LEFT);
			$agencia = substr($row['CTB_NM_AGENCIA'],0,-1);
			$agenciaDebito = str_pad($agencia,4,"0", STR_PAD_LEFT); 	// Agencia para debito "0212" sem digito
			$conta = substr($row['CTB_NM_CONTA_CORRENTE'],0,-1);
			$contaDebito = str_pad($conta,14,"0", STR_PAD_LEFT);		// Conta para debito "00000212100916" sem digito
			$valor = $row['TIT_VALOR_TITULO'];
			$somaLote += $row['TIT_VALOR_TITULO'];
			$pontos = array(",", ".");$valorSemPonto = str_replace($pontos, "", $valor);
			$valorDebito = str_pad($valorSemPonto,15,"0", STR_PAD_LEFT);		// Valor a ser debitado "000000000005000"
			$fixo3 = "03".$convennio;											// Numero do convenio para débito em conta
			$documento = $row['TIT_NUMERO_DOCUMENTO'];
			$fixo4 = str_pad("",63," ", STR_PAD_LEFT);
			$fixo5 = "0";
			$linha = $fixo1.$numeroQuota.$fixo2.$agenciaDebito.$contaDebito.$dataDebito.$valorDebito.$fixo3.$documento.$fixo4.$fixo5."\r\n";
			fwrite($arquivoAberto, $linha);$regGrav +=1;
//**** GRAVAR DETALHE NO RELATORIO
			fwrite($arquivoHtml, "<tr><td align='right' class='tdLista'>".$quota."</td><td class='tdLista'>".$row['CTB_NOME']."</td><td class='tdLista'>".$row['TIT_NUMERO_DOCUMENTO']."</td><td align='right' class='tdLista'>".$row['CTB_NM_AGENCIA']."</td><td align='right' class='tdLista'>".$row['CTB_NM_CONTA_CORRENTE']."</td><td align='right' class='tdLista'>".$row['TIT_VALOR_TITULO']."</td></tr>\r\n");
//*********************************			
		}
	}	
}
//***** FECHAR DETALHE DO RELATORIO
fwrite($arquivoHtml, "</table>\r\n");
//*********************************
# compor trailer do arquivo
$fixo1 = "Z";
$regGrav +=1;
$totalRegistros = str_pad($regGrav,6,"0", STR_PAD_LEFT);	// Total de registros no arquivo "000005
$valor = number_format($somaLote,2,",",".");
$pontos = array(",", ".");$valorSemPonto = str_replace($pontos, "", $valor);
$valorLote = str_pad($valorSemPonto,17,"0", STR_PAD_LEFT);
$fixo2 = str_pad("",126," ", STR_PAD_LEFT);
$linha = $fixo1.$totalRegistros.$valorLote.$fixo2."\r\n";
fwrite($arquivoAberto, $linha);
fclose($arquivoAberto);

$Valor_Total_Registros = str_replace(".", "", $valor);
$Valor_Total_Registros = str_replace(",", ".", $Valor_Total_Registros);

$sql = ""
." INSERT INTO sce_remessa"
." (EMPRESA_ID, EVENTO_ID, COD_BANCO, SEQUENCIAL, NOME_ARQ, REMESSA_DATA, TOTAL_REGISTROS, VALOR_TOTAL_REGISTROS)"
." VALUES ("
."1"
.",".$gblCampanha
.",'001'"
.",".$numeroDaRemessa
.",'".$nomeArquivoBB."'"
.",'".$ano."-".$mes."-".$dia."'"
.",".$totalRegistros
.",".$Valor_Total_Registros
.")";

$rs = mysql_query($sql, $conn);
if (mysql_error() != ""){
	echo("Erro ao incluir arquivo<br>");
	echo("sql=".$sql);
	exit();
}

for($x=0; $x< count($listaTitulos); $x++){
	if ($listaTitulos[$x] != ""){
		$sql = "UPDATE sce_titulo SET REMESSA_ID = ".$numeroDaRemessa.", TIT_STATUS = 1, hdrdata = ".$dataReferencia.",hdrhora = ".$horaReferencia.",hdrobse = '".$_POST['listaEmail']."' WHERE TIT_ID = ".$listaTitulos[$x];
		$rs = mysql_query($sql, $conn);
		if (mysql_error() != ""){
			echo("Erro ao atualizar registros enviados...<br>");
			echo("sql=".$sql);
			exit();
		}		
	}	
}

fwrite($arquivoHtml, "<br>\r\n");
fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Total de registros:</td><td>".$totalRegistros."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Valor total:</td><td>".$valor."</td></tr>\r\n");
fwrite($arquivoHtml, "</table>\r\n");
fclose($arquivoHtml);

$nomeArquivoModelo = $nomeArquivoBB.".html";
$arquivoModelo = fopen($destino.$nomeArquivoModelo , "rb");
$arquivoNovo   = fread($arquivoModelo,1000000);
fclose($arquivoModelo);
echo($arquivoNovo);

?>