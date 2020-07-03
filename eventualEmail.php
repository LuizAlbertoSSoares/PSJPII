<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
if ($perfilDoUsuario != "Master"){echo "Usuário sem permisão para esta transação!";exit;};
require_once('busConsistirPemissao.php');
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$destino = 'downloads//'; if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$destino = "downloads\\";}

$Mensagem = $_GET['Mensagem'];
$Operacao = $_GET['Operacao'];
$Registro = $_GET['Registro'];
echo "# eventualEmail.php?Mensagem=Sorteio&Operacao=Arquivo&Registro=200<br>";
echo "# Agradecimento<br>";
echo "# Sorteio<br>";
echo "# ParcelasEmAtraso<br>";
echo "# Arquivo<br>";
echo "# Email<br>";
echo "<hr>";

if ($Operacao == "Arquivo"){

	$arquivoNome = "email".$Mensagem.".txt";
	echo "Gerando ".$Operacao." ".$arquivoNome."<br>";
	$arquivoAberto = fopen($destino.$arquivoNome,"w");
	
	if ($Mensagem == "ParcelasEmAtraso"){
		$sql = ""
		."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
		."Where Q.QUO_ID_EMPRESA = 1 "
		."AND Q.QUO_ID_EVENTO = ".$globalEvento
		." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
		."AND C.CTB_EMAIL <> '' "
		."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
		."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
		."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
		."AND T.TIT_STATUS IN ('0') "
		."AND T.TIT_DATA_LANCAMENTO <= ".$dataReferencia
		."GROUP BY QUO_ID_QUOTA "
		."ORDER BY QUO_ID_QUOTA ASC ";
	}
	if ($Mensagem == "Agradecimento"){
		$sql = ""
		."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
		."Where Q.QUO_ID_EMPRESA = 1 "
		."AND Q.QUO_ID_EVENTO = ".$globalEvento
		." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
		."AND C.CTB_EMAIL <> '' "
		."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
		."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
		."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
		."AND T.TIT_STATUS IN ('8','9') "
		."AND T.TIT_DATA_COMPENSACAO <= ".$dataReferencia
		."GROUP BY QUO_ID_QUOTA "
		."ORDER BY QUO_ID_QUOTA ASC ";
	}
	if ($Mensagem == "Sorteio"){
		$sql = ""
		." SELECT COUNT(1) AS parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		." FROM sce_contribuinte AS C, sce_quota AS Q "
		." WHERE Q.QUO_ID_EMPRESA = 1 AND Q.QUO_ID_EVENTO = ".$globalEvento." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID AND C.CTB_EMAIL <> '' "
		." GROUP BY Q.QUO_ID_CONTRIBUINTE "
		." ORDER BY C.CTB_NOME ASC ";
	}

	$exec = mysql_query($sql, $conn) or die(mysql_error());
	$row  = mysql_num_rows($exec);
	$registros = 0;
	while ($row = mysql_fetch_assoc($exec)){
	#   Registro	Quota	Contribuinte	Nome
		$registros += 1;
		$sequencial   = str_pad($registros,04,"0", STR_PAD_LEFT);
		$quota        = str_pad($row['QUO_ID_QUOTA'],04,"0", STR_PAD_LEFT);
		$contribuinte = str_pad($row['CTB_ID'],05,"0", STR_PAD_LEFT);
		$nome         = str_pad($row['CTB_NOME'],50," ", STR_PAD_RIGHT);
		$linhaTexto = $sequencial." ".$quota." ".$contribuinte." ".$nome." ".$email."\r\n";
		fwrite($arquivoAberto, $linhaTexto);
	}
	fclose($arquivoAberto);
	echo $registros." Registros<br>";
	exit;
}	
if ($Operacao == "Email"){
	echo "Enviando Email...<br>";
	$numeroDaCampanha = $globalEvento;
	$valorApurado = 0;
	$remetente = "paroquiansassuncao@uol.com.br.com";
	$origem    = "Paroquia Assunção <sousa@kinghost.net>";
	$resposta  = "paroquiansassuncao@uol.com.br";
#-- ENVIAR EMAIL - COMUNICANDO SORTEIO REALIZADO -----#
	if ($Mensagem == "Sorteio"){

		$nomeArquivoModelo = "Comunicar_Sorteio.txt";
		$arquivoModelo = fopen($destino.$nomeArquivoModelo , "rb");
		$arquivoNovo   = fread($arquivoModelo,1000000);
		fclose($arquivoModelo);
						
		$data = $dia." de ".$mesExtenso." de ".$ano;
		$arquivoNovo = str_replace("[data]",$data,$arquivoNovo);
	
		$arquivoNome = "email".$Mensagem.".txt";
		$arquivoAberto = fopen($destino.$arquivoNome,"r");	
		$enviado = 0;
		while (!feof($arquivoAberto)){
			$linha = fgets($arquivoAberto, 4096);	
			$sequencial   = substr($linha,0,04);
			$quota        = substr($linha,5,04);
			$contribuinte = substr($linha,10,05);
			$nome         = substr($linha,16,50);
			if ($sequencial >= $Registro){
				$enviado += 1;
				if ($enviado < 201){
					echo $sequencial." ".$quota." ".$nome."<br>";
					$sql = ""
					." SELECT COUNT(1) AS parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
					." FROM sce_contribuinte AS C, sce_quota AS Q "
					." WHERE Q.QUO_ID_EMPRESA = 1 AND Q.QUO_ID_EVENTO = ".$globalEvento." AND QUO_ID_QUOTA = ".$quota." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID AND C.CTB_EMAIL <> '' "
					." GROUP BY Q.QUO_ID_CONTRIBUINTE "
					." ORDER BY C.CTB_NOME ASC ";	
					$exec =  mysql_query($sql, $conn) or die(mysql_error());
					$row = mysql_num_rows($exec);
					while ($row = mysql_fetch_assoc($exec))	{
						$mensagemHtml = $arquivoNovo;
						$destino = $row['CTB_EMAIL'];
						$nome = $row['CTB_NOME'];
						$carne = $row['QUO_ID_QUOTA'];
						$mensagemHtml = str_replace("[nomecontribuinte]",$nome,$mensagemHtml);
						$mensagemHtml = str_replace("[numerocarne]",$carne,$mensagemHtml);
						$assunto = "Sorteio";
						$mensagem= $mensagemHtml;
						$headers = "Content-Type:text/html; charset=utf-8\r\n";
						$headers .= "From: ".$origem."\r\n";
						$headers .= "Reply-To: ".$resposta."\r\n";
						if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";}
					}
				}
			}
		}
		fclose($arquivoAberto);
		exit;
	}
#-- ENVIAR EMAIL - PARCELAS EM ATRASO -----#
	if ($Mensagem == "ParcelasEmAtraso"){

		$nomeArquivoModelo = "Parcelas_Pendentes.txt";
		$arquivoModelo = fopen($destino.$nomeArquivoModelo , "rb");
		$arquivoNovo   = fread($arquivoModelo,1000000);
		fclose($arquivoModelo);
		
		$data = $dia." de ".$mesExtenso." de ".$ano;
		$arquivoNovo = str_replace("[dataAtual]",$data,$arquivoNovo);

		$sqlPremio = "SELECT PRM_DT_SORTEIO, PRM_DT_QUITACAO, PRM_PARCELA, PRM_NM_PREMIO FROM sce_premios WHERE PRM_ID_EVENTO = ".$numeroDaCampanha." AND PRM_DT_SORTEIO > ".$dataReferencia;
		$exePremio =  mysql_query($sqlPremio, $conn) or die(mysql_error());
		$rowPremio = mysql_num_rows($exePremio);
		$rowPremio = mysql_fetch_assoc($exePremio);
		$dataProxSorteio = substr($rowPremio['PRM_DT_SORTEIO'],8,2)."/"
						  .substr($rowPremio['PRM_DT_SORTEIO'],5,2)."/"
						  .substr($rowPremio['PRM_DT_SORTEIO'],0,4);
		$premioProxSorteio = $rowPremio['PRM_NM_PREMIO'];
		$parcelaProxSorteio = $rowPremio['PRM_PARCELA'];
		$dataQuitarProxSorteio = substr($rowPremio['PRM_DT_QUITACAO'],8,2)."/"
								.substr($rowPremio['PRM_DT_QUITACAO'],5,2)."/"
								.substr($rowPremio['PRM_DT_QUITACAO'],0,4);
								
		$arquivoNovo = str_replace("[dataSorteio]",$dataProxSorteio,$arquivoNovo);
		$arquivoNovo = str_replace("[nomePremio]",$premioProxSorteio,$arquivoNovo);
		$arquivoNovo = str_replace("[parcelaPremio]",$parcelaProxSorteio,$arquivoNovo);
		$arquivoNovo = str_replace("[dataPagamento]",$dataQuitarProxSorteio,$arquivoNovo);
								
		$arquivoNome = "email".$Mensagem.".txt";
		$arquivoAberto = fopen($destino.$arquivoNome,"r");	
		$enviado = 0;
		while (!feof($arquivoAberto)){
			$linha = fgets($arquivoAberto, 4096);	
			$sequencial   = substr($linha,0,04);
			$quota        = substr($linha,5,04);
			$contribuinte = substr($linha,10,05);
			$nome         = substr($linha,16,50);
			if ($sequencial >= $Registro){
				$enviado += 1;
				if ($enviado < 201){
					echo $sequencial." ".$quota." ".$nome."<br>";
					$sql = ""
					."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
					."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
					."Where Q.QUO_ID_EMPRESA = 1 "
					."AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." "
					."AND Q.QUO_ID_CONTRIBUINTE = ".$contribuinte." "
					."AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
					."AND C.CTB_EMAIL <> '' "
					."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
					."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
					."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
					."AND T.TIT_STATUS IN ('0') "
					."AND T.TIT_DATA_DOCUMENTO <= ".$dataReferencia
					."GROUP BY QUO_ID_QUOTA "
					."ORDER BY QUO_ID_QUOTA ASC ";
					$exec =  mysql_query($sql, $conn) or die(mysql_error());
					$row = mysql_num_rows($exec);
					while ($row = mysql_fetch_assoc($exec))	{
						$mensagemHtml = $arquivoNovo;
						$destino = $row['CTB_EMAIL'];	
						$nome = $row['CTB_NOME'];
						$carne = $row['QUO_ID_QUOTA'];
						$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
						$mensagemHtml = str_replace("[numeroQuota]",$carne,$mensagemHtml);

						$sqlTitulos = "SELECT TIT_EVN_ID, TIT_QUO_ID, SUBSTR(TIT_NUMERO_DOCUMENTO,9,2) AS PARCELA, "
						."TIT_VALOR_TITULO, TIT_VALOR_RECEBIDO, TIT_VALOR_DESCONTOS, TIT_DATA_LANCAMENTO, TIT_STATUS "
						."FROM sce_titulo WHERE TIT_EVN_ID = ".$numeroDaCampanha." AND TIT_QUO_ID = ".$row['QUO_ID_QUOTA']
						." AND TIT_STATUS IN ('0') "
						."AND TIT_DATA_LANCAMENTO <= ".$dataReferencia;

						$totalTitulos = 0;
						$exeTitulos =  mysql_query($sqlTitulos, $conn) or die(mysql_error());
						$rowTitulos = mysql_num_rows($exeTitulos);
						while ($rowTitulos = mysql_fetch_assoc($exeTitulos)){
							$totalTitulos += 1;
							$sequenciaParcela = "parcela".sprintf("%02d",$totalTitulos);
							$sequenciaVencimento = "vencimento".sprintf("%02d",$totalTitulos);
							$sequenciaValor = "valorParcela".sprintf("%02d",$totalTitulos);
							$sequenciaPagamento = "formaPagto".sprintf("%02d",$totalTitulos);
							$dataVencimento = substr($rowTitulos['TIT_DATA_LANCAMENTO'],8,2)."/"
											 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],5,2)."/"
											 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],0,4);
							$situacaoTitulo = "PAGO";if ($rowTitulos['TIT_STATUS'] == 0){$situacaoTitulo = "PENDENTE";}
							$mensagemHtml = str_replace("[".$sequenciaParcela."]",$rowTitulos['PARCELA'],$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaVencimento."]", $dataVencimento, $mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaValor."]",$rowTitulos['TIT_VALOR_TITULO'],$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaPagamento."]",$situacaoTitulo,$mensagemHtml);
							$valorApurado += $rowTitulos['TIT_VALOR_TITULO'];
						}
						for ($i = 1; $i <= 20; $i++) {
							$sequenciaParcela = "parcela".sprintf("%02d",$i);
							$sequenciaVencimento = "vencimento".sprintf("%02d",$i);
							$sequenciaValor = "valorParcela".sprintf("%02d",$i);
							$sequenciaPagamento = "formaPagto".sprintf("%02d",$i);
							$mensagemHtml = str_replace("[".$sequenciaParcela."]"," ",$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaVencimento."]", " ", $mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaValor."]", " ",$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaPagamento."]", " ",$mensagemHtml);
						}
						$assunto = "Parcela em atraso";
						$mensagem= $mensagemHtml;
						$headers = "Content-Type:text/html; charset=utf-8\r\n";
						$headers .= "From: ".$origem."\r\n";
						$headers .= "Reply-To: ".$resposta."\r\n";
						if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";}
					}
				}
			}
		}
		fclose($arquivoAberto);
		exit;
	}
#-- ENVIAR EMAIL - AGRADDECIMENTO -----#
	if ($Mensagem == "Agradecimento"){

		$nomeArquivoModelo = "Agradecimento.txt";
		$arquivoModelo = fopen($destino.$nomeArquivoModelo , "rb");
		$arquivoNovo   = fread($arquivoModelo,1000000);
		fclose($arquivoModelo);
		
		$data = $dia." de ".$mesExtenso." de ".$ano;
		$arquivoNovo = str_replace("[dataAtual]",$data,$arquivoNovo);

		$sqlPremio = "SELECT PRM_DT_SORTEIO, PRM_DT_QUITACAO, PRM_PARCELA, PRM_NM_PREMIO FROM sce_premios WHERE PRM_ID_EVENTO = ".$numeroDaCampanha." AND PRM_DT_SORTEIO > ".$dataReferencia;
		$exePremio =  mysql_query($sqlPremio, $conn) or die(mysql_error());
		$rowPremio = mysql_num_rows($exePremio);
		$rowPremio = mysql_fetch_assoc($exePremio);
		$dataProxSorteio = substr($rowPremio['PRM_DT_SORTEIO'],8,2)."/"
						  .substr($rowPremio['PRM_DT_SORTEIO'],5,2)."/"
						  .substr($rowPremio['PRM_DT_SORTEIO'],0,4);
		$premioProxSorteio = $rowPremio['PRM_NM_PREMIO'];
		$parcelaProxSorteio = $rowPremio['PRM_PARCELA'];
		$dataQuitarProxSorteio = substr($rowPremio['PRM_DT_QUITACAO'],8,2)."/"
								.substr($rowPremio['PRM_DT_QUITACAO'],5,2)."/"
								.substr($rowPremio['PRM_DT_QUITACAO'],0,4);
								
		$arquivoNovo = str_replace("[dataSorteio]",$dataProxSorteio,$arquivoNovo);
		$arquivoNovo = str_replace("[nomePremio]",$premioProxSorteio,$arquivoNovo);
		$arquivoNovo = str_replace("[parcelaPremio]",$parcelaProxSorteio,$arquivoNovo);
		$arquivoNovo = str_replace("[dataPagamento]",$dataQuitarProxSorteio,$arquivoNovo);
								
		$arquivoNome = "email".$Mensagem.".txt";
		$arquivoAberto = fopen($destino.$arquivoNome,"r");	
		$enviado = 0;
		while (!feof($arquivoAberto)){
			$linha = fgets($arquivoAberto, 4096);	
			$sequencial   = substr($linha,0,04);
			$quota        = substr($linha,5,04);
			$contribuinte = substr($linha,10,05);
			$nome         = substr($linha,16,50);
			if ($sequencial >= $Registro){
				$enviado += 1;
				if ($enviado < 2){
					echo $sequencial." ".$quota." ".$nome."<br>";
					$sql = ""
					."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
					."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
					."Where Q.QUO_ID_EMPRESA = 1 "
					."AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." "
					."AND Q.QUO_ID_CONTRIBUINTE = ".$contribuinte." "
					."AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
					."AND C.CTB_EMAIL <> '' "
					."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
					."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
					."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
					."AND T.TIT_STATUS IN ('0') "
					."AND T.TIT_DATA_DOCUMENTO <= ".$dataReferencia
					."GROUP BY QUO_ID_QUOTA "
					."ORDER BY QUO_ID_QUOTA ASC ";
					$exec =  mysql_query($sql, $conn) or die(mysql_error());
					$row = mysql_num_rows($exec);
					while ($row = mysql_fetch_assoc($exec))	{
						$mensagemHtml = $arquivoNovo;
						$destino = $row['CTB_EMAIL'];	
						$nome = $row['CTB_NOME'];
						$carne = $row['QUO_ID_QUOTA'];
						$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
						$mensagemHtml = str_replace("[numeroCarne]",$carne,$mensagemHtml);

						$sqlTitulos = "SELECT TIT_EVN_ID, TIT_QUO_ID, SUBSTR(TIT_NUMERO_DOCUMENTO,9,2) AS PARCELA, "
						."TIT_VALOR_TITULO, TIT_VALOR_RECEBIDO, TIT_VALOR_DESCONTOS, TIT_DATA_LANCAMENTO, TIT_STATUS "
						."FROM sce_titulo WHERE TIT_EVN_ID = ".$numeroDaCampanha." AND TIT_QUO_ID = ".$row['QUO_ID_QUOTA']
						." AND TIT_STATUS IN ('8','9') "
						."AND TIT_DATA_LANCAMENTO <= ".$dataReferencia;
						
						$totalTitulos = 0;
						$exeTitulos =  mysql_query($sqlTitulos, $conn) or die(mysql_error());
						$rowTitulos = mysql_num_rows($exeTitulos);
						while ($rowTitulos = mysql_fetch_assoc($exeTitulos)){
							$totalTitulos += 1;
							$sequenciaParcela = "parcela".sprintf("%02d",$totalTitulos);
							$sequenciaVencimento = "vencimento".sprintf("%02d",$totalTitulos);
							$sequenciaValor = "valorParcela".sprintf("%02d",$totalTitulos);
							$sequenciaPagamento = "formaPagto".sprintf("%02d",$totalTitulos);
							$dataVencimento = substr($rowTitulos['TIT_DATA_LANCAMENTO'],8,2)."/"
											 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],5,2)."/"
											 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],0,4);
							$situacaoTitulo = "PAGO";if ($rowTitulos['TIT_STATUS'] == 0){$situacaoTitulo = "PENDENTE";}
							$mensagemHtml = str_replace("[".$sequenciaParcela."]",$rowTitulos['PARCELA'],$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaVencimento."]", $dataVencimento, $mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaValor."]",$rowTitulos['TIT_VALOR_TITULO'],$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaPagamento."]",$situacaoTitulo,$mensagemHtml);
							$valorApurado += $rowTitulos['TIT_VALOR_TITULO'];
						}
						for ($i = 1; $i <= 20; $i++) {
							$sequenciaParcela = "parcela".sprintf("%02d",$i);
							$sequenciaVencimento = "vencimento".sprintf("%02d",$i);
							$sequenciaValor = "valorParcela".sprintf("%02d",$i);
							$sequenciaPagamento = "formaPagto".sprintf("%02d",$i);
							$mensagemHtml = str_replace("[".$sequenciaParcela."]"," ",$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaVencimento."]", " ", $mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaValor."]", " ",$mensagemHtml);
							$mensagemHtml = str_replace("[".$sequenciaPagamento."]", " ",$mensagemHtml);
						}
						$assunto = "Agradecimento";
						$mensagem= $mensagemHtml;
						$headers = "Content-Type:text/html; charset=utf-8\r\n";
						$headers .= "From: ".$origem."\r\n";
						$headers .= "Reply-To: ".$resposta."\r\n";
						$destino = "jmosousa@yahoo.com.br";
						if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";}
					}
				}
			}
		}
		fclose($arquivoAberto);
		exit;
	}	
}



?>
