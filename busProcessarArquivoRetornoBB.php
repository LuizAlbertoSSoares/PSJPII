<?php
set_time_limit(0); 
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

$nomeArquivo = $arquivoRetorno.".html";
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
fwrite($arquivoHtml, "<div id='divTitulo'><h4>ARQUIVO DE RETORNO - ".$arquivoOrigem."</h4></div>\n");

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

		$sqlArquivo = "SELECT COUNT(1) AS Registros from sce_arquivo where BAN_ID = ".$BAN_ID." AND ARQ_NSA = ".$NSA." AND ARQ_DATA_BANCO = '".$dataArquivoINV."'";
		$exeArquivo = mysql_query($sqlArquivo, $conn) or die(mysql_error());
		$rowArquivo = mysql_num_rows($exeArquivo);
		$rowArquivo = mysql_fetch_assoc($exeArquivo);
		if ($rowArquivo['Registros'] > 0){
			echo("<h2>Atenção!</h2><br>");
			echo("O arquivo ".$arquivoRetorno."<br>");
			echo("Não pode ser re-processado.<br>");
			echo "NSA  = ".$NSA."<br>";
			echo "Data = ".$dataArquivoINV."<br>";
			exit;
		}
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
				$sqlBaixarTitulo = ""
				."  UPDATE sce_titulo SET"
				."  TIT_STATUS = '9'"
				.", REMESSA_ID = ".$NSA
				.", TIT_VALOR_RECEBIDO = ".$TIT_VALOR_RECEBIDO
				.", TIT_VALOR_DESCONTOS = ".$TIT_VALOR_DESCONTOS
				.", TIT_DATA_COMPENSACAO = '".$dataPagamento."'"
				."  WHERE TIT_ID = ".$TIT_ID;
				$rs_BaixarTitulo = mysql_query($sqlBaixarTitulo, $conn);
				if (mysql_error() != ""){
					echo("Erro ao baixar titulo<br>");
					echo("Titulo=".$TIT_ID."<br>");
					echo("Registro=".$totalRegistros."<br>");
					echo("sql=".$sqlBaixarTitulo."<br>");
					exit();
				}
			}
//***** FIM BAIXAR TITULO PAGO
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
		  	$sqlBaixarTitulo = " UPDATE sce_titulo SET TIT_STATUS = '0', REMESSA_ID = 0 WHERE TIT_NUMERO_DOCUMENTO = '".$NumeroDocumento."'";
			$rs_BaixarTitulo = mysql_query($sqlBaixarTitulo, $conn);
			if (mysql_error() != ""){
				echo("Erro ao registrar titulo não baixado<br>");
				echo("Titulo=".$TIT_ID."<br>");
				echo("Registro=".$totalRegistros."<br>");
				echo("sql=".$sqlBaixarTitulo."<br>");
				exit();
			}
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

$sqlArquivo = ""
." INSERT INTO sce_arquivo"
." (ARQ_NSA, ARQ_TIPO, ARQ_NOME, ARQ_DATA_BANCO, ARQ_DATA_PROCESSAMENTO, BAN_ID,"
." ARQ_REGISTROS, ARQ_VALOR, ARQ_VALOR_CREDITADO, ARQ_VALOR_DESCONTOS, ARQ_REGISTROS_ERRO,"
." ARQ_VALOR_ERRO, ARQ_DUPLICIDADE)"
." VALUES ("
."  '".$NSA."'"
.",  "."2"
.", '".$arquivoOrigem."'"
.", '".$dataArquivoINV."'"
.",  ".$dataReferencia
.",  ".$BAN_ID
.",  ".$totalRegistros
.",  ".$Soma_Valor_Titulos
.",  ".$Soma_Valor_Creditado
.",  ".$Soma_Valor_Tarifas
.",  ".$Total_Registros_Com_Erro
.",  ".$Soma_Valor_com_erro
.",  ".$Total_Registros_Duplicidade.")";
$rs_IncluirArquivo = mysql_query($sqlArquivo, $conn);
if (mysql_error() != ""){
	echo("Erro ao incluir arquivo<br>");
	echo("sql=".$sqlArquivo);
	exit();
}

$nomeArquivoModelo = $nomeArquivo;
$arquivoModelo = fopen($nomeArquivoModelo , "rb");
$arquivoNovo   = fread($arquivoModelo,1000000);
fclose($arquivoModelo);

$de = $arquivoRetorno;
$para = $arquivoRetorno."-PROCESSADO";
rename($de, $para);
echo($arquivoNovo);

?>
