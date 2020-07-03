<?php
require_once('busConsistirPemissao.php');
require_once('Conn.php');	
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i')."00";
$contImpressao = 0;
include("BoletoBB_funcoes.php");

$sql = "select * from sce_configuracao";
$exe = mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exe);
while ($row = mysql_fetch_assoc($exe)){
	$CedenteCampanha    		= sprintf("%03d", $row['campanha']);
	$CedenteNome 				= $row['instituicao'];
	$CedenteAgencia				= $row['agencia'];		## Agencia sem o digito
	$CedenteConta               = $row['conta'];		## Conta sem o digito
	$CedenteConvenio            = $row['convenio'];		## Numero do convenio para cobranca via boleto
}


#$CedenteCampanha    		= sprintf("%03d", $globalEvento);
$ContribuinteQuotaInicio    = sprintf("%05d", $_POST['QuotaInicio']);
$ContribuinteQuotaFim 		= sprintf("%05d", $_POST['QuotaFim']);
$ContribuinteParcelaInicio	= sprintf("%02d", $_POST['ParcelaInicio']);
$ContribuinteParcelaFim		= sprintf("%02d", $_POST['ParcelaFim']);
if ($ContribuinteQuotaFim == "") {$ContribuinteQuotaFim = $ContribuinteQuotaInicio;};
if ($ContribuinteParcelaFim < $ContribuinteParcelaInicio){$ContribuinteParcelaFim = $ContribuinteParcelaInicio;};

#$CedenteNome 				= "MITRA ARQUIDIOCESANA DE BRASILIA/Parôquia Nossa Senhora da Assunção";
#$CedenteAgencia 			= "4733";				// Agencia sem digito
#$CedenteConta   			= "12000";				// Conta sem digito		Campanha
#$CedenteConvenio			= "1139283";			// Numero do convênio 	Campanha
$CedenteEspecie 			= "R$";					// Moeda
$CedenteAceite				= "N";
$CedenteEspecieDocumento	= "DM";
$CedenteCarteira			= "18";
$CedenteCarteiraVariacao	= "";
$CedenteQuantidade 			= "";
$CedenteValorUnitario		= "";

$PremioDataDoSorteio = Array();
$PremioNome = Array();
$PremioDataQuitacao = Array();

$sql = "SELECT * FROM sce_premios WHERE PRM_ID_EVENTO = ".$CedenteCampanha. " ORDER BY PRM_PARCELA ASC";
$exec =  mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec)){
	$PremioDataDoSorteio[$row['PRM_PARCELA']] = $row['PRM_DT_SORTEIO'];
	$PremioNome[$row['PRM_PARCELA']] = $row['PRM_DS_PREMIO'];
	$PremioDataQuitacao[$row['PRM_PARCELA']] = $row['PRM_DT_QUITACAO'];
}

for ( $c = $ContribuinteQuotaInicio; $c <= $ContribuinteQuotaFim; $c += 1) {
	$ContribuinteQuota = sprintf("%05d", $c);
	
	$BoletoGerado = 0;
	for ( $p = $ContribuinteParcelaInicio; $p <= $ContribuinteParcelaFim; $p += 1) {

		$Parcela = sprintf("%02d", $p);
		$ContribuinteTitulo         = $CedenteCampanha.$ContribuinteQuota.$Parcela;

		$sql = "SELECT * FROM sce_titulo WHERE TIT_EVN_ID = ".$CedenteCampanha." AND TIT_NUMERO_DOCUMENTO = ".$ContribuinteTitulo;
		$exec =  mysql_query($sql, $conn) or die(mysql_error());
		$achoTitulos = mysql_affected_rows();
		$row = mysql_num_rows($exec);
		while ($row = mysql_fetch_assoc($exec))	{
			$ContribuinteCodigo 		= $row['TIT_CLN_ID'];
			$TituloParcela				= $Parcela;
			$TituloValorCobrado			= $row['TIT_VALOR_TITULO'];
			$TituloAnoMesDocumento		= $row['TIT_ANO_MES_DOCUMENTO'];
		}
		if ($achoTitulos == 0){
			$ContribuinteCodigo 		= 0;
			$TituloParcela				= $Parcela;
			$TituloValorCobrado			= $globalValorDaParcela;
			$TituloAnoMesDocumento		= substr($PremioDataQuitacao[$Parcela],0,4).substr($PremioDataQuitacao[$Parcela],5,2);
			if ($p == 0){$TituloValorCobrado = $globalValorDaParcelaUnica;};
		}
# VALIDAR CODIGO DE BARRAS OU LINHA DIGITAVEL     	http://evandro.net/codigo_barras.html
########################################################
#123456789012345678901234567890123456789012345678901234#
#        10        20        30        40        50    #
#00190.00009 01139.283004 70300.101188 8 00000000005500#
#            ---------     --------                    #
#            Convenio	   Quota e Parcela             #
########################################################
# TESTE BOLETO SEM VENCIMENTO E SEM VALOR PARA DIZIMO
#$CedenteAgencia 			= "4733";				// Agencia sem digito
#$CedenteConta   			= "21000";				// Conta sem digito		Dízimo teste
#$CedenteConvenio			= "2523164";			// Numero do convênio 	Dízimo teste
#$TituloValorCobrado = 0.00;

		$TituloNossoNumero 			= $CedenteCampanha.$ContribuinteQuota.$TituloParcela;
		$TituloNossoDocumento 		= $CedenteCampanha.$ContribuinteQuota.$TituloParcela;
		$TituloDataVencimento		= "07/10/1997";
		$TitutloDiasParaPagamento   = "0";
		$TituloTaxaBoleto			= "0,00";

		$sql = "SELECT * FROM sce_contribuinte WHERE CTB_ID = ".$ContribuinteCodigo;
		$exec =  mysql_query($sql, $conn) or die(mysql_error());
		$row = mysql_num_rows($exec);
		$ContribuinteNome = "";
		$ContribuinteEndereco = "";
		$ContribuinteCidade = "";
		$ContribuinteEstado = "";
		$ContribuinteCEP = "";		
		while ($row = mysql_fetch_assoc($exec))	{
			$ContribuinteNome = $row['CTB_NOME'];
			$ContribuinteEndereco = $row['CTB_ENDERECO'];
			$ContribuinteCidade = $row['CTB_MUNICIPIO'];
			$ContribuinteEstado = $row['CTB_UF'];
			$ContribuinteCEP = $row['CTB_CEP'];
		}
		if ($achoTitulos == 0){
			$ContribuinteNome = "Carnê: ".$ContribuinteQuota;
		}
	// +----------------------------------------------------------------------+
	// | BoletoPhp - Versão Beta                                              |
	// +----------------------------------------------------------------------+
	// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
	// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
	// | Você deve ter recebido uma cópia da GNU Public License junto com     |
	// | esse pacote; se não, escreva para:                                   |
	// |                                                                      |
	// | Free Software Foundation, Inc.                                       |
	// | 59 Temple Place - Suite 330                                          |
	// | Boston, MA 02111-1307, USA.                                          |
	// +----------------------------------------------------------------------+

	// +----------------------------------------------------------------------+
	// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
	// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
	// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				        |
	// | 														                                   			  |
	// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
	// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
	// +----------------------------------------------------------------------+

	// +--------------------------------------------------------------------------------------------------------+
	// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>              		             				|
	// | Desenvolvimento Boleto Banco do Brasil: Daniel William Schultz / Leandro Maniezo / Rogério Dias Pereira|
	// +--------------------------------------------------------------------------------------------------------+


	// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
	// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

	// DADOS DO BOLETO PARA O SEU CLIENTE
		$dias_de_prazo_para_pagamento 	= $TitutloDiasParaPagamento;
		$taxa_boleto 					= $TituloTaxaBoleto;
		$data_venc 						= date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));
		$valor_cobrado 					= $TituloValorCobrado;
		$valor_cobrado 					= str_replace(",", ".",$valor_cobrado);
		$valor_boleto					= number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

		$dadosboleto["nosso_numero"] 		= $TituloNossoNumero;
		$dadosboleto["numero_documento"] 	= $TituloNossoDocumento;
		$dadosboleto["data_vencimento"] 	= $TituloDataVencimento;
		$dadosboleto["data_documento"] 		= date("d/m/Y"); // Data de emissão do Boleto
		$dadosboleto["data_processamento"] 	= date("d/m/Y"); // Data de processamento do boleto (opcional)
		$dadosboleto["valor_boleto"] 		= $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

	// DADOS DO SEU CLIENTE
		$dadosboleto["sacado"] 				= $ContribuinteNome;
		$dadosboleto["endereco1"] 			= $ContribuinteEndereco;
		$dadosboleto["endereco2"] 			= $ContribuinteCidade. " - ".$ContribuinteEstado." -  CEP: ".$ContribuinteCEP;

	// INFORMACOES PARA O CLIENTE
		$dadosboleto["demonstrativo1"] 		= "- NÃO COBRAR JUROS APÓS O VENCIMENTO";
		$dadosboleto["demonstrativo2"] 		= "- Parcela: ".$Parcela
											 ." - Mês/Ano: ".substr($TituloAnoMesDocumento, 4, 2)."/".substr($TituloAnoMesDocumento, 0, 4)
											 ." - Data do Sorteio: ".substr($PremioDataDoSorteio[$Parcela],8,2)
											 ."/".substr($PremioDataDoSorteio[$Parcela],5,2)
											 ."/".substr($PremioDataDoSorteio[$Parcela],0,4);
		$dadosboleto["demonstrativo3"] 		= "- Prêmio: ".$PremioNome[$Parcela];

	// INSTRUÇÕES PARA O CAIXA
		$dadosboleto["instrucoes1"] 		= "&nbsp;";
		$dadosboleto["instrucoes2"] 		= "Para concorrer a este sorteio esta parcela, assim como as anteriores, deverão"
											 ." ser quitadas até o DIA: ".substr($PremioDataQuitacao[$Parcela],8,2)
											 ."/".substr($PremioDataQuitacao[$Parcela],5,2)
											 ."/".substr($PremioDataQuitacao[$Parcela],0,4);

	// INSTRUCOES PARA PARCELA UNICA
		if ($p == 0){
			$dadosboleto["demonstrativo2"] = "- COTA ÚNICA";
			$dadosboleto["demonstrativo3"] = "Para concorrer aos sorteios seguintes, esta "
											."parcela única deverá estar quitada até a data "
											."limite para pagamento constante do anexo ao "
											."regulamento.";
			$dadosboleto["instrucoes2"] = "- Ao pagar esta COTA ÚNICA, não é preciso pagar os demais boletos. ";
		}

		$dadosboleto["instrucoes3"] 		= "&nbsp;";
		$dadosboleto["instrucoes4"] 		= "Atendimento ao contribuinte: (61) 3376-0101 ou paroquia_cristorei@yahoo.com.br";

	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		$dadosboleto["quantidade"] 			= $CedenteQuantidade;
		$dadosboleto["valor_unitario"] 		= $CedenteValorUnitario;
		$dadosboleto["aceite"] 				= $CedenteAceite;
		$dadosboleto["especie"] 			= $CedenteEspecie;
		$dadosboleto["especie_doc"] 		= $CedenteEspecieDocumento;


	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


	// DADOS DA SUA CONTA - BANCO DO BRASIL
		$dadosboleto["agencia"] 			= $CedenteAgencia;
		$dadosboleto["conta"]   			= $CedenteConta;

	// DADOS PERSONALIZADOS - BANCO DO BRASIL
		$dadosboleto["convenio"] 			= $CedenteConvenio;
		$dadosboleto["contrato"] 			= "019116584"; // Num do seu contrato
		$dadosboleto["carteira"] 			= $CedenteCarteira;
		$dadosboleto["variacao_carteira"] 	= $CedenteCarteiraVariacao;

	// TIPO DO BOLETO
		$dadosboleto["formatacao_convenio"] = "7"; // REGRA: 8 p/ Convênio c/ 8 dígitos, 7 p/ Convênio c/ 7 dígitos, ou 6 se Convênio c/ 6 dígitos
		$dadosboleto["formatacao_nosso_numero"] = "2"; // REGRA: Usado apenas p/ Convênio c/ 6 dígitos: informe 1 se for NossoNúmero de até 5 dígitos ou 2 para opção de até 17 dígitos

	/*
	#################################################
	DESENVOLVIDO PARA CARTEIRA 18

	- Carteira 18 com Convenio de 8 digitos
	  Nosso número: pode ser até 9 dígitos

	- Carteira 18 com Convenio de 7 digitos
	  Nosso número: pode ser até 10 dígitos

	- Carteira 18 com Convenio de 6 digitos
	  Nosso número:
	  de 1 a 99999 para opção de até 5 dígitos
	  de 1 a 99999999999999999 para opção de até 17 dígitos

	#################################################
	*/


	// SEUS DADOS
		$dadosboleto["identificacao"] = "";			// "BoletoPhp - Código Aberto de Sistema de Boletos"
		$dadosboleto["cpf_cnpj"] = "";
		$dadosboleto["endereco"] = "";				// "Coloque o endereço da sua empresa aqui"
		$dadosboleto["cidade_uf"] = "";				// "Cidade / Estado"
		$dadosboleto["cedente"] = $CedenteNome;

		$codigobanco = "001";
		$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);

	// Alterado por José Maria Boleto sem data de vencimento
		if ($fator_vencimento == "0") {
			$fator_vencimento = "0000";
			$dadosboleto["data_vencimento"] = "Contra apresentação";
		}

	//valor tem 10 digitos, sem virgula
		$valor = formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
	//agencia é sempre 4 digitos
		$agencia = formata_numero($dadosboleto["agencia"],4,0);
	//conta é sempre 8 digitos
		$conta = formata_numero($dadosboleto["conta"],8,0);
	//carteira 18
		$carteira = $dadosboleto["carteira"];
	//agencia e conta
		$agencia_codigo = $agencia."-". modulo_11($agencia) ." / ". $conta ."-". modulo_11($conta);
	//Zeros: usado quando convenio de 7 digitos
		$livre_zeros='000000';

	// Carteira 18 com Convênio de 8 dígitos

		if ($dadosboleto["formatacao_convenio"] == "8") {
			$convenio = formata_numero($dadosboleto["convenio"],8,0,"convenio");
			// Nosso número de até 9 dígitos
			$nossonumero = formata_numero($dadosboleto["nosso_numero"],9,0);
			$dv=modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
			$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
			//montando o nosso numero que aparecerá no boleto
			$nossonumero = $convenio . $nossonumero ."-". modulo_11($convenio.$nossonumero);
		}

	// Carteira 18 com Convênio de 7 dígitos
		if ($dadosboleto["formatacao_convenio"] == "7") {
			$convenio = formata_numero($dadosboleto["convenio"],7,0,"convenio");
			// Nosso número de até 10 dígitos
			$nossonumero = formata_numero($dadosboleto["nosso_numero"],10,0);
			$dv=modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
			$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
		  $nossonumero = $convenio.$nossonumero;
			//Não existe DV na composição do nosso-número para convênios de sete posições
		}

	// Carteira 18 com Convênio de 6 dígitos
		if ($dadosboleto["formatacao_convenio"] == "6") {
			$convenio = formata_numero($dadosboleto["convenio"],6,0,"convenio");

			if ($dadosboleto["formatacao_nosso_numero"] == "1") {

				// Nosso número de até 5 dígitos
				$nossonumero = formata_numero($dadosboleto["nosso_numero"],5,0);
				$dv = modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira");
				$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira";
				//montando o nosso numero que aparecerá no boleto
				$nossonumero = $convenio . $nossonumero ."-". modulo_11($convenio.$nossonumero);
			}

			if ($dadosboleto["formatacao_nosso_numero"] == "2") {

				// Nosso número de até 17 dígitos
				$nservico = "21";
				$nossonumero = formata_numero($dadosboleto["nosso_numero"],17,0);
				$dv = modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$nservico");
				$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$nservico";
			}
		}

		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		$dadosboleto["nosso_numero"] = $nossonumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
		include("BoletoBB_layout.php");

		$contImpressao += 1;
		if ($contImpressao == 2){
			$contImpressao =  0;
			echo "<div class='Z'></div>";
		}

	}
}
?>
