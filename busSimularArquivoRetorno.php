<?php
set_time_limit(0); 
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
$tipoDeArquivo = substr($arquivoOrigem,-3);

$arquivoRetorno = "uploads//".$arquivoOrigem;
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}
$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
$linha = fgets($arquivo, 4096);
fclose($arquivo);

#----- SIMULANDO RETORNO - COBRANÇA BANCO DO BRASIL -----#
if (substr($arquivoOrigem,0,3) == "CBR" or substr($arquivoOrigem,0,3) == "IED"){

	$arquivoRetorno = "uploads//".$arquivoOrigem;
	if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}
	$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
	$reglido = 0;
	$Soma_Valor_Titulos = 0;
	$Soma_Valor_Tarifas = 0;
	$Soma_Valor_Juros_Multas_Encargos = 0;
	$Soma_Valor_Creditado = 0;
	$Soma_Valor_Pago = 0;

	$totalRegistros = 0;
	$Total_Registros_Com_Erro = 0;
	$Soma_Valor_com_erro = 0;
	$Total_Registros_Duplicidade = 0;

	$nomeArquivo = $arquivoRetorno."SimulandoRetorno.html";	
	$arquivoHtml  = fopen($nomeArquivo, "w");
	fwrite($arquivoHtml, "<html><head><title></title>\n");
	fwrite($arquivoHtml, "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	fwrite($arquivoHtml, "<style>\n");
	fwrite($arquivoHtml, "* { text-decoration: none;}\n");
	fwrite($arquivoHtml, "a { color: #6B8D58; }\n");
	fwrite($arquivoHtml, ".tbConsulta  	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:900px;}\n");
	fwrite($arquivoHtml, "#divTitulo{\n");
	fwrite($arquivoHtml, " text-align			: center;\n");
	fwrite($arquivoHtml, " border				: 1px solid #f9f9f9;\n");
	fwrite($arquivoHtml, " background			: #ccc;\n");
	fwrite($arquivoHtml, "}\n");
	fwrite($arquivoHtml, "</style>\n");
	fwrite($arquivoHtml, "<div id='divTitulo'><h4>SIMULANDO RETORNO COBRANÇA BB - ".$arquivoOrigem."</h4></div>\n");

	while (!feof($arquivo)){
		$linha = fgets($arquivo, 4096);
		$reglido += 1;

		$TipoDeRegistro = substr($linha,8-1,1);

		if ($TipoDeRegistro === '0'){						// HEADER DO ARQUIVO

			$DataDoArquivo = substr($linha, 144-1,8);
			$dataArquivoINV = substr($DataDoArquivo,5-1,4)."-".substr($DataDoArquivo,3-1,2)."-".substr($DataDoArquivo,1-1,2);
			$NSA = substr($linha, 158-1,6);

			$BAN_ID = 1; 
			$NomeDoBanco = "BANCO DO BRASIL";
			$BancoCliente = "T";
			$BancoDetalhe = 'U';
			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Data do arquivo</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>NSA</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Banco</th>\n");
			fwrite($arquivoHtml, "</td>\n");
			fwrite($arquivoHtml, "<tr><td>".substr($DataDoArquivo,1-1,2)."/".substr($DataDoArquivo,3-1,2)."/".substr($DataDoArquivo,5-1,4)."</td><td>".$NSA."</td><td>".$NomeDoBanco."</td></tr>\n");
			fwrite($arquivoHtml, "</table>\n");
			fwrite($arquivoHtml, "<br>\n");
			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<thead>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<th align='left'  width='05%'>Parcela</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='10%'>Documento</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='40%'>Contribuinte</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Valor pago</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='05%'>Situação</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Controle</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='20%'>Observação</th>\n");
			fwrite($arquivoHtml, "</tr>\n");
			fwrite($arquivoHtml, "</thead>\n");
		}

		if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === $BancoCliente ) {
			$Soma_Valor_Tarifas += substr($linha,199-1,15) / 100;
			$NossoNumero = substr($linha,38-1,17);
			$Campanha = substr($NossoNumero,8-1,3);
			$Contrato = substr($NossoNumero,1-1,7);
			$Parcela = substr($NossoNumero,16-1,2);
			$NumeroDocumento = substr($NossoNumero,8-1,10);
			$ValorDoDesconto = substr($linha, 189-1,25) / 100;
			$Quota = substr($linha,48-1,5);
			$Soma_Valor_Titulos += substr($linha,82-1,15) / 100;
		}

		if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === $BancoDetalhe ) {
			$totalRegistros += 1;
			$Valor_Pago = substr($linha,78-1,15) / 100;
			$Valor_Creditado = substr($linha,93-1,15) / 100;
			$Valor_Juros_Multas_Encargos = substr($linha,18-1,15) / 100;
			$Situacao = substr($linha,211-1,3);
			$Soma_Valor_Pago += $Valor_Pago;
			$Soma_Valor_Creditado += $Valor_Creditado;
			$Soma_Valor_Juros_Multas_Encargos += $Valor_Juros_Multas_Encargos;
			$valorTitulo = ereg_replace(",", ".", $Valor_Pago);
			$dataPagamento = substr($linha,150-1,4)."-".substr($linha,148-1,2)."-".substr($linha,146-1,2);
			$sqlTitulo = ""
			."SELECT "
			."	 TIT_ID as TituloChave,"
			."	 TIT_EVN_ID as Campanha,"
			."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
			."	 SUBSTR(TIT_NUMERO_DOCUMENTO,7,2) as Parcela,"
			."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
			."	 TIT_STATUS as Situacao,"
			."	 REMESSA_ID as Remessa,"
			."	 TIT_ID as ParcelaID,"
			."	 TIT_CLN_ID as Contribuinte,"
			."	 TIT_QUO_ID as Quota,"
			."   CTB_NOME as Nome"
			." FROM sce_titulo, sce_contribuinte"
			." WHERE TIT_EVN_ID = ".$globalEvento
			."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
			."  AND TIT_VALOR_RECEBIDO = 0"
			."  AND TIT_VALOR_TITULO = ".$valorTitulo
			."  AND CTB_ID = TIT_CLN_ID "
			."LIMIT 1";
			$execTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
			$rowTitulo = mysql_num_rows($execTitulo);
			$rowTitulo = mysql_fetch_assoc($execTitulo);
			$controle = $rowTitulo['NumeroDocumento'];

			if ($controle != null){

				$valor = $Valor_Pago;
				if(!strpos($valor,".")&&(strpos($valor,",")))
				$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
				$TIT_VALOR_RECEBIDO = $valor;

				$valor = $ValorDoDesconto;
				if(!strpos($valor,".")&&(strpos($valor,",")))
				$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
				$TIT_VALOR_DESCONTOS = $valor;
				$TIT_ID = $rowTitulo['TituloChave'];
				
				$wssValor = $Valor_Pago - $ValorDoDesconto;
				$valor = $wssValor;
				if(!strpos($valor,".")&&(strpos($valor,",")))
				$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
				$TIT_VALOR_RECEBIDO = $valor;
			}
			if ($controle == null){
				$controle = 'NAO BAIXADO';
				$Total_Registros_Com_Erro += 1;
				$Soma_Valor_com_erro += $Valor_Pago;
			};

			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
			fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='right'>".$Situacao."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='right'>".$controle."</td>\n");
			$observacao = "";
			if ($NumeroDocumento != $controle) { $observacao = "Parcela fora de sequencia "; };
			fwrite($arquivoHtml, "<td width='10%' align='right'>".$observacao."</td>\n");
			fwrite($arquivoHtml, "<tr>\n");
		}
	}
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivo);
	fwrite($arquivoHtml, "<br>");
	fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total de titulos</td><td width='20%'>".sprintf("%01.0f", $totalRegistros)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos titulos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Titulos)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das tarifas</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Tarifas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores pagos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Pago)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores creditados</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Creditado)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das multas e encargos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Juros_Multas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivoHtml);

	//* SALVAR CONTROLE E RESUMO DO ARQUIVO PROCESSADO
	$valor = $Soma_Valor_Titulos;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Titulos = $valor;

	$valor = $Soma_Valor_Creditado;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Creditado = $valor;

	$valor = $Soma_Valor_Tarifas;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Tarifas = $valor;

	$valor = $Soma_Valor_com_erro;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_com_erro = $valor;

	$nomeArquivoModelo = $nomeArquivo;
	$arquivoModelo = fopen($nomeArquivoModelo , "rb");
	$arquivoNovo   = fread($arquivoModelo,1000000);
	fclose($arquivoModelo);

	echo($arquivoNovo);
}
#----- SIMULANDO RETORNO - DEBITO BB -----#
if (substr($arquivoOrigem,0,6) == "DBT BB"){
	$arquivoRetorno = "uploads//".$arquivoOrigem;
	if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}
	$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
	$reglido = 0;
	$Soma_Valor_Titulos = 0;
	$Soma_Valor_Tarifas = 0;
	$Soma_Valor_Juros_Multas_Encargos = 0;
	$Soma_Valor_Creditado = 0;
	$Soma_Valor_Pago = 0;

	$totalRegistros = 0;
	$Total_Registros_Com_Erro = 0;
	$Soma_Valor_com_erro = 0;
	$Total_Registros_Duplicidade = 0;

	$nomeArquivo = $arquivoRetorno."SimulandoRetorno.html";	
	$arquivoHtml  = fopen($nomeArquivo, "w");
	fwrite($arquivoHtml, "<html><head><title></title>\n");
	fwrite($arquivoHtml, "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	fwrite($arquivoHtml, "<style>\n");
	fwrite($arquivoHtml, "* { text-decoration: none;}\n");
	fwrite($arquivoHtml, "a { color: #6B8D58; }\n");
	fwrite($arquivoHtml, ".tbConsulta  	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:900px;}\n");
	fwrite($arquivoHtml, "#divTitulo{\n");
	fwrite($arquivoHtml, " text-align			: center;\n");
	fwrite($arquivoHtml, " border				: 1px solid #f9f9f9;\n");
	fwrite($arquivoHtml, " background			: #ccc;\n");
	fwrite($arquivoHtml, "}\n");
	fwrite($arquivoHtml, "</style>\n");
	fwrite($arquivoHtml, "<div id='divTitulo'><h4>SIMULANDO RETORNO DEBITO BB - ".$arquivoOrigem."</h4></div>\n");
	while (!feof($arquivo)){
		$linha = fgets($arquivo, 4096);
		$reglido += 1;
		$TipoDeRegistro = substr($linha,1-1,1);
		if ($TipoDeRegistro === 'A'){						// HEADER DO ARQUIVO
			$DataDoArquivo = substr($linha, 66-1,8);
			$dataArquivoINV = substr($DataDoArquivo,1-1,4)."-".substr($DataDoArquivo,5-1,2)."-".substr($DataDoArquivo,7-1,2);
			$NSA = substr($linha, 74-1,6);
			$BAN_ID = 1; 
			$NomeDoBanco = "BANCO DO BRASIL";
			$BancoDetalhe = 'F';
			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Data do arquivo</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>NSA</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Banco</th>\n");
			fwrite($arquivoHtml, "</td>\n");
			fwrite($arquivoHtml, "<tr><td>".substr($DataDoArquivo,7-1,2)."/".substr($DataDoArquivo,5-1,2)."/".substr($DataDoArquivo,1-1,4)."</td><td>".$NSA."</td><td>".$NomeDoBanco."</td></tr>\n");
			fwrite($arquivoHtml, "</table>\n");
			fwrite($arquivoHtml, "<br>\n");
			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<thead>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<th align='left'  width='05%'>Parcela</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='10%'>Documento</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='45%'>Contribuinte</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Valor pago</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Situação</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='20%'>Controle</th>\n");
			fwrite($arquivoHtml, "</tr>\n");
			fwrite($arquivoHtml, "</thead>\n");
		}
		if (substr($linha,1-1,1) === "F") {
			$Soma_Valor_Tarifas += 0;
			$NossoNumero = substr($linha,70-1,17);
			$Campanha = substr($NossoNumero,8-1,3);
			$Contrato = substr($NossoNumero,1-1,7);
			$Parcela = substr($NossoNumero,16-1,2);
			$NumeroDocumento = substr($NossoNumero,8-1,10);
			$ValorDoDesconto = 0;
			$Quota = substr($linha,5-1,5);
			$Soma_Valor_Titulos += substr($linha,53-1,15) / 100;
			$dataPagamento = substr($linha,45-1,4)."-".substr($linha,49-1,2)."-".substr($linha,51-1,2);
			$totalRegistros += 1;
			$codigoRetorno = substr($linha,68-1,2);
			if ($codigoRetorno == '00' or $codigoRetorno == '31'){
				$Valor_Pago = substr($linha,53-1,15) / 100;
				$Valor_Creditado = substr($linha,53-1,15) / 100;
				$Valor_Juros_Multas_Encargos = 0;
				$Situacao = substr($linha,150-1,1);
				$Soma_Valor_Pago += $Valor_Pago;
				$Soma_Valor_Creditado += $Valor_Creditado;
				$Soma_Valor_Juros_Multas_Encargos += $Valor_Juros_Multas_Encargos;
				$valorTitulo = $Valor_Pago;
				$sqlTitulo = ""
				."SELECT "
				."	 TIT_ID as TituloChave,"
				."	 TIT_EVN_ID as Campanha,"
				."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
				."	 SUBSTR(TIT_NUMERO_DOCUMENTO,7,2) as Parcela,"
				."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
				."	 TIT_STATUS as Situacao,"
				."	 REMESSA_ID as Remessa,"
				."	 TIT_ID as ParcelaID,"
				."	 TIT_CLN_ID as Contribuinte,"
				."	 TIT_QUO_ID as Quota,"
				."   CTB_NOME as Nome"
				." FROM sce_titulo, sce_contribuinte"
				." WHERE TIT_EVN_ID = ".$globalEvento
				."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
				."  AND TIT_VALOR_RECEBIDO = 0"
				."  AND TIT_VALOR_TITULO = ".$valorTitulo
				."  AND CTB_ID = TIT_CLN_ID "
				."LIMIT 1";
				$execTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
				$rowTitulo = mysql_num_rows($execTitulo);
				$rowTitulo = mysql_fetch_assoc($execTitulo);
				$controle = $rowTitulo['NumeroDocumento'];
				if ($controle != null){
					$valor = $Valor_Pago;
					if(!strpos($valor,".")&&(strpos($valor,",")))
					$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
					$TIT_VALOR_RECEBIDO = $valor;
					$valor = $ValorDoDesconto;
					if(!strpos($valor,".")&&(strpos($valor,",")))
					$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
					$TIT_VALOR_DESCONTOS = $valor;
					$TIT_ID = $rowTitulo['TituloChave'];
				}
				if ($controle == null){
					$controle = 'NAO BAIXADO';
					$Total_Registros_Com_Erro += 1;
					$Soma_Valor_com_erro += $Valor_Pago;
				};
				fwrite($arquivoHtml, "<tr>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
				fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$Situacao."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$controle."</td>\n");
				fwrite($arquivoHtml, "<tr>\n");
			}
	//***** REGISTRO RETORNADO COM ERRO 	
			if (($codigoRetorno != '00') && ($codigoRetorno != '31')){
				$sqlTitulo = ""
				."SELECT "
				."	 TIT_ID as TituloChave,"
				."	 TIT_EVN_ID as Campanha,"
				."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
				."	 SUBSTR(TIT_NUMERO_DOCUMENTO,7,2) as Parcela,"
				."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
				."	 TIT_STATUS as Situacao,"
				."	 REMESSA_ID as Remessa,"
				."	 TIT_ID as ParcelaID,"
				."	 TIT_CLN_ID as Contribuinte,"
				."	 TIT_QUO_ID as Quota,"
				."   CTB_NOME as Nome"
				." FROM sce_titulo, sce_contribuinte"
				." WHERE TIT_EVN_ID = ".$globalEvento
				."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
				."  AND TIT_VALOR_RECEBIDO = 0"
				."  AND CTB_ID = TIT_CLN_ID "
				."LIMIT 1";
				$exeTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
				$rowTitulo = mysql_num_rows($exeTitulo);
				$rowTitulo = mysql_fetch_assoc($exeTitulo);			
			
				$totalRegistros += 1;
				$Total_Registros_Com_Erro += 1;
				$Soma_Valor_com_erro += substr($linha,53-1,15) / 100;
				fwrite($arquivoHtml, "<tr>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
				fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", "0")."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$codigoRetorno."</td>\n");
				$mensagemDeErro = mensagemDeErro($codigoRetorno);
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$mensagemDeErro."</td>\n");
				fwrite($arquivoHtml, "<tr>\n");
			}
		}
	}
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivo);
	fwrite($arquivoHtml, "<br>");
	fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total de titulos</td><td width='20%'>".sprintf("%01.0f", $totalRegistros)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos titulos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Titulos)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das tarifas</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Tarifas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores pagos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Pago)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores creditados</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Creditado)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das multas e encargos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Juros_Multas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total com erro</td><td width='20%'>".sprintf("%01.0f", $Total_Registros_Com_Erro)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma com erro</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_com_erro)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivoHtml);

	//* SALVAR CONTROLE E RESUMO DO ARQUIVO PROCESSADO
	$valor = $Soma_Valor_Titulos;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Titulos = $valor;

	$valor = $Soma_Valor_Creditado;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Creditado = $valor;

	$valor = $Soma_Valor_Tarifas;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Tarifas = $valor;

	$valor = $Soma_Valor_com_erro;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_com_erro = $valor;

	$nomeArquivoModelo = $nomeArquivo;
	$arquivoModelo = fopen($nomeArquivoModelo , "rb");
	$arquivoNovo   = fread($arquivoModelo,1000000);
	fclose($arquivoModelo);
	echo($arquivoNovo);
}
#----- SIMULANDO RETORNO - DEBITO BRB -----#
if (substr($arquivoOrigem,0,7) == "DBT BRB"){
	$arquivoRetorno = "uploads//".$arquivoOrigem;
	if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}

	$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
	$reglido = 0;
	$Soma_Valor_Titulos = 0;
	$Soma_Valor_Tarifas = 0;
	$Soma_Valor_Juros_Multas_Encargos = 0;
	$Soma_Valor_Creditado = 0;
	$Soma_Valor_Pago = 0;

	$totalRegistros = 0;
	$Total_Registros_Com_Erro = 0;
	$Soma_Valor_com_erro = 0;
	$Total_Registros_Duplicidade = 0;

	$nomeArquivo = $arquivoRetorno."SimulandoRetorno.html";	
	$arquivoHtml  = fopen($nomeArquivo, "w");
	fwrite($arquivoHtml, "<html><head><title></title>\n");
	fwrite($arquivoHtml, "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	fwrite($arquivoHtml, "<style>\n");
	fwrite($arquivoHtml, "* { text-decoration: none;}\n");
	fwrite($arquivoHtml, "a { color: #6B8D58; }\n");
	fwrite($arquivoHtml, ".tbConsulta  	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:900px;}\n");
	fwrite($arquivoHtml, "#divTitulo{\n");
	fwrite($arquivoHtml, " text-align			: center;\n");
	fwrite($arquivoHtml, " border				: 1px solid #f9f9f9;\n");
	fwrite($arquivoHtml, " background			: #ccc;\n");
	fwrite($arquivoHtml, "}\n");
	fwrite($arquivoHtml, "</style>\n");
	fwrite($arquivoHtml, "<div id='divTitulo'><h4>SIMULANDO RETORNO DEBITO BRB - ".$arquivoOrigem."</h4></div>\n");

	while (!feof($arquivo)){
		$linha = fgets($arquivo, 4096);
		$reglido += 1;

		$TipoDeRegistro = substr($linha,8-1,1);

		if ($TipoDeRegistro === '0'){						// HEADER DO ARQUIVO

			$DataDoArquivo = substr($linha, 144-1,8);
			$dataArquivoINV = substr($DataDoArquivo,5-1,4)."-".substr($DataDoArquivo,3-1,2)."-".substr($DataDoArquivo,1-1,2);
			$NSA = substr($linha, 158-1,6);
			$BancoCliente = "A";
			$BAN_ID = 2; 
			$NomeDoBanco = "BRB";
			$BancoDetalhe = 'B';

			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Data do arquivo</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>NSA</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Banco</th>\n");
			fwrite($arquivoHtml, "</td>\n");
			fwrite($arquivoHtml, "<tr><td>".substr($DataDoArquivo,1-1,2)."/".substr($DataDoArquivo,3-1,2)."/".substr($DataDoArquivo,5-1,4)."</td><td>".$NSA."</td><td>".$NomeDoBanco."</td></tr>\n");
			fwrite($arquivoHtml, "</table>\n");
			fwrite($arquivoHtml, "<br>\n");
			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<thead>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<th align='left'  width='05%'>Parcela</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='10%'>Documento</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='45%'>Contribuinte</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Valor pago</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Situação</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='20%'>Controle</th>\n");
			fwrite($arquivoHtml, "</tr>\n");
			fwrite($arquivoHtml, "</thead>\n");
		}

		if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === $BancoCliente ) {
			$Soma_Valor_Tarifas += substr($linha,199-1,15) / 100;
			$NossoNumero = substr($linha,84-1,17);
			$Campanha    = substr($linha,84-1.3);
			$Contrato    = substr($NossoNumero,1-1,7);
			$Parcela     = substr($linha,92-1,2);
			$NumeroDocumento = substr($linha,84-1,10);
			$ValorDoDesconto = 0;
			$Quota = substr($NossoNumero,4-1,5);
			$Soma_Valor_Titulos += substr($linha,120-1,15) / 100;
			$Situacao = substr($linha,230-1,3);
			$dataPagamento = substr($linha,98-1,4)."-".substr($linha,96-1,2)."-".substr($linha,94-1,2);
			$codigoRetorno = substr($linha,230-1,3);

	//***** REGISTRO RETORNADO COM ERRO
			if ($codigoRetorno != '000'){
				$sqlTitulo = ""
				."SELECT "
				."	 TIT_ID as TituloChave,"
				."	 TIT_EVN_ID as Campanha,"
				."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
				."	 SUBSTR(TIT_NUMERO_DOCUMENTO,7,2) as Parcela,"
				."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
				."	 TIT_STATUS as Situacao,"
				."	 REMESSA_ID as Remessa,"
				."	 TIT_ID as ParcelaID,"
				."	 TIT_CLN_ID as Contribuinte,"
				."	 TIT_QUO_ID as Quota,"
				."   CTB_NOME as Nome"
				." FROM sce_titulo, sce_contribuinte"
				." WHERE TIT_EVN_ID = ".$globalEvento
				."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
				."  AND TIT_VALOR_RECEBIDO = 0"
				."  AND CTB_ID = TIT_CLN_ID "
				."LIMIT 1";
				$exeTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
				$rowTitulo = mysql_num_rows($exeTitulo);
				$rowTitulo = mysql_fetch_assoc($exeTitulo);			
			
				$totalRegistros += 1;
				$Total_Registros_Com_Erro += 1;
				$Soma_Valor_com_erro += substr($linha,120-1,15) / 100;
				
				fwrite($arquivoHtml, "<tr>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
				fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", "0")."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$Situacao."</td>\n");
				$mensagemDeErro = mensagemDeErro($codigoRetorno);
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$mensagemDeErro."</td>\n");				
				fwrite($arquivoHtml, "<tr>\n");
			}
		}
		if (substr($linha,8-1,1) === "3" and substr($linha,14-1,1) === $BancoDetalhe ) {
			$totalRegistros += 1;
			$Valor_Pago = substr($linha,136-1,15) / 100;
			$Valor_Creditado = $Valor_Pago;
			$Valor_Juros_Multas_Encargos = 0;
			$Soma_Valor_Pago += $Valor_Pago;
			$Soma_Valor_Creditado += $Valor_Creditado;
			$Soma_Valor_Juros_Multas_Encargos += $Valor_Juros_Multas_Encargos;
			$valorTitulo = $Valor_Pago;

			$sqlTitulo = ""
			."SELECT "
			."	 TIT_ID as TituloChave,"
			."	 TIT_EVN_ID as Campanha,"
			."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
			."	 SUBSTR(TIT_NUMERO_DOCUMENTO,9,2) as Parcela,"
			."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
			."	 TIT_STATUS as Situacao,"
			."	 REMESSA_ID as Remessa,"
			."	 TIT_ID as ParcelaID,"
			."	 TIT_CLN_ID as Contribuinte,"
			."	 TIT_QUO_ID as Quota,"
			."   CTB_NOME as Nome"
			." FROM sce_titulo, sce_contribuinte"
			." WHERE TIT_EVN_ID = ".$globalEvento
			."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
			."  AND TIT_VALOR_RECEBIDO = 0"
			."  AND TIT_VALOR_TITULO = ".$valorTitulo
			."  AND CTB_ID = TIT_CLN_ID "
			."LIMIT 1";

			$execTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
			$rowTitulo = mysql_num_rows($execTitulo);
			$rowTitulo = mysql_fetch_assoc($execTitulo);
			$controle = $rowTitulo['NumeroDocumento'];

	//***** INICIO BAIXAR TITULO PAGO
			if ($controle != null){

				$valor = $Valor_Pago;
				if(!strpos($valor,".")&&(strpos($valor,",")))
				$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
				$TIT_VALOR_RECEBIDO = $valor;

				$valor = $ValorDoDesconto;
				if(!strpos($valor,".")&&(strpos($valor,",")))
				$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
				$TIT_VALOR_DESCONTOS = $valor;
				$TIT_ID = $rowTitulo['TituloChave'];
				$status = '9';
			}
			if ($controle == null){
				$controle = 'NAO BAIXADO';
				$Total_Registros_Com_Erro += 1;
				$Soma_Valor_com_erro += $Valor_Pago;
			};

			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
			fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='right'>".$Situacao."</td>\n");
			fwrite($arquivoHtml, "<td width='10%' align='right'>".$controle."</td>\n");
			fwrite($arquivoHtml, "<tr>\n");
		}
	}
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivo);
	fwrite($arquivoHtml, "<br>");
	fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total de titulos</td><td width='20%'>".sprintf("%01.0f", $totalRegistros)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos titulos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Titulos)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das tarifas</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Tarifas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores pagos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Pago)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores creditados</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Creditado)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das multas e encargos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Juros_Multas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total com erro</td><td width='20%'>".sprintf("%01.0f", $Total_Registros_Com_Erro)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma com erro</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_com_erro)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivoHtml);

	//* SALVAR CONTROLE E RESUMO DO ARQUIVO PROCESSADO

	$valor = $Soma_Valor_Titulos;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Titulos = $valor;

	$valor = $Soma_Valor_Creditado;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Creditado = $valor;

	$valor = $Soma_Valor_Tarifas;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_Tarifas = $valor;

	$valor = $Soma_Valor_com_erro;
	if(!strpos($valor,".")&&(strpos($valor,",")))
	$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
	$Soma_Valor_com_erro = $valor;

	$nomeArquivoModelo = $nomeArquivo;
	$arquivoModelo = fopen($nomeArquivoModelo , "rb");
	$arquivoNovo   = fread($arquivoModelo,1000000);
	fclose($arquivoModelo);
	echo($arquivoNovo);
}
#----- SIMULANDO RETORNO - DEBITO CEF -----#
if (substr($arquivoOrigem,0,7) == "DBT CEF"){
	$arquivoRetorno = "uploads//".$arquivoOrigem;
	if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "uploads\\".$arquivoOrigem;}

	$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
	$reglido = 0;
	$Soma_Valor_Titulos = 0;
	$Soma_Valor_Tarifas = 0;
	$Soma_Valor_Juros_Multas_Encargos = 0;
	$Soma_Valor_Creditado = 0;
	$Soma_Valor_Pago = 0;

	$totalRegistros = 0;
	$Total_Registros_Com_Erro = 0;
	$Soma_Valor_com_erro = 0;
	$Total_Registros_Duplicidade = 0;

	$nomeArquivo = $arquivoRetorno."SimulandoRetorno.html";	
	$arquivoHtml  = fopen($nomeArquivo, "w");
	fwrite($arquivoHtml, "<html><head><title></title>\n");
	fwrite($arquivoHtml, "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	fwrite($arquivoHtml, "<style>\n");
	fwrite($arquivoHtml, "* { text-decoration: none;}\n");
	fwrite($arquivoHtml, "a { color: #6B8D58; }\n");
	fwrite($arquivoHtml, ".tbConsulta  	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:900px;}\n");
	fwrite($arquivoHtml, "#divTitulo{\n");
	fwrite($arquivoHtml, " text-align			: center;\n");
	fwrite($arquivoHtml, " border				: 1px solid #f9f9f9;\n");
	fwrite($arquivoHtml, " background			: #ccc;\n");
	fwrite($arquivoHtml, "}\n");
	fwrite($arquivoHtml, "</style>\n");
	fwrite($arquivoHtml, "<div id='divTitulo'><h4>SIMULANDO RETORNO DEBITO CEF - ".$arquivoOrigem."</h4></div>\n");

	while (!feof($arquivo)){
		$linha = fgets($arquivo, 4096);
		$reglido += 1;

		$TipoDeRegistro = substr($linha,1-1,1);

		if ($TipoDeRegistro === 'A'){						// HEADER DO ARQUIVO

			$DataDoArquivo = substr($linha, 66-1,8);
			$dataArquivoINV = substr($DataDoArquivo,1-1,4)."-".substr($DataDoArquivo,5-1,2)."-".substr($DataDoArquivo,7-1,2);
			$BAN_ID = 3; $NomeDoBanco = "CAIXA";
			$NSA = substr($linha, 74-1,6);

			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Data do arquivo</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>NSA</th>\n");
			fwrite($arquivoHtml, "<td align='left'  width='10%'>Banco</th>\n");
			fwrite($arquivoHtml, "</td>\n");
			fwrite($arquivoHtml, "<tr><td>".substr($DataDoArquivo,7-1,2)."/".substr($DataDoArquivo,5-1,2)."/".substr($DataDoArquivo,1-1,4)."</td><td>".$NSA."</td><td>".$NomeDoBanco."</td></tr>\n");
			fwrite($arquivoHtml, "</table>\n");
			fwrite($arquivoHtml, "<br>\n");
			fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
			fwrite($arquivoHtml, "<thead>\n");
			fwrite($arquivoHtml, "<tr>\n");
			fwrite($arquivoHtml, "<th align='left'  width='05%'>Parcela</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='10%'>Documento</th>\n");
			fwrite($arquivoHtml, "<th align='left'  width='45%'>Contribuinte</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Valor pago</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='10%'>Situação</th>\n");
			fwrite($arquivoHtml, "<th align='right' width='20%'>Controle</th>\n");
			fwrite($arquivoHtml, "</tr>\n");
			fwrite($arquivoHtml, "</thead>\n");
		}

		if ($TipoDeRegistro === 'F'){
			$totalRegistros += 1;
			$codigoRetorno = substr($linha,68-1,2);
			$Campanha    = substr($linha,70-1,3);
			$Parcela     = substr($linha,78-1,2);
			$NumeroDocumento = substr($linha,70-1,10);
			$Quota = substr($linha,73-1,5);
			$Soma_Valor_Titulos += substr($linha,53-1,15) / 100;
			$Soma_Valor_Tarifas += 0;
			$Situacao = substr($linha,150-1,1);
				
			if ($codigoRetorno == '00'){
				$ValorDoDesconto = 0;
				$Valor_Pago = substr($linha,53-1,15) / 100;
				$Valor_Creditado = substr($linha,53-1,15) / 100;
				$Valor_Juros_Multas_Encargos = 0;
				$Soma_Valor_Pago += $Valor_Pago;
				$Soma_Valor_Creditado += $Valor_Creditado;
				$Soma_Valor_Juros_Multas_Encargos += $Valor_Juros_Multas_Encargos;
				$valorTitulo = $Valor_Pago;
				$sqlTitulo = ""
				."SELECT "
				."	 TIT_ID as TituloChave,"
				."	 TIT_EVN_ID as Campanha,"
				."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
				."	 SUBSTR(TIT_NUMERO_DOCUMENTO,7,2) as Parcela,"
				."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
				."	 TIT_STATUS as Situacao,"
				."	 REMESSA_ID as Remessa,"
				."	 TIT_ID as ParcelaID,"
				."	 TIT_CLN_ID as Contribuinte,"
				."	 TIT_QUO_ID as Quota,"
				."   CTB_NOME as Nome"
				." FROM sce_titulo, sce_contribuinte"
				." WHERE TIT_EVN_ID = ".$globalEvento
				."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
				."  AND TIT_VALOR_RECEBIDO = 0"
				."  AND TIT_VALOR_TITULO = ".$valorTitulo
				."  AND CTB_ID = TIT_CLN_ID "
				."LIMIT 1";
				$execTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
				$rowTitulo = mysql_num_rows($execTitulo);
				$rowTitulo = mysql_fetch_assoc($execTitulo);
				$controle = $rowTitulo['NumeroDocumento'];
	//***** INICIO BAIXAR TITULO PAGO
				if ($controle != null){
					$valor = $Valor_Pago;
					if(!strpos($valor,".")&&(strpos($valor,",")))
					$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
					$TIT_VALOR_RECEBIDO = $valor;
					$valor = $ValorDoDesconto;
					if(!strpos($valor,".")&&(strpos($valor,",")))
					$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
					$TIT_VALOR_DESCONTOS = $valor;
					$TIT_ID = $rowTitulo['TituloChave'];
				}
				if ($controle == null){
					$controle = 'NAO BAIXADO';
					$Total_Registros_Com_Erro += 1;
					$Soma_Valor_com_erro += $Valor_Pago;
				};
				fwrite($arquivoHtml, "<tr>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
				fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", $Valor_Pago)."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$codigoRetorno."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$controle."</td>\n");
				fwrite($arquivoHtml, "<tr>\n");
			}
			if ($codigoRetorno != '00'){
				$sqlTitulo = ""
				."SELECT "
				."	 TIT_ID as TituloChave,"
				."	 TIT_EVN_ID as Campanha,"
				."   TIT_NUMERO_DOCUMENTO as NumeroDocumento,"
				."	 SUBSTR(TIT_NUMERO_DOCUMENTO,7,2) as Parcela,"
				."	 TIT_VALOR_RECEBIDO as ValorRecebido,"
				."	 TIT_STATUS as Situacao,"
				."	 REMESSA_ID as Remessa,"
				."	 TIT_ID as ParcelaID,"
				."	 TIT_CLN_ID as Contribuinte,"
				."	 TIT_QUO_ID as Quota,"
				."   CTB_NOME as Nome"
				." FROM sce_titulo, sce_contribuinte"
				." WHERE TIT_EVN_ID = ".$globalEvento
				."  AND SUBSTR(TIT_NOSSO_NUMERO,11,5) = ".$Quota
				."  AND TIT_VALOR_RECEBIDO = 0"
				."  AND CTB_ID = TIT_CLN_ID "
				."LIMIT 1";
				$execTitulo =  mysql_query($sqlTitulo, $conn) or die(mysql_error());
				$rowTitulo = mysql_num_rows($execTitulo);
				$rowTitulo = mysql_fetch_assoc($execTitulo);
			
				$totalRegistros += 1;
				$Total_Registros_Com_Erro += 1;
				$Soma_Valor_com_erro += substr($linha,53-1,15) / 100;
				fwrite($arquivoHtml, "<tr>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$Parcela."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='left'>".$NumeroDocumento."</td>\n");
				fwrite($arquivoHtml, "<td width='30%' align='left'>".$rowTitulo['Nome']."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".sprintf("%01.2f", "0")."</td>\n");
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$codigoRetorno."</td>\n");
				$mensagemDeErro = mensagemDeErro($codigoRetorno);
				fwrite($arquivoHtml, "<td width='10%' align='right'>".$mensagemDeErro."</td>\n");				
				fwrite($arquivoHtml, "<tr>\n");
			}
		}
	}
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivo);
	fwrite($arquivoHtml, "<br>");
	fwrite($arquivoHtml, "<table class='tbConsulta' align='center'>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total de titulos</td><td width='20%'>".sprintf("%01.0f", $totalRegistros)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos titulos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Titulos)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das tarifas</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Tarifas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores pagos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Pago)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma dos valores creditados</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Creditado)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma das multas e encargos</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_Juros_Multas)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Total com erro</td><td width='20%'>".sprintf("%01.0f", $Total_Registros_Com_Erro)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "<tr><td width='20%'>Soma com erro</td><td width='20%'>".sprintf("%01.2f", $Soma_Valor_com_erro)."</td><td  width='60%'></td></tr>\n");
	fwrite($arquivoHtml, "</table>\n");
	fclose($arquivoHtml);

	//* SALVAR CONTROLE E RESUMO DO ARQUIVO PROCESSADO

		$valor = $Soma_Valor_Titulos;
		if(!strpos($valor,".")&&(strpos($valor,",")))
		$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
		$Soma_Valor_Titulos = $valor;

		$valor = $Soma_Valor_Creditado;
		if(!strpos($valor,".")&&(strpos($valor,",")))
		$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
		$Soma_Valor_Creditado = $valor;

		$valor = $Soma_Valor_Tarifas;
		if(!strpos($valor,".")&&(strpos($valor,",")))
		$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
		$Soma_Valor_Tarifas = $valor;

		$valor = $Soma_Valor_com_erro;
		if(!strpos($valor,".")&&(strpos($valor,",")))
		$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
		$Soma_Valor_com_erro = $valor;

	$nomeArquivoModelo = $nomeArquivo;
	$arquivoModelo = fopen($nomeArquivoModelo , "rb");
	$arquivoNovo   = fread($arquivoModelo,1000000);
	fclose($arquivoModelo);
	echo($arquivoNovo);
}
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