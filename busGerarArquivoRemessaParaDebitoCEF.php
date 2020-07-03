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

$sql = "SELECT MAX(SEQUENCIAL) as Remessa FROM sce_remessa WHERE COD_BANCO = 104 AND EVENTO_ID = ".$gblCampanha;
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec))	{ $numeroDaRemessa = $row['Remessa'] + 1;};
#if ($numeroDaRemessa == 1){$numeroDaRemessa = 236;};

$dataArquivoCEF = $dia."/".$mes."/".$ano;
$nomeArquivoCEF = "ACC.".$dia.$mes.$ano.".AAFPFE.".str_pad($numeroDaRemessa,6,"0", STR_PAD_LEFT).".REM";
$arquivoAberto = fopen($destino.$nomeArquivoCEF,"w");

$banco = "104";																	// Codigo da CEF(TAMANHO 4)
$codigoConvenio = "233285110001";                                               // Código do convenio(TAMANHO 12)       
$codigoContaCEF = "3494003000030040";                                           // Código da conta da Paroquia(TAMANHO 16)
                   
$codigoTeste    = "PP";                                                         // Código indicando teste TT ou produção PP(TAMANHO 2)
$regGrav = 0;
# compor header
$fixo1 = "A1".$codigoConvenio."        MITRA ARQUIDIOCESANA104CAIXA ECONOMICA FEDE";
            
$data  = $ano.$mes.$dia;														// Data do arquivo no formato aaaammdd
$numeroArquivo = str_pad($numeroDaRemessa,6,"0", STR_PAD_LEFT);					// Numero do arquivo "000441"
$fixo2 = "04DEB AUTOMA       ".$codigoContaCEF.$codigoTeste."                           000000 ";
$linha = $fixo1.$data.$numeroArquivo.$fixo2."\r\n";
fwrite($arquivoAberto, $linha);$regGrav +=1;

//***** CABECALHO DO RELATORIO DA REMESSA
$arquivoHtml  = fopen($destino.$nomeArquivoCEF.".html", "w");
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
fwrite($arquivoHtml, "<div id='divTitulo'><h4>ARQUIVO DE REMESSA - CAIXA ECONOMICA FEDERAL</h4></div>\r\n");
fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Remessa:</td><td>".$numeroDaRemessa."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Arquivo:</td><td>".$nomeArquivoCEF."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Data:</td><td>".$dataArquivoCEF."</td></tr>\r\n");
fwrite($arquivoHtml, "<tr><td width='20%' align='right'>Data para debito:</td><td>".$diaAAAAMMDD."/".$mesAAAAMMDD."/".$anoAAAAMMDD."</td><tr>\r\n");
fwrite($arquivoHtml, "</table>\r\n");
fwrite($arquivoHtml, "<br>\r\n");
fwrite($arquivoHtml, "<table class='tbNormal' align='center' id='tbLista'>\r\n");
fwrite($arquivoHtml, "<thead><th class='tdLista'width='10%' align='right'>Quota</td><th class='tdLista'width='30%' align='left'>Contribuinte</td><th class='tdLista'width='20%' align='left'>Numero Documento</td><th class='tdLista'width='10%' align='right'>Agencia</td><th class='tdLista'width='10%' align='right'>Conta</td><th class='tdLista'width='20%' align='right'>Valor</td></thead>\r\n");
//************************

# compor detalhe
$contadorDetalhe = 0;
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
		."  CTB_CONTA_POUPANCA,"		
		."  QUO_ID_QUOTA, QUO_FORMA_PAGAMENTO"
		." FROM sce_titulo AS T, sce_contribuinte AS C, sce_quota AS Q"
		." WHERE TIT_ID = ".$listaTitulos[$x]
		."   AND CTB_ID = TIT_CLN_ID"
		."   AND QUO_ID_QUOTA = TIT_QUO_ID AND QUO_FORMA_PAGAMENTO = 'D' AND CTB_ID_BANCO = 3";
	
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
#		."  CTB_NOME,"
#		."  CTB_CONTA_POUPANCA"
#		." FROM sce_titulo AS T, sce_contribuinte as C"
#		." WHERE TIT_ID = ".$listaTitulos[$x]
#		."   AND CTB_ID = TIT_CLN_ID";

		$exec = mysql_query($sql, $conn) or die(mysql_error());
		$row  = mysql_num_rows($exec);
		$contador = 0;
		while ($row = mysql_fetch_assoc($exec)){
			$fixo1 = "E0".str_pad($gblCampanha,2,"0", STR_PAD_LEFT);
			$quota = $row['TIT_QUO_ID'];
			$numeroQuota = str_pad($quota,5,"0", STR_PAD_LEFT);
			$fixo2 = str_pad("",17," ", STR_PAD_LEFT);
			$agencia = $row['CTB_NM_AGENCIA'];
			$agenciaDebito = str_pad($agencia,4,"0", STR_PAD_LEFT); 	// Agencia para debito "0212" sem digito
			$operacaoCEF = "001";
			$tipoConta = $row['CTB_CONTA_POUPANCA'];
			$tipoPPA = "S";
			if(strcasecmp($tipoConta, $tipoPPA) == 0) {
				$operacaoCEF = "013";
			}
			$conta = $row['CTB_NM_CONTA_CORRENTE'];
			$contaDebito = str_pad($conta,9,"0", STR_PAD_LEFT);		// Conta para debito "00000212100916" com digito
			$fixo3 = str_pad("",02," ", STR_PAD_LEFT);
			$valor = $row['TIT_VALOR_TITULO'];
			$somaLote += $row['TIT_VALOR_TITULO'];
			$pontos = array(",", ".");$valorSemPonto = str_replace($pontos, "", $valor);
			$valorDebito = str_pad($valorSemPonto,15,"0", STR_PAD_LEFT);		// Valor a ser debitado "000000000005000"
			$fixo4 = "03";					
			$documento = $row['TIT_NUMERO_DOCUMENTO'];
			$fixo5 = str_pad("",50," ", STR_PAD_LEFT);
			$contadorDetalhe += 1;
			$regDetalhe = str_pad($contadorDetalhe,6,"0", STR_PAD_LEFT);
			$fixo6 = str_pad("",8," ", STR_PAD_LEFT);
			$fixo7 = "0";
			$linha = $fixo1.$numeroQuota.$fixo2.$agenciaDebito.$operacaoCEF.$contaDebito.$fixo3.$dataDebito.$valorDebito.$fixo4.$documento.$fixo5.$regDetalhe.$fixo6.$regDetalhe.$fixo7."\r\n";
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
$regTot = $regGrav + 1;
$totalRegGrav = str_pad($regTot,6,"0", STR_PAD_LEFT);		// Total de registros no arquivo "000005
$totalRegistros = str_pad($regGrav,6,"0", STR_PAD_LEFT);	// Total de registros no arquivo "000005
$valor = number_format($somaLote,2,",",".");
$pontos = array(",", ".");$valorSemPonto = str_replace($pontos, "", $valor);
$valorLote = str_pad($valorSemPonto,17,"0", STR_PAD_LEFT);
$fixo2 = str_pad("",119," ", STR_PAD_LEFT);
$fixo3 = " ";
$linha = $fixo1.$totalRegGrav.$valorLote.$fixo2.$totalRegistros.$fixo3."\r\n";
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
.",'104'"
.",".$numeroDaRemessa
.",'".$nomeArquivoCEF."'"
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
		$sql = "UPDATE sce_titulo SET REMESSA_ID = ".$numeroDaRemessa.", TIT_STATUS = 1 WHERE TIT_ID = ".$listaTitulos[$x];
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

$nomeArquivoModelo = $nomeArquivoCEF.".html";
$arquivoModelo = fopen($destino.$nomeArquivoModelo , "rb");
$arquivoNovo   = fread($arquivoModelo,1000000);
fclose($arquivoModelo);
echo($arquivoNovo);


?>