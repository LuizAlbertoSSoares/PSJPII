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

$sql = "select * from sce_configuracao";
$exe = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exe);
while ($row = mysql_fetch_assoc($exe)){
	$CedenteConvenio            = $row['convenio'];		## Numero do convenio para cobranca via boleto
	$ValorParcela	            = $row['valor_parcela'];
	$ValorParcelaUnica          = $row['valor_parcela_unica'];
}
$gblCodigoDoConvenio  = $CedenteConvenio;
$gblEmpresa           = 1;

$parametroOperacao = $_POST['Operacao'];

if ($parametroOperacao == 'AlterarFormaDePagamento'){

	$QuotaNumero          = $_POST['Quota'];
	$CodigoDoContribuinte = $_POST['Contribuinte'];
	$FormaDePagamento     = $_POST['FormaDePagamento'];
	$TipoDeQuota          = $_POST['TipoDeQuota'];

	$cmdSQL = "SELECT * FROM sce_contribuinte WHERE ctb_id = ".$CodigoDoContribuinte;
	$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
	if (mysql_num_rows($exec) < 1){
		echo("<table class='tbRetorno'>");
		echo("<tr><td>O contribuinte ".$CodigoDoContribuinte." não está cadastrado...</td></tr>");
		echo("</table>");
		exit();
	}
# CONSISTIR FORMA DE PAGAMENTO - DEBITO EM CONTA	
//	$row = mysql_num_rows($exec);
//	while ($row = mysql_fetch_assoc($exec))	{
//		if ($FormaDePagamento == "D"){
//			if ($row['CTB_NM_CONTA_CORRENTE'] < 1){
//				echo "O contribuinte não possui conta cadastrada!";exit;
//			}
//		}
//	};
	
	$sql = "UPDATE sce_quota SET QUO_FORMA_PAGAMENTO = '".$FormaDePagamento."'"
	." WHERE "
	." QUO_ID_EVENTO = ".$globalEvento
	." AND QUO_ID_CONTRIBUINTE = ".$CodigoDoContribuinte
	." AND QUO_ID_QUOTA = ".$QuotaNumero;
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	echo mysql_affected_rows()." Alterado(s)";
	exit;
}

if ($parametroOperacao == 'Associar'){
	$QuotaNumero          = $_POST['Quota'];
	$CodigoDoContribuinte = $_POST['Contribuinte'];
	$FormaDePagamento     = $_POST['FormaDePagamento'];
	$TipoDeQuota          = $_POST['TipoDeQuota'];
	
	$cmdSQL = "SELECT * FROM sce_contribuinte WHERE ctb_id = ".$CodigoDoContribuinte;
	$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
	if (mysql_num_rows($exec) < 1){
		echo("<table class='tbRetorno'>");
		echo("<tr><td>O contribuinte ".$CodigoDoContribuinte." não está cadastrado...</td></tr>");
		echo("</table>");
		exit();
	}
# CONSISTIR FORMA DE PAGAMENTO - DEBITO EM CONTA	
//	$row = mysql_num_rows($exec);
//	while ($row = mysql_fetch_assoc($exec))	{
//		if ($FormaDePagamento == "D"){
//			if ($row['CTB_NM_CONTA_CORRENTE'] < 1){
//				echo "O contribuinte não possui conta cadastrada!";exit;
//			}
//		}
//	};
	
	$cmdSQL = "SELECT Q.QUO_ID_CONTRIBUINTE, C.CTB_NOME FROM sce_quota Q, sce_contribuinte C "
			." WHERE QUO_ID_QUOTA = ".$QuotaNumero." AND QUO_ID_EVENTO = ".$globalEvento." AND C.CTB_ID = Q.QUO_ID_CONTRIBUINTE";
	$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	while ($row = mysql_fetch_assoc($exec))	{
		$QuotaAtuNumero = $row['QUO_ID_QUOTA'];
		$QuotaAtuContribuinte = $row['QUO_ID_CONTRIBUINTE'];
		$QuotaAtuNome = $row['CTB_NOME'];
	};
	if ($QuotaAtuContribuinte != ""){
		echo("<table class='tbRetorno'>");
		echo("<tr><td>A quota informada já esta associada ao contribuinte ".$QuotaAtuContribuinte." ".$QuotaAtuNome."</td></tr>");
		echo("</table>");
		exit();
	};
/*	
# SE EXISTIR A QUOTA SEM ASSOCIACAO ASSOCIAR E CONCLUIR OPERACAO
	$sql = "SELECT QUO_ID_CONTRIBUINTE FROM sce_quota "
	." WHERE QUO_ID_QUOTA = ".$QuotaNumero
	." AND   QUO_ID_EMPRESA = ".$gblEmpresa
	." AND  QUO_ID_EVENTO = ".$globalEvento;
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	$quotaContribuinte = 1;
	while ($row = mysql_fetch_assoc($exe)){$quotaContribuinte = $row['QUO_ID_CONTRIBUINTE'];};	
    if (mysql_num_rows($exec) == 0){
		if ($quotaContribuinte == 0){
			$sql = "UPDATE sce_titulo SET TIT_CLN_ID = ".$CodigoDoContribuinte
			." WHERE TIT_CLN_ID = 0 "
			." AND   TIT_QUO_ID = ".$QuotaNumero
			." AND   TIT_PARCELA = ".$i
			." AND   TIT_EVN_ID = ".$globalEvento;
			$exe =  mysql_query($sql, $conn) or die(mysql_error());
			echo mysql_affected_rows()." registros re-associados em Titulos<br>";
			$sql = "UPDATE sce_quota SET QUO_ID_CONTRIBUINTE = ".$CodigoDoContribuinte
			." WHERE QUO_ID_CONTRIBUINTE = 0 "
			." AND   QUO_ID_QUOTA   = ".$QuotaNumero
			." AND   QUO_ID_EMPRESA = ".$gblEmpresa
			." AND   QUO_ID_EVENTO  = ".$globalEvento;
			$exe =  mysql_query($sql, $conn) or die(mysql_error());
			echo mysql_affected_rows()." registros re-associados em Quotas";
			exit;
		}
	}
*/
	#****************************************************************	
	
	if ($QuotaAtuNumero != ""){
		$cmdSQL = "DELETE FROM sce_quota WHERE QUO_ID_QUOTA = ".$QuotaNumero." AND QUO_ID_EVENTO = ".$globalEvento;
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
	};
	
	$parcelasDaQuota = $globalTotalDeParcelas;	
	
	if ($TipoDeQuota == "U"){$parcelasDaQuota = 1;};
	$comando = "INSERT INTO sce_quota ";
	$campos  = " (QUO_ID_EMPRESA, QUO_ID_EVENTO, QUO_ID_QUOTA, QUO_ID_CONTRIBUINTE, QUO_FORMA_PAGAMENTO, QUO_DATA_ENTREGA, QUO_TOTAL_PARCELAS)";
	$dados   = " VALUES (1, ".$globalEvento.", ".$QuotaNumero.", ".$CodigoDoContribuinte.", '".$FormaDePagamento."', ".$dataReferencia.", ".$parcelasDaQuota." )";
	$cmdSQL  = $comando.$campos.$dados;
	$rs_Incluir = mysql_query($cmdSQL, $conn);
	if (mysql_error() == ""){
		echo "OK - Quota incluida.<br>";}
	else {
		echo "ERRO=".mysql_error()." SQL =".$cmdSQL."</br";
		exit;
	}
	//**** INCLUIR PARCELA UNICA ****
	if ($TipoDeQuota == "U"){
		$cmdSQL = "SELECT MAX(TIT_ID) as UltimoTitulo FROM sce_titulo WHERE TIT_EVN_ID = ".sprintf("%02d", $globalEvento);
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
		while ($row = mysql_fetch_assoc($exec)){$UltimoTitulo = $row['UltimoTitulo'];}
		$UltimoTitulo += 1;
		$i = 01;
		$cmdSQL = "SELECT * FROM sce_premios WHERE PRM_ID_EVENTO = ".$globalEvento." AND PRM_PARCELA = '".sprintf("%02d",$i)."'";
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
		while ($row = mysql_fetch_assoc($exec)){
			$MesPremio = $row['PRM_DT_SORTEIO'];
			$AnoMesDoDocumento = substr($row['PRM_DT_QUITACAO'],0,4).substr($row['PRM_DT_QUITACAO'],5,2);
			$DataDoLancamento  = "'".substr($row['PRM_DT_QUITACAO'],0,4)."-".substr($row['PRM_DT_QUITACAO'],5,2)."-".substr($row['PRM_DT_QUITACAO'],8,2)."'";
		}
		$NumeroDoDocumento = sprintf("%05d", $QuotaNumero)."01";
		$NossoNumero       = $NumeroDoDocumento;
		$ValorDoDesconto = 0;
		$ValorDeJurosOuMulta = 0;
		$ValorAcrescimos = 0;
		$ValorRecebido = 0;
		$EspecieDaMoeda = "0.00";
		$SituacaoDaParcela = "0";
		$RemessaGravada = "N";
		$cmdSQL = 
		"INSERT INTO sce_titulo (
		TIT_ID,
		TIT_PARCELA,
		TIT_EVN_ID,
		TIT_CLN_ID,
		TIT_QUO_ID,
		TIT_FORMA_PAG,
		TIT_DATA_DOCUMENTO,
		TIT_NOSSO_NUMERO,
		TIT_NUMERO_DOCUMENTO,
		TIT_VALOR_TITULO,
		TIT_VALOR_DESCONTOS,
		TIT_VALOR_JUROS_MULTA,
		TIT_VALOR_ACRESCIMOS,
		TIT_VALOR_RECEBIDO,
		TIT_ESPECIE_MOEDA,
		TIT_STATUS,
		TIT_REM_GRAVADA)	
		VALUES ("
		.$UltimoTitulo
		.",  ".$ValorRecebido
		.",  ".$globalEvento
		.",  ".$CodigoDoContribuinte
		.",  ".$QuotaNumero
		.", '".$FormaDePagamento."'"
		.",  ".$dataReferencia
		.", '".$NossoNumero."'"
		.", '".$NumeroDoDocumento."'"
		.",  ".$ValorParcelaUnica
		.",  ".$ValorDoDesconto
		.",  ".$ValorDeJurosOuMulta
		.",  ".$ValorAcrescimos
		.",  ".$ValorRecebido
		.", '".$EspecieDaMoeda."'"
		.", '".$SituacaoDaParcela."'"
		.", '".$RemessaGravada."'"
		.")";
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
		if (mysql_error() == ""){
			echo("OK - Parcela unica ".$NumeroDoDocumento." incluida.<br>");
		}
		else{
			echo("<tr><td>ERRO na inclusão dos titulos:".mysql_error()."</td></tr>");
			echo("<tr><td>cmdSQL=".$cmdSQL."</td></tr>");
			echo("</table>");
			exit();
		}
		exit;
	}
	//**** INCLUIR PARCELAS ****
	for ($i = 1; $i <= $globalTotalDeParcelas; $i++){
		$cmdSQL = "SELECT MAX(TIT_ID) as UltimoTitulo FROM sce_titulo WHERE TIT_EVN_ID = ".sprintf("%02d", $globalEvento);
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
		while ($row = mysql_fetch_assoc($exec)){$UltimoTitulo = $row['UltimoTitulo'];}
		$UltimoTitulo += 1;
		$cmdSQL = "SELECT * FROM sce_premios WHERE PRM_ID_EVENTO = ".$globalEvento." AND PRM_PARCELA = '".sprintf("%02d",$i)."'";
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
		while ($row = mysql_fetch_assoc($exec)){
			$MesPremio         = substr($row['PRM_DT_SORTEIO'],5,2);
			$AnoMesDoDocumento = substr($row['PRM_DT_QUITACAO'],0,4).substr($row['PRM_DT_QUITACAO'],5,2);
			$DataDoLancamento  = "'".substr($row['PRM_DT_QUITACAO'],0,4)."-".substr($row['PRM_DT_QUITACAO'],5,2)."-".substr($row['PRM_DT_QUITACAO'],8,2)."'";
		}
		//$globalEvento
		$Parcela             = $i;
		$NumeroDoDocumento = sprintf("%05d", $QuotaNumero).sprintf("%02d", $i);
		//$NossoNumero       = "0".sprintf("%05d", $QuotaNumero).sprintf("%02d", $i)."0".sprintf("%02d", $i);
		$NossoNumero       = $NumeroDoDocumento;
		$ValorDoDesconto     = 0;
		$ValorDeJurosOuMulta = 0;
		$ValorAcrescimos     = 0;
		$ValorRecebido       = 0;
		$EspecieDaMoeda      = "0.00";
		$SituacaoDaParcela   = "0";
		$RemessaGravada = "N";
		$cmdSQL = 
		"INSERT INTO sce_titulo (
		TIT_ID,
		TIT_PARCELA,
		TIT_EVN_ID,
		TIT_CLN_ID,
		TIT_QUO_ID,
		TIT_FORMA_PAG,
		TIT_DATA_DOCUMENTO,
		TIT_NOSSO_NUMERO,
		TIT_NUMERO_DOCUMENTO,
		TIT_VALOR_TITULO,
		TIT_VALOR_DESCONTOS,
		TIT_VALOR_JUROS_MULTA,
		TIT_VALOR_ACRESCIMOS,
		TIT_VALOR_RECEBIDO,
		TIT_ESPECIE_MOEDA,
		TIT_STATUS,
		TIT_REM_GRAVADA) 
		VALUES ("
		.$UltimoTitulo
		.",  ".$Parcela
		.",  ".$globalEvento
		.",  ".$CodigoDoContribuinte
		.",  ".$QuotaNumero
		.", '".$FormaDePagamento."'"
		.",  ".$dataReferencia
		.", '".$NossoNumero."'"
		.", '".$NumeroDoDocumento."'"
		.",  ".$ValorParcela
		.",  ".$ValorDoDesconto
		.",  ".$ValorDeJurosOuMulta
		.",  ".$ValorAcrescimos
		.",  ".$ValorRecebido
		.", '".$EspecieDaMoeda."'"
		.", '".$SituacaoDaParcela."'"
		.", '".$RemessaGravada."'"
		.")";
		$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error()."(".$cmdSQL.")");
		if (mysql_error() == ""){
			echo("OK - Titulo ".$NumeroDoDocumento." incluido.<br>");
		}
		else{
			echo("<tr><td>ERRO na inclusãoo dos titulos:".mysql_error()."</td></tr>");
			echo("<tr><td>cmdSQL=".$cmdSQL."</td></tr>");
			echo("</table>");
			exit();
		}
	}
	echo("</table>");
	exit;
}	
if ($parametroOperacao == 'ExcluirAssociacao'){
	$QuotaNumero          = $_POST['Quota'];
	$CodigoDoContribuinte = $_POST['Contribuinte'];
	
# NAO EXISTINDO PARCELA PAGA - EXCLUIR	
	$sql = "SELECT * FROM sce_titulo "
	." WHERE TIT_CLN_ID = ".$CodigoDoContribuinte
	." AND   TIT_QUO_ID = ".$QuotaNumero
	//." AND   TIT_PARCELA = ".$gblEmpresa
	." AND   TIT_EVN_ID = ".$globalEvento 
	." AND   TIT_VALOR_RECEBIDO > 0";
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	if (mysql_num_rows($exe) == 0){
		$sql = "DELETE FROM sce_titulo "
		." WHERE TIT_CLN_ID = ".$CodigoDoContribuinte
		." AND   TIT_QUO_ID = ".$QuotaNumero
	//	." AND   TIT_PARCELA = ".$gblEmpresa
		." AND   TIT_EVN_ID = ".$globalEvento;
		$exe =  mysql_query($sql, $conn) or die(mysql_error());
		echo mysql_affected_rows()." parcelas excluídas<br>";	
		$sql = "DELETE FROM sce_quota "
		." WHERE QUO_ID_CONTRIBUINTE = ".$CodigoDoContribuinte
		." AND   QUO_ID_QUOTA   = ".$QuotaNumero
	//	." AND   QUO_ID_EMPRESA = ".$gblEmpresa
		." AND   QUO_ID_EVENTO  = ".$globalEvento;
		$exe =  mysql_query($sql, $conn) or die(mysql_error());
		echo mysql_affected_rows()." Quota excluída";
		exit;	
	}
# EXISTINDO PARCELA PAGA - ALTERAR O CODIGO DO CONTRIBUINTE PARA ZEROS	
	$sql = "UPDATE sce_titulo SET TIT_CLN_ID = 0 "
	." WHERE TIT_CLN_ID = ".$CodigoDoContribuinte
	." AND   TIT_QUO_ID = ".$QuotaNumero
	//." AND   TIT_EMP_ID = ".$gblEmpresa
	." AND   TIT_EVN_ID = ".$globalEvento;
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	echo mysql_affected_rows()." registros alterados em Titulos<br>";
	
	$sql = "UPDATE sce_quota SET QUO_ID_CONTRIBUINTE = 0 "
	." WHERE QUO_ID_CONTRIBUINTE = ".$CodigoDoContribuinte
	." AND   QUO_ID_QUOTA   = ".$QuotaNumero
	//." AND   QUO_ID_EMPRESA = ".$gblEmpresa
	." AND   QUO_ID_EVENTO  = ".$globalEvento;
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	echo mysql_affected_rows()." registros alterados em Quotas";
	exit;
}
?>