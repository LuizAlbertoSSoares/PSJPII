<?php
require_once('Seguranca.bus.php');
include "../Conn.php";
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";

session_start('cpfcnpj'); $cpf_cnpj = $_SESSION["cpfcnpj"];
session_start('usuario'); $usuario  = $_SESSION["usuario"];

$Operacao = $_POST['Operacao'];	
$Mensagem = $_POST['Mensagem'];

$remetente = "campanha.psjoaopauloii@gmail.com";
$origem    = "Paroquia São João Paulo II <psjoaopauloii@psjoaopauloii.kinghost.net>";
$resposta  = "campanha.psjoaopauloii@gmail.com";

# BUSCAR DADOS DE CONFIGURAÇÃO
$sql = "select * from sce_configuracao";
$exe = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exe);
while ($row = mysql_fetch_assoc($exe)){	$numeroDaCampanha = sprintf("%03d", $row['campanha']); }

### IDENTIFICAR LIMITE DIARIO PARA CORREIO
$sqlControleEmail = "SELECT * FROM sce_controle_email WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
$exeControleEmail = mysql_query($sqlControleEmail, $conn) or die(mysql_error());
$rowControleEmail = mysql_num_rows($exeControleEmail);
$rowControleEmail = mysql_fetch_assoc($exeControleEmail);
if ($rowControleEmail['enviado'] > $rowControleEmail['limite']){
	echo "Limite diário para envio de email não permitido!<br>";
	echo "Limite  = ".$rowControleEmail['limite']." Enviado = ".$rowControleEmail['enviado'];
	exit;
}

### LISTA PARA ENVIO DO CORREIO
if ($Operacao == "BuscarDestinatario"){
	if ($Mensagem == "Agradecimento"){
		$sql = ""
		."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
		."Where Q.QUO_ID_EMPRESA = 1 "
		."AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha
		." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
		."AND C.CTB_EMAIL <> '' "
		."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
		."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
		."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
		."AND T.TIT_VALOR_RECEBIDO > 0 "
		."AND T.TIT_DATA_COMPENSACAO <= ".$dataReferencia
		."GROUP BY QUO_ID_QUOTA "
		."ORDER BY QUO_ID_QUOTA ASC ";
	}
	if ($Mensagem == "Sorteio"){
		$sql = ""
		." SELECT COUNT(1) AS parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		." FROM sce_contribuinte AS C, sce_quota AS Q "
		." WHERE Q.QUO_ID_EMPRESA = 1 AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID AND C.CTB_EMAIL <> '' "
		." GROUP BY Q.QUO_ID_CONTRIBUINTE "
		." ORDER BY C.CTB_NOME ASC ";
	}
	if ($Mensagem == "Lembrete"){	
		$sql = ""
		."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
		."Where Q.QUO_ID_EMPRESA = 1 "
		."AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha
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
	
	if ($Mensagem == "Contribuinte"){
		$sql = ""
		." SELECT COUNT(1) AS parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		." FROM sce_contribuinte AS C, sce_quota AS Q "
		." WHERE Q.QUO_ID_EMPRESA = 1 AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID AND C.CTB_EMAIL <> '' "
		." GROUP BY Q.QUO_ID_CONTRIBUINTE "
		." ORDER BY C.CTB_NOME ASC ";
	}
	if ($Mensagem == "NaoContribuinte"){
		$sql = ""
		." SELECT C.CTB_ID, C.CTB_NOME, C.CTB_EMAIL, Q.QUO_ID_QUOTA "
		." FROM sce_contribuinte C "
		." LEFT JOIN sce_quota Q ON ( Q.QUO_ID_CONTRIBUINTE = C.CTB_ID) "
		." WHERE C.CTB_EMAIL <> '' ";
	}
	
	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	echo("<table class='tbNormal' align='center' id='tbLista'>");
	echo("<thead>");
	echo("<th class='tdLista'width='10%' align='left'>Sequencia</td>"
	."<th class='tdLista'width='10%' align='left'>Seleção</td>"
	."<th class='tdLista'width='10%' align='left'>Quota</td>"
	."<th class='tdLista'width='70%' align='left'>Contribuinte</td>"
	."<th class='tdLista'width='10%' align='left'>Parcelas</td>"
	."</th>");
	echo("</thead>");
	$totContribuintes = 0;
	while ($row = mysql_fetch_assoc($exec)){
		if ($Mensagem == "NaoContribuinte"){
			if ($row['QUO_ID_QUOTA'] != NULL){goto Proximo;}
		}
		$totContribuintes += 1;
		
		if ($Mensagem == "NaoContribuinte"){
			echo("<tr>"
			."<td class='tdLista' width='10%'>".str_pad($totContribuintes,4,"0", STR_PAD_LEFT)."</td>"
			."<td class='tdLista' width='10%'><input type=checkbox id='".$row['CTB_ID']."' checked='true'></td>"
			."<td class='tdLista' width='10%'>".$row['CTB_ID']."</td>"
			."<td class='tdLista' width='70%'>".sprintf("%04d",$row['CTB_ID'])." - ".$row['CTB_NOME']."</td>"
			."<td class='tdLista' width='10%'>".$row['parcelas']."</td>"
			."</tr>");
			goto Proximo;
		}
		
		echo("<tr>"
		."<td class='tdLista' width='10%'>".str_pad($totContribuintes,4,"0", STR_PAD_LEFT)."</td>"
		."<td class='tdLista' width='10%'><input type=checkbox id='".$row['QUO_ID_QUOTA']."' checked='true'></td>"
		."<td class='tdLista' width='10%'>".$row['QUO_ID_QUOTA']."</td>"
		."<td class='tdLista' width='70%'>".sprintf("%04d",$row['CTB_ID'])." - ".$row['CTB_NOME']."</td>"
		."<td class='tdLista' width='10%'>".$row['parcelas']."</td>"
		."</tr>");

		Proximo:
		
	}
	echo("</table>");
	exit;
}

### EXIBIR OU ENVIAR O CORREIO DE AGRADECIMENTO
if ($Mensagem == "Agradecimento"){
	$Lista = $_POST['ListaEmail'];
	$listaEmail = explode(';', $Lista);

	$nomeArquivoModelo = "Agradecimento";
	$sql = "SELECT texto FROM sce_texto WHERE nome = '".$nomeArquivoModelo."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$row = mysql_fetch_assoc($exe);
	$arquivoNovo = html_entity_decode($row['texto'], ENT_QUOTES);
	$data = $dia." de ".$mesExtenso." de ".$ano;
	$arquivoNovo = str_replace("[dataAtual]",$data,$arquivoNovo);
	
	$sqlPremio = "SELECT PRM_DT_SORTEIO, PRM_DT_QUITACAO, PRM_PARCELA, PRM_NM_PREMIO FROM sce_premios WHERE PRM_ID_EVENTO = ".$numeroDaCampanha." AND PRM_DT_SORTEIO >= ".$dataReferencia;
	$exePremio =  mysql_query($sqlPremio, $conn) or die(mysql_error());
	$rowPremio =  mysql_num_rows($exePremio);
	$rowPremio =  mysql_fetch_assoc($exePremio);
	$dataProxSorteio = substr($rowPremio['PRM_DT_SORTEIO'],8,2)."/"
					  .substr($rowPremio['PRM_DT_SORTEIO'],5,2)."/"
					  .substr($rowPremio['PRM_DT_SORTEIO'],0,4);
	$premioProxSorteio = $rowPremio['PRM_NM_PREMIO'];
	$parcelaProxSorteio = $rowPremio['PRM_PARCELA'];
	$dataQuitarProxSorteio = substr($rowPremio['PRM_DT_QUITACAO'],8,2)."/"
							.substr($rowPremio['PRM_DT_QUITACAO'],5,2)."/"
							.substr($rowPremio['PRM_DT_QUITACAO'],0,4);
							
	$arquivoNovo = str_replace("[dataProxSorteio]",$dataProxSorteio,$arquivoNovo);
	$arquivoNovo = str_replace("[nomePremio]",$premioProxSorteio,$arquivoNovo);
	$arquivoNovo = str_replace("[parcelaPremio]",$parcelaProxSorteio,$arquivoNovo);
	$arquivoNovo = str_replace("[dataPagamento]",$dataQuitarProxSorteio,$arquivoNovo);

	$sql = ""
	."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
	."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
	."Where Q.QUO_ID_EMPRESA = 1 "
	."AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." "
	."AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
	."AND C.CTB_EMAIL <> '' "
	."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
	."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
	."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
	."AND T.TIT_VALOR_RECEBIDO > 0 "
	."AND T.TIT_DATA_COMPENSACAO <= ".$dataReferencia
	."GROUP BY QUO_ID_QUOTA "
	."ORDER BY QUO_ID_QUOTA ASC ";
	$exec = mysql_query($sql, $conn) or die(mysql_error());
	$row  = mysql_num_rows($exec);
	$totalEmail = 0;
	while ($row = mysql_fetch_assoc($exec))	{
		if (array_search($row['QUO_ID_QUOTA'], $listaEmail)!== FALSE){
			$mensagemHtml = $arquivoNovo;
			$destino = $row['CTB_EMAIL'];
			$nome = $row['CTB_NOME'];
			$carne = $row['QUO_ID_QUOTA'];
			$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
			$mensagemHtml = str_replace("[numeroCarne]",$carne,$mensagemHtml);
			$sqlTitulos = "SELECT TIT_EVN_ID, TIT_QUO_ID, SUBSTR(TIT_NOSSO_NUMERO,16,2) AS PARCELA, "
			."TIT_VALOR_TITULO, TIT_VALOR_RECEBIDO, TIT_VALOR_DESCONTOS, TIT_DATA_LANCAMENTO, TIT_STATUS "
			."FROM sce_titulo WHERE TIT_EVN_ID = ".$numeroDaCampanha." AND TIT_QUO_ID = ".$row['QUO_ID_QUOTA']
			." AND TIT_VALOR_RECEBIDO > 0 ";		
			$totalTitulos = 0;
			$exeTitulos = mysql_query($sqlTitulos, $conn) or die(mysql_error());
			$rowTitulos = mysql_num_rows($exeTitulos);
			while ($rowTitulos = mysql_fetch_assoc($exeTitulos))			{
				$totalTitulos += 1;
				$sequenciaParcela = sprintf("%02d",$totalTitulos);
				$sequenciaVencimento = "vencimento".sprintf("%02d",$totalTitulos);
				$sequenciaValor = "valorParcela".sprintf("%02d",$totalTitulos);
				$valor = $rowTitulos['TIT_VALOR_TITULO'];
				if ($rowTitulos['PARCELA'] == 00) {$valor = $globalValorDaParcelaUnica;};
				$sequenciaPagamento = "formaPagto".sprintf("%02d",$totalTitulos);
				$dataVencimento = substr($rowTitulos['TIT_DATA_LANCAMENTO'],8,2)."/"
								 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],5,2)."/"
								 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],0,4);
				$situacaoTitulo = "PAGO";
				if ($rowTitulos['TIT_VALOR_RECEBIDO'] == 0){$situacaoTitulo = "Pendente";}
				$mensagemHtml = str_replace("[parcela".$sequenciaParcela."]",$rowTitulos['PARCELA'],$mensagemHtml);
				$mensagemHtml = str_replace("[vencimento".$sequenciaParcela."]", $dataVencimento, $mensagemHtml);
				$mensagemHtml = str_replace("[valorParcela".$sequenciaParcela."]", sprintf("%01.2f",$valor),$mensagemHtml);
				$mensagemHtml = str_replace("[formaPagto".$sequenciaParcela."]",$situacaoTitulo,$mensagemHtml);
				$valorApurado += $rowTitulos['TIT_VALOR_TITULO'];
			}
			for ($i = 1; $i <= 20; $i++) {
				$sequenciaParcela = sprintf("%02d",$i);
				$sequenciaVencimento = "vencimento".sprintf("%02d",$i);
				$sequenciaValor = "valorParcela".sprintf("%02d",$i);
				$sequenciaPagamento = "formaPagto".sprintf("%02d",$i);
				$mensagemHtml = str_replace("[parcela".$sequenciaParcela."]"," ",$mensagemHtml);
				$mensagemHtml = str_replace("[vencimento".$sequenciaParcela."]", " ", $mensagemHtml);
				$mensagemHtml = str_replace("[valorParcela".$sequenciaParcela."]", " ",$mensagemHtml);
				$mensagemHtml = str_replace("[formaPagto".$sequenciaParcela."]", " ",$mensagemHtml);
			}
			$assunto = "Agradecimento";
			$mensagem= $mensagemHtml;
			
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type:text/html; charset=utf-8\r\n";
			$headers .= "From: ".$origem."\r\n";
			$headers .= "Reply-To: ".$resposta."\r\n";
			if ($Operacao == "ExibirCorreio"){	
				echo "<html><head><title></title>";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				echo "<style type='text/css'>";
				echo "body{";
				echo "	font-family: Arial, Verdana, Tahoma, Sans-Serif;";
				echo "	color: #333333;";
				echo "	font-size: 12px;";
				echo "} ";
				echo "#divExibirCorreio{";
				echo "	width:685px;";
				echo "	border: 3px solid #9BCD9B;";
				echo "}";
				echo "</style>";
				echo "</head>";
				echo "<body>";
				echo "<div id='divExibirCorreio'>";
				echo $mensagem;
				echo "<div>";
				echo "</body>";
				echo "</html>";
				exit;
			}
			else{
				if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";exit;}
			}
			$totalEmail += 1;
		}
	}
	### SALVAR ENVIADOS NO CONTROLE
	$sqlControleEmail = "SELECT * FROM sce_controle_email WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	$exeControleEmail = mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	$rowControleEmail = mysql_num_rows($exeControleEmail);
	$rowControleEmail = mysql_fetch_assoc($exeControleEmail);
	$enviado = $rowControleEmail['enviado'];
	if ($enviado > 0){
		$total = $enviado + $totalEmail;
		$sqlControleEmail = "UPDATE sce_controle_email SET enviado = ".$total." WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	} else{
		$sqlControleEmail = "INSERT INTO sce_controle_email (pk_data, limite, enviado) values ('".$ano."-".$mes."-".$dia."', 2000, ".$totalEmail.")";
	}
	$exeControleEmail =  mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	echo $totalEmail." ENVIADOS...";
	exit;
}

### EXIBIR OU ENVIAR O CORREIO DE SORTEIO
if ($Mensagem == "Sorteio"){
	$Lista = $_POST['ListaEmail'];
	$listaEmail = explode(';', $Lista);
	
	$nomeArquivoModelo = "Comunicar Sorteio";
	$sql = "SELECT texto FROM sce_texto WHERE nome = '".$nomeArquivoModelo."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$row = mysql_fetch_assoc($exe);
	$arquivoNovo = html_entity_decode($row['texto'], ENT_QUOTES);
	$data = $dia." de ".$mesExtenso." de ".$ano;
	$arquivoNovo = str_replace("[dataAtual]",$data,$arquivoNovo);	

	$sql = ""
	." SELECT COUNT(1) AS parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
	." FROM sce_contribuinte AS C, sce_quota AS Q "
	." WHERE Q.QUO_ID_EMPRESA = 1 AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID AND C.CTB_EMAIL <> '' "
	." GROUP BY Q.QUO_ID_CONTRIBUINTE "
	." ORDER BY C.CTB_NOME ASC ";	

	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	$totalEmail = 0;
	
	while ($row = mysql_fetch_assoc($exec))	{
		if (array_search($row['QUO_ID_QUOTA'], $listaEmail)!== FALSE){
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
			if ($Operacao == "ExibirCorreio"){	
				echo "<html><head><title></title>";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				echo "<style type='text/css'>";
				echo "body{";
				echo "	font-family: Arial, Verdana, Tahoma, Sans-Serif;";
				echo "	color: #333333;";
				echo "	font-size: 12px;";
				echo "} ";
				echo "#divExibirCorreio{";
				echo "	width:685px;";
				echo "	border: 3px solid #9BCD9B;";
				echo "}";
				echo "</style>";
				echo "</head>";
				echo "<body>";
				echo "<div id='divExibirCorreio'>";
				echo $mensagem;
				echo "<div>";
				echo "</body>";
				echo "</html>";
				exit;
			}
			else{
				if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";exit;}
			}
			$totalEmail += 1;
		}
	}
	### SALVAR ENVIADOS NO CONTROLE
	$sqlControleEmail = "SELECT * FROM sce_controle_email WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	$exeControleEmail = mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	$rowControleEmail = mysql_num_rows($exeControleEmail);
	$rowControleEmail = mysql_fetch_assoc($exeControleEmail);
	$enviado = $rowControleEmail['enviado'];
	if ($enviado > 0){
		$total = $enviado + $totalEmail;
		$sqlControleEmail = "UPDATE sce_controle_email SET enviado = ".$total." WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	} else{
		$sqlControleEmail = "INSERT INTO sce_controle_email (pk_data, limite, enviado) values ('".$ano."-".$mes."-".$dia."', 2000, ".$totalEmail.")";
	}
	$exeControleEmail =  mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	echo $totalEmail." ENVIADOS...";
	exit;	
}
### EXIBIR OU ENVIAR O CORREIO DE LEMBRETE COM PARCELAS EM ATRASO
if ($Mensagem == "Lembrete"){
	$Lista = $_POST['ListaEmail'];
	$listaEmail = explode(';', $Lista);
	
	$nomeArquivoModelo = "Lembrete do sorteio";
	$sql = "SELECT texto FROM sce_texto WHERE nome = '".$nomeArquivoModelo."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$row = mysql_fetch_assoc($exe);
	$arquivoNovo = html_entity_decode($row['texto'], ENT_QUOTES);
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

		$sql = ""
		."SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		."FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
		."Where Q.QUO_ID_EMPRESA = 1 "
		."AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." "
		."AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
		."AND C.CTB_EMAIL <> '' "
		."AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
		."AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA "
		."AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
		."AND T.TIT_VALOR_RECEBIDO = 0 "
		."AND T.TIT_DATA_DOCUMENTO <= ".$dataReferencia
		."GROUP BY QUO_ID_QUOTA "
		."ORDER BY QUO_ID_QUOTA ASC ";
		$exec =  mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_num_rows($exec);
		$totalEmail = 0;
		while ($row = mysql_fetch_assoc($exec))	{
			if (array_search($row['QUO_ID_QUOTA'], $listaEmail)!== FALSE){
				$mensagemHtml = $arquivoNovo;
				$destino = $row['CTB_EMAIL'];
				$nome = $row['CTB_NOME'];
				$carne = $row['QUO_ID_QUOTA'];
				$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
				$mensagemHtml = str_replace("[numeroQuota]",$carne,$mensagemHtml);

				$sqlTitulos = "SELECT TIT_EVN_ID, TIT_QUO_ID, SUBSTR(TIT_NOSSO_NUMERO,16,2) AS PARCELA, "
				."TIT_VALOR_TITULO, TIT_VALOR_RECEBIDO, TIT_VALOR_DESCONTOS, TIT_DATA_LANCAMENTO, TIT_STATUS "
				."FROM sce_titulo WHERE TIT_EVN_ID = ".$numeroDaCampanha." AND TIT_QUO_ID = ".$row['QUO_ID_QUOTA']
				." AND TIT_VALOR_RECEBIDO = 0 "
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
					$situacaoTitulo = "PAGO";
					if ($rowTitulos['TIT_STATUS'] == 0){
						$situacaoTitulo = "PENDENTE";
					}
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
				$assunto = "Lembrete do sorteio";
				$mensagem= $mensagemHtml;
				$headers = "Content-Type:text/html; charset=utf-8\r\n";
				$headers .= "From: ".$origem."\r\n";
				$headers .= "Reply-To: ".$resposta."\r\n";
				if ($Operacao == "ExibirCorreio"){	
					echo "<html><head><title></title>";
					echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
					echo "<style type='text/css'>";
					echo "body{";
					echo "	font-family: Arial, Verdana, Tahoma, Sans-Serif;";
					echo "	color: #333333;";
					echo "	font-size: 12px;";
					echo "} ";
					echo "#divExibirCorreio{";
					echo "	width:685px;";
					echo "	border: 3px solid #9BCD9B;";
					echo "}";
					echo "</style>";
					echo "</head>";
					echo "<body>";
					echo "<div id='divExibirCorreio'>";
					echo $mensagem;
					echo "<div>";
					echo "</body>";
					echo "</html>";
					exit;
				}
				else{
					if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";exit;}
					echo "Correio enviado com sucesso.";
				}			
				$totalEmail += 1;
			}
		}
	### SALVAR ENVIADOS NO CONTROLE
	$sqlControleEmail = "SELECT * FROM sce_controle_email WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	$exeControleEmail = mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	$rowControleEmail = mysql_num_rows($exeControleEmail);
	$rowControleEmail = mysql_fetch_assoc($exeControleEmail);
	$enviado = $rowControleEmail['enviado'];
	if ($enviado > 0){
		$total = $enviado + $totalEmail;
		$sqlControleEmail = "UPDATE sce_controle_email SET enviado = ".$total." WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	} else{
		$sqlControleEmail = "INSERT INTO sce_controle_email (pk_data, limite, enviado) values ('".$ano."-".$mes."-".$dia."', 2000, ".$totalEmail.")";
	}
	$exeControleEmail =  mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	echo $totalEmail." ENVIADOS...";
	exit;		
}
# EXIBIR OU ENVIAR CORREIO Genêrica - Contribuinte
if ($Mensagem == "Contribuinte"){
	$Lista = $_POST['ListaEmail'];
	$listaEmail = explode(';', $Lista);
	
	$nomeArquivoModelo = "Comunicação genêrica para contribuinte";

	$sql = "SELECT texto FROM sce_texto WHERE nome = '".$nomeArquivoModelo."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$row = mysql_fetch_assoc($exe);
	$arquivoNovo = html_entity_decode($row['texto'], ENT_QUOTES);
	$data = $dia." de ".$mesExtenso." de ".$ano;
	$arquivoNovo = str_replace("[dataAtual]",$data,$arquivoNovo);	

	$sql = ""
	." SELECT COUNT(1) AS parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
	." FROM sce_contribuinte AS C, sce_quota AS Q "
	." WHERE Q.QUO_ID_EMPRESA = 1 AND Q.QUO_ID_EVENTO = ".$numeroDaCampanha." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID AND C.CTB_EMAIL <> '' "
	." GROUP BY Q.QUO_ID_CONTRIBUINTE "
	." ORDER BY C.CTB_NOME ASC ";

	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	$totalEmail = 0;
	
	while ($row = mysql_fetch_assoc($exec))	{
		if (array_search($row['QUO_ID_QUOTA'], $listaEmail)!== FALSE){
			$mensagemHtml = $arquivoNovo;
			$destino = $row['CTB_EMAIL'];
			$nome = $row['CTB_NOME'];
			$carne = $row['QUO_ID_QUOTA'];
			$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
			$mensagemHtml = str_replace("[numerocarne]",$carne,$mensagemHtml);
			$assunto = "Sorteio";
			$mensagem= $mensagemHtml;
			$headers = "Content-Type:text/html; charset=utf-8\r\n";
			$headers .= "From: ".$origem."\r\n";
			$headers .= "Reply-To: ".$resposta."\r\n";
			if ($Operacao == "ExibirCorreio"){	
				echo "<html><head><title></title>";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				echo "<style type='text/css'>";
				echo "body{";
				echo "	font-family: Arial, Verdana, Tahoma, Sans-Serif;";
				echo "	color: #333333;";
				echo "	font-size: 12px;";
				echo "} ";
				echo "#divExibirCorreio{";
				echo "	width:685px;";
				echo "	border: 3px solid #9BCD9B;";
				echo "}";
				echo "</style>";
				echo "</head>";
				echo "<body>";
				echo "<div id='divExibirCorreio'>";
				echo $mensagem;
				echo "<div>";
				echo "</body>";
				echo "</html>";
				exit;
			}
			else{
				if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";exit;}
			}
			$totalEmail += 1;
		}
	}
	### SALVAR ENVIADOS NO CONTROLE
	$sqlControleEmail = "SELECT * FROM sce_controle_email WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	$exeControleEmail = mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	$rowControleEmail = mysql_num_rows($exeControleEmail);
	$rowControleEmail = mysql_fetch_assoc($exeControleEmail);
	$enviado = $rowControleEmail['enviado'];
	if ($enviado > 0){
		$total = $enviado + $totalEmail;
		$sqlControleEmail = "UPDATE sce_controle_email SET enviado = ".$total." WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	} else{
		$sqlControleEmail = "INSERT INTO sce_controle_email (pk_data, limite, enviado) values ('".$ano."-".$mes."-".$dia."', 2000, ".$totalEmail.")";
	}
	$exeControleEmail =  mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	echo $totalEmail." ENVIADOS...";
	exit;	
}
# EXIBIR OU ENVIAR CORREIO Genêrica - Não Contribuinte
if ($Mensagem == "NaoContribuinte"){
	$Lista = $_POST['ListaEmail'];
	$listaEmail = explode(';', $Lista);
	
	$nomeArquivoModelo = "Comunicação genêrica para não contribuinte";

	$sql = "SELECT texto FROM sce_texto WHERE nome = '".$nomeArquivoModelo."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$row = mysql_fetch_assoc($exe);
	$arquivoNovo = html_entity_decode($row['texto'], ENT_QUOTES);
	$data = $dia." de ".$mesExtenso." de ".$ano;
	$arquivoNovo = str_replace("[dataAtual]",$data,$arquivoNovo);	

	$sql = ""
	." SELECT C.CTB_ID, C.CTB_NOME, C.CTB_EMAIL, Q.QUO_ID_QUOTA "
	." FROM sce_contribuinte C "
	." LEFT JOIN sce_quota Q ON ( Q.QUO_ID_CONTRIBUINTE = C.CTB_ID) "
	." WHERE C.CTB_EMAIL <> '' ";	
	
	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row  =  mysql_num_rows($exec);
	$totalEmail = 0;
	
	while ($row = mysql_fetch_assoc($exec))	{
		if (array_search($row['CTB_ID'], $listaEmail)!== FALSE){
			$mensagemHtml = $arquivoNovo;
			$destino = $row['CTB_EMAIL'];
			$nome = $row['CTB_NOME'];
			$carne = $row['QUO_ID_QUOTA'];
			$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
			$mensagemHtml = str_replace("[numerocarne]",$carne,$mensagemHtml);
			$assunto = "Sorteio";
			$mensagem= $mensagemHtml;
			$headers = "Content-Type:text/html; charset=utf-8\r\n";
			$headers .= "From: ".$origem."\r\n";
			$headers .= "Reply-To: ".$resposta."\r\n";
			if ($Operacao == "ExibirCorreio"){	
				echo "<html><head><title></title>";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				echo "<style type='text/css'>";
				echo "body{";
				echo "	font-family: Arial, Verdana, Tahoma, Sans-Serif;";
				echo "	color: #333333;";
				echo "	font-size: 12px;";
				echo "} ";
				echo "#divExibirCorreio{";
				echo "	width:685px;";
				echo "	border: 3px solid #9BCD9B;";
				echo "}";
				echo "</style>";
				echo "</head>";
				echo "<body>";
				echo "<div id='divExibirCorreio'>";
				echo $mensagem;
				echo "<div>";
				echo "</body>";
				echo "</html>";
				exit;
			}
			else{
				if (mail($destino, $assunto, $mensagem, $headers) == false) {echo "Ocorreu um erro durante o envio do email.";exit;}
			}
			$totalEmail += 1;
		}
	}
	### SALVAR ENVIADOS NO CONTROLE
	$sqlControleEmail = "SELECT * FROM sce_controle_email WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	$exeControleEmail = mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	$rowControleEmail = mysql_num_rows($exeControleEmail);
	$rowControleEmail = mysql_fetch_assoc($exeControleEmail);
	$enviado = $rowControleEmail['enviado'];
	if ($enviado > 0){
		$total = $enviado + $totalEmail;
		$sqlControleEmail = "UPDATE sce_controle_email SET enviado = ".$total." WHERE pk_data = '".$ano."-".$mes."-".$dia."'";
	} else{
		$sqlControleEmail = "INSERT INTO sce_controle_email (pk_data, limite, enviado) values ('".$ano."-".$mes."-".$dia."', 2000, ".$totalEmail.")";
	}
	$exeControleEmail =  mysql_query($sqlControleEmail, $conn) or die(mysql_error());
	echo $totalEmail." ENVIADOS...";
	exit;	
}

?>
