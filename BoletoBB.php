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
	$CedenteAgencia				= $row['agencia'];		// Agencia sem o digito
	$CedenteConta               = $row['conta'];		// Conta sem o digito
	$CedenteConvenio            = $row['convenio'];		// Numero do convenio para cobranca via boleto
}

$CedenteCampanha    		= sprintf("%03d", $globalEvento);
$ContribuinteQuotaInicio    = sprintf("%05d", $_POST['QuotaInicio']);
$ContribuinteQuotaFim 		= $ContribuinteQuotaInicio;

$sql = "SELECT * FROM sce_titulo WHERE TIT_FORMA_PAG = 'B' AND TIT_QUO_ID = ".$ContribuinteQuotaInicio."";
$exec =  mysql_query($sql, $conn) or die(mysql_error());
$achou = mysql_affected_rows();
$row = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec))	{
		$TitNumDoc = $row['TIT_NUMERO_DOCUMENTO'];
		$Parcela   = $row['TIT_PARCELA'];  
	}

if ($achou > 0){
	if ($Parcela == 0){
		$ContribuinteParcelaInicio	= 0;
		$ContribuinteParcelaFim     = 0;
	}
	else{
		$ContribuinteParcelaInicio	= 1;
		$ContribuinteParcelaFim     = 10;
	}

	if ($ContribuinteQuotaFim == "") {$ContribuinteQuotaFim = $ContribuinteQuotaInicio;};
	if ($ContribuinteParcelaFim < $ContribuinteParcelaInicio){$ContribuinteParcelaFim = $ContribuinteParcelaInicio;};

	$CedenteConvenioDv			= "0066044";	// Numero do convênio 	Campanha
	$CedenteEspecie 			= "R$";		// Moeda
	$CedenteAceite				= "N";
	$CedenteEspecieDocumento	= "DM";
	$CedenteCarteira			= "1";
	$CedenteCarteiraVariacao	= "";
	$CedenteQuantidade 			= "";
	$CedenteValorUnitario		= "";
	$modalidade                 = "01";

	$PremioDataDoSorteio = Array();
	$PremioNome = Array();
	$PremioDataQuitacao = Array();

	$sql = "SELECT * FROM sce_premios WHERE PRM_ID_EVENTO = 4 ORDER BY PRM_PARCELA ASC";
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
			if ($Parcela == 0){$Parcela = sprintf("%02d", "01");}	
			else {$Parcela = sprintf("%02d", $p);}
			$ContribuinteTitulo = $ContribuinteQuota.$Parcela;
			
			$sql = "SELECT * FROM sce_titulo WHERE TIT_EVN_ID = ".$CedenteCampanha." AND TIT_NUMERO_DOCUMENTO = ".$ContribuinteTitulo.""	;
			$exec =  mysql_query($sql, $conn) or die(mysql_error());
			$achoTitulos = mysql_affected_rows();
			$row = mysql_num_rows($exec);
			while ($row = mysql_fetch_assoc($exec))	{
				$ContribuinteCodigo 		= $row['TIT_CLN_ID'];
				$TituloParcela				= $row['TIT_PARCELA'];
				$TituloValorCobrado			= $row['TIT_VALOR_TITULO'];
				$TituloAnoMesDocumento		= $row['TIT_ANO_MES_DOCUMENTO'];
				$TituloStatus				= $row['TIT_STATUS'];
				$TitID	 					= $row['TIT_ID'];
				$TituloNossoNumero          = $row['TIT_NOSSO_NUMERO'];
			}

			$NumTitulo = str_pad($TituloNossoNumero,7,"0", STR_PAD_LEFT);	
			
			if ($TituloStatus <> 9){
		
	// VALIDAR CODIGO DE BARRAS OU LINHA DIGITAVEL     	http://evandro.net/codigo_barras.html

				$TituloNossoDocumento 		= $TituloNossoNumero;
				$GuardaTitID	            = $TitID;
				$ParcelaVenc = $TituloParcela; 
				//echo $ParcelaVenc;
				$TituloDataVencimento = dataVenc($ParcelaVenc);
						
				$TitutloDiasParaPagamento   = "0";
				$TituloTaxaBoleto			= "0,00";

				if ($Parcela == 0){
					$Parcela0 = "01"; 
					$Titulo = $CedenteCampanha.$ContribuinteQuota.$Parcela0;
					$sql = "SELECT * FROM sce_titulo WHERE TIT_EVN_ID = ".$CedenteCampanha." AND TIT_NUMERO_DOCUMENTO = ".$Titulo	;
					$exec =  mysql_query($sql, $conn) or die(mysql_error());
					$req = mysql_num_rows($exec);
					while ($reg = mysql_fetch_assoc($exec)){
						$ContribuinteCodigo 		= $reg['TIT_CLN_ID'];
					}
				}

				$sql = "SELECT * FROM sce_contribuinte WHERE CTB_ID = ".$ContribuinteCodigo;
				$exec =  mysql_query($sql, $conn) or die(mysql_error());
				$row = mysql_num_rows($exec);
				$ContribuinteNome = "";
				$ContribuinteCPF;
				$ContribuinteEndereco = "";
				$ContribuinteCidade = "";
				$ContribuinteEstado = "";
				$ContribuinteCEP = "";	
				while ($row = mysql_fetch_assoc($exec))	{
					$ContribuinteNome = $row['CTB_NOME'];
					$ContribuinteCPF = $row['CTB_CPF'];
					$ContribuinteEndereco = $row['CTB_ENDERECO'];
					$ContribuinteCidade = $row['CTB_MUNICIPIO'];
					$ContribuinteEstado = $row['CTB_UF'];
					$ContribuinteCEP = $row['CTB_CEP'];
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

				$taxa_boleto 					= $TituloTaxaBoleto;
				$valor_cobrado 					= $TituloValorCobrado;
				$valor_cobrado 					= str_replace(",", ".",$valor_cobrado);
				$valor_boleto					= number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

				if ($Parcela == 0){$Parcela = "01";}
		
				$Dv_NN = DvNN("43320000066044".$NumTitulo);
				$dadosboleto["nosso_numero"] 		= $NumTitulo."-".$Dv_NN;
				$dadosboleto["numero_documento"] 	= $TituloNossoDocumento;
				$dadosboleto["data_vencimento"] 	= $TituloDataVencimento;
				$dadosboleto["data_documento"] 		= date("d/m/Y"); // Data de emissão do Boleto
				$dadosboleto["data_processamento"] 	= date("d/m/Y"); // Data de processamento do boleto (opcional)
				$dadosboleto["valor_boleto"] 		= $valor_boleto; // Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

	// DADOS DO SEU CLIENTE
	
				$dadosboleto["sacado"] 				= $ContribuinteNome. ".        - CPF: ".$ContribuinteCPF;
				$dadosboleto["endereco1"] 			= $ContribuinteEndereco;
				$dadosboleto["endereco2"] 			= $ContribuinteCidade. " - ".$ContribuinteEstado." -  CEP: ".$ContribuinteCEP;
		
	// INFORMACOES PARA O CLIENTE

				$dadosboleto["demonstrativo1"] 		= "- NÃO COBRAR JUROS APÓS O VENCIMENTO";
				$dadosboleto["demonstrativo2"] 		= "- Parcela: ".$Parcela;
	
	// INSTRUÇÕES PARA O CAIXA
				
				$dadosboleto["instrucoes1"] 		= "&nbsp;";
		
				if (substr($PremioNome[$Parcela],0,6) == "NIHILL") {
					$dadosboleto["demonstrativo3"] 	= "";
					$dadosboleto["instrucoes2"] 	= ""; }
				else {
					$dadosboleto["demonstrativo3"] 	= "- Prêmio: ".$PremioNome[$Parcela];
					$dadosboleto["instrucoes2"] 	= "Para concorrer a este sorteio, esta parcela assim como as anteriores, deverão"
											 ." ser quitadas até o DIA: ".substr($PremioDataQuitacao[$Parcela],8,2)
											 ."/".substr($PremioDataQuitacao[$Parcela],5,2)
											 ."/".substr($PremioDataQuitacao[$Parcela],0,4);
				}	
	// INSTRUCOES PARA PARCELA UNICA
	
				if ($p == 0){
					$dadosboleto["demonstrativo2"] = "- COTA ÚNICA";
					$dadosboleto["demonstrativo3"] = "Para concorrer aos sorteios seguintes, esta "
											."parcela única deverá estar quitada até a data "
											."limite para pagamento constante do anexo ao "
											."regulamento.";
					$dadosboleto["instrucoes2"] = "- Ao pagar esta COTA ÚNICA, não é preciso pagar os demais boletos. ";
					$dadosboleto["data_vencimento"] = $TituloDataVencimento;
				}

	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		
				$dadosboleto["quantidade"] 			= $CedenteQuantidade;
				$dadosboleto["valor_unitario"] 		= $CedenteValorUnitario;
				$dadosboleto["aceite"] 				= $CedenteAceite;
				$dadosboleto["especie"] 			= $CedenteEspecie;
				$dadosboleto["especie_doc"] 		= $CedenteEspecieDocumento;


	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

	// DADOS DA SUA CONTA - SICOOB

				$dadosboleto["agencia"] 			= $CedenteAgencia;
				$dadosboleto["conta"]   			= $CedenteConta;

	// DADOS PERSONALIZADOS - SICOOB
		
				$dadosboleto["convenio"] 			= $CedenteConvenio;
				$dadosboleto["contrato"] 			= "6604-4";
				$dadosboleto["carteira"] 			= $CedenteCarteira;
				$dadosboleto["variacao_carteira"] 	= $CedenteCarteiraVariacao;

	// TIPO DO BOLETO

				$dadosboleto["formatacao_convenio"] = "7"; // REGRA: 8 p/ Convênio c/ 8 dígitos, 7 p/ Convênio c/ 7 dígitos, ou 6 se Convênio c/ 6 dígitos
	
	// DESENVOLVIDO PARA CARTEIRA 1
	
	// SEUS DADOS
		
				$dadosboleto["identificacao"] = "";			// "BoletoPhp - Código Aberto de Sistema de Boletos"
				$dadosboleto["cpf_cnpj"] = "00.108.217/0150-60";
				$dadosboleto["endereco"] = "Quadra 107, Rua das Aroeiras Lt. 3 - Águas Claras DF - CEP: 71920-700 _____________________________________________________________________";	// "Coloque o endereço da sua empresa aqui"
				$dadosboleto["cidade_uf"] = "";				// "Cidade / Estado"
				$dadosboleto["cedente"] = $CedenteNome;

				$codigobanco = "756";
				$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
				$nummoeda = "9";
				$fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);
		
	// valor tem 10 digitos, sem virgula
				$valor = formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
	// carteira 1
				$carteira = $dadosboleto["carteira"];
	// agencia e conta
				$agencia_codigo = $CedenteAgencia."-0 / 6604-4";
	// Carteira 1 com Convênio de 7 dígitos
				if ($dadosboleto["formatacao_convenio"] == "7") {
	// Nosso número 8 dígitos	
					$Parcela = str_pad($Parcela,3,"0", STR_PAD_LEFT);		
					$dv= modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$carteira$CedenteAgencia$modalidade$CedenteConvenioDv$NumTitulo$Dv_NN$Parcela");
					$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$carteira$CedenteAgencia$modalidade$CedenteConvenioDv$NumTitulo$Dv_NN$Parcela";

			//Banco/Moeda/DV Cod Barras/Fator Venc./Valor/Carteira/Coop./Modalidade/Cliente/Nosso Número/Parcela		
				}

				$dadosboleto["codigo_barras"] = $linha;
				$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
				$dadosboleto["agencia_codigo"] = $agencia_codigo;
				$Dv_NN = DvNN("43320000066044".$NumTitulo);
				$dadosboleto["nosso_numero"] = $NumTitulo."-".$Dv_NN;
				$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
				include("BoletoBB_layout.php");	
				$contImpressao += 1;
				if ($contImpressao == 2){
					$contImpressao =  0;
					echo "<div class='Z'></div>";
				}
	// Atualiza tabela Título para gerar arquivo de cadastro dos boletos			
				$Grav = "N";
				$sql = "UPDATE sce_titulo SET TIT_REM_GRAVADA = '$Grav' WHERE TIT_ID = $GuardaTitID"; 
				$exe = mysql_query($sql, $conn) or die(mysql_error());
			}
		}		
	}
}
else{ 
	echo "-----------------------------------------------------------------------------------------------------------------------------------------------------------"."<br>";
	echo "QUOTA NÃO CADASTRADA OU NÃO É PARA PAGAMENTO VIA BOLETO OU PARCELAS JÁ FORAM PAGAS."."<br>";
	echo "-----------------------------------------------------------------------------------------------------------------------------------------------------------"."<br>";
}
?>
