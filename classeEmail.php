<?php
require_once('busConsistirPemissao.php');
set_time_limit(0); 
ignore_user_abort(true);
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");

$Email = new Email;
echo $Email->EnviarEmail("Agradecimento", "3001", "Enviar");

exit;

class Email { 

	public $listaQuotas;						# Indicar um array com a lista dos contribuintes
	public $enviaOUvisualiza;     				# Indicar se envia ou visualiza o email
	
	public $numeroDaCampanha;
	public $origem    			= "Paroquia Assunção <adfinanceiro@adfinanceiro.com.br>";
	public $remetente 			= "paroquiansassuncao@uol.com.br.com";
	public $resposta  			= "paroquiansassuncao@uol.com.br";
	public $destino   			= 'email//'; 	# Identificar a pasta
	public $dia;
	public $mes;
	public $ano;
	public $mesExtenso;

	public function Email(){
	
		$this->dia = date("d");
		$this->mes = date("m"); 
		$this->ano = date("Y"); 
		$this->mesExtenso = gmstrftime("%B", time());
		if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$this->destino = "email\\";}
	}	

	public function EnviarEmail($nomeDaMensagem, $listaQuotas, $EnviarOuVisualizar) { 
	
		$this->listaDeQuotas = explode(';', $listaQuotas);
		$this->enviaOUvisualiza = $EnviarOuVisualizar;
	
		if ($nomeDaMensagem == "Agradecimento"){ $this->Email_Agradecimento(); return; }
		if ($nomeDaMensagem == "Sorteio")      { $this->Email_Sorteio(); return; }
		if ($nomeDaMensagem == "Atraso")       { $this->Email_Atraso();	return;	}
		return;
	} 
	private function Email_Sorteio(){
	}
	
	private function Email_Atraso(){
	}
	
	private function Email_Agradecimento(){
	
		$arquiOri = fopen($this->destino."Agradecimento.txt", "rb");
		$arquiTra = fread($arquiOri,1000000);
		fclose($arquiOri);
				
		$header  		 = "<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
		$trailer 		 =  "</header></html>";
		$arquivoTrabalho = $header.$arquiTra.$trailer;
		
		include "Conn.php";	
#------ IDENTIFICAR A CAMPANHA CORRENTE
		$sql = "select * from sce_evento";
		$exe = mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_num_rows($exe);
		while ($row = mysql_fetch_assoc($exe)){
			$this->numeroDaCampanha = $row['EVE_ID'];
		}
				
		$data = $this->dia." de ".$this->mesExtenso." de ".$this->ano;
		$arquivoTrabalho = str_replace("[dataAtual]",$data,$arquivoTrabalho);
		
		$sqlPremio = "SELECT PRM_DT_SORTEIO, PRM_DT_QUITACAO, PRM_PARCELA, PRM_NM_PREMIO FROM sce_premios "
		           . "       WHERE PRM_ID_EVENTO = ".$this->numeroDaCampanha." AND PRM_DT_SORTEIO >= '".$ano."-".$mes."-".$dia."'";
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
								
		$arquivoTrabalho = str_replace("[dataProxSorteio]",$dataProxSorteio,$arquivoTrabalho);
		$arquivoTrabalho = str_replace("[nomePremio]",$premioProxSorteio,$arquivoTrabalho);
		$arquivoTrabalho = str_replace("[parcelaPremio]",$parcelaProxSorteio,$arquivoTrabalho);
		$arquivoTrabalho = str_replace("[dataPagamento]",$dataQuitarProxSorteio,$arquivoTrabalho);

#------ SELECIONAR TODAS AS QUOTAS COM PARCELAS PAGAS		
		$sql = ""
		." SELECT COUNT(1) As parcelas, C.CTB_ID, C.CTB_NOME,C.CTB_EMAIL,Q.QUO_ID_QUOTA,Q.QUO_TOTAL_PARCELAS AS totalParcelas "
		." FROM sce_contribuinte AS C, sce_quota AS Q, sce_titulo AS T "
		." Where Q.QUO_ID_EVENTO =  7 "
		." AND Q.QUO_ID_CONTRIBUINTE = C.CTB_ID "
		." AND C.CTB_EMAIL <> '' "
		." AND T.TIT_QUO_ID = Q.QUO_ID_QUOTA "
		." AND T.TIT_EMP_ID = Q.QUO_ID_EMPRESA " 
		." AND T.TIT_EVN_ID = Q.QUO_ID_EVENTO "
		." AND T.TIT_VALOR_RECEBIDO > 0 "
		." AND T.TIT_DATA_COMPENSACAO <= '".$this->ano."-".$this->mes."-".$this->dia."'"
		." GROUP BY QUO_ID_QUOTA "
		." ORDER BY QUO_ID_QUOTA ASC ";
		$exe = mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_num_rows($exe);
		while ($row = mysql_fetch_assoc($exe)){
#---------- SELECIONAR APENAS AS QUOTAS INFORMADAS PELO USUARIO NO ARRAY listaDeQuotas
			if (array_search($row['QUO_ID_QUOTA'], $this->listaDeQuotas)!== FALSE){
				$mensagemHtml = $arquivoTrabalho;
				$destino      = $row['CTB_EMAIL'];
				$nome         = $row['CTB_NOME'];
				$carne        = $row['QUO_ID_QUOTA'];
				$mensagemHtml = str_replace("[nomeContribuinte]",$nome,$mensagemHtml);
				$mensagemHtml = str_replace("[numeroCarne]",$carne,$mensagemHtml);
#-------------- SELECIONAR OS TITULOS DA QUOTA				
				$sqlTitulos = ""
				."SELECT TIT_EVN_ID, TIT_QUO_ID, SUBSTR(TIT_NOSSO_NUMERO,16,2) AS PARCELA, "
				."TIT_VALOR_TITULO, TIT_VALOR_RECEBIDO, TIT_VALOR_DESCONTOS, TIT_DATA_LANCAMENTO, TIT_STATUS "
				."   FROM sce_titulo WHERE TIT_EVN_ID = ".$this->numeroDaCampanha." AND TIT_QUO_ID = ".$row['QUO_ID_QUOTA']." AND TIT_VALOR_RECEBIDO > 0 ";		
				$totalTitulos = 0;
				$exeTitulos = mysql_query($sqlTitulos, $conn) or die(mysql_error());
				$rowTitulos = mysql_num_rows($exeTitulos);
				while ($rowTitulos = mysql_fetch_assoc($exeTitulos))			{
					$totalTitulos += 1;
					$sequenciaParcela    = sprintf("%02d",$totalTitulos);
					$sequenciaVencimento = "vencimento".sprintf("%02d",$totalTitulos);
					$sequenciaValor      = "valorParcela".sprintf("%02d",$totalTitulos);
					$valor               = $rowTitulos['TIT_VALOR_TITULO'];
					if ($rowTitulos['PARCELA'] == 00) {$valor = $globalValorDaParcelaUnica;};
					$sequenciaPagamento = "formaPagto".sprintf("%02d",$totalTitulos);
					$dataVencimento = substr($rowTitulos['TIT_DATA_LANCAMENTO'],8,2)."/"
									 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],5,2)."/"
									 .substr($rowTitulos['TIT_DATA_LANCAMENTO'],0,4);
					$situacaoTitulo = "PAGO";
					if ($rowTitulos['TIT_VALOR_RECEBIDO'] == 0){$situacaoTitulo = "PENDENTE";}
					$mensagemHtml = str_replace("[parcela".$sequenciaParcela."]",$rowTitulos['PARCELA'],$mensagemHtml);
					$mensagemHtml = str_replace("[vencimento".$sequenciaParcela."]", $dataVencimento, $mensagemHtml);
					$mensagemHtml = str_replace("[valorParcela".$sequenciaParcela."]", sprintf("%01.2f",$valor),$mensagemHtml);
					$mensagemHtml = str_replace("[formaPagto".$sequenciaParcela."]",$situacaoTitulo,$mensagemHtml);
					$valorApurado += $rowTitulos['TIT_VALOR_TITULO'];
				}
				for ($i = 1; $i <= 20; $i++) {
					$sequenciaParcela    = sprintf("%02d",$i);
					$sequenciaVencimento = "vencimento".sprintf("%02d",$i);
					$sequenciaValor      = "valorParcela".sprintf("%02d",$i);
					$sequenciaPagamento  = "formaPagto".sprintf("%02d",$i);
					$mensagemHtml        = str_replace("[parcela".$sequenciaParcela."]"," ",$mensagemHtml);
					$mensagemHtml        = str_replace("[vencimento".$sequenciaParcela."]", " ", $mensagemHtml);
					$mensagemHtml        = str_replace("[valorParcela".$sequenciaParcela."]", " ",$mensagemHtml);
					$mensagemHtml        = str_replace("[formaPagto".$sequenciaParcela."]", " ",$mensagemHtml);
				}
#-------------- COMPOR DADOS PARA ENVIAR O EMAIL				
				$assunto  = "Agradecimento";
				$mensagem = $mensagemHtml;
				$headers  = "Content-Type:text/html; charset=utf-8\r\n";
				$headers .= "From: ".$this->origem."\r\n";
				$headers .= "Reply-To: ".$this->resposta."\r\n";
				$destino = "jmosousa@yahoo.com.br";
				if ($this->enviaOUvisualiza == "Visualizar"){
					echo $mensagem;
				}
				else {
					if (mail($destino, $assunto, $mensagem, $headers) == false) {
						echo "Ocorreu um erro durante o envio do email.";
					}
				}
			}
		}
		return;
	}

}

?>
