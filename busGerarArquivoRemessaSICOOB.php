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
include("BoletoBB_funcoes.php");

if (PHP_OS == "WIN32" || PHP_OS == "WINNT" || PHP_OS == "WIN64") {
	$destino = './';
}	

$sql = "SELECT MAX(REMESSA_ID) as Remessa FROM sce_remessa WHERE EVENTO_ID = 4";
$exec = mysql_query($sql, $conn) or die(mysql_error());
$row  = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec))	{ $numeroDaRemessa = $row['Remessa'] + 1;};

$NumRemessa = str_pad($numeroDaRemessa,4,"0", STR_PAD_LEFT);
$nomeArquivoSICOOB = "Remessa-PSJPII-SICOOB-R".$NumRemessa.".txt";
$dataArquivoSICOOB = $dia."/".$mes."/".$ano;
$arquivoAberto = fopen($destino.$nomeArquivoSICOOB,"w");

$regGrav = 0;
$Banco = "756";	  // Código do banco SICOOB

// Compor HEADER

$Lote_serviço = "0000";
$Registro = "0";
$CNAB1 = str_repeat(" ",9);
$Insc_Tipo = "2";					// Indica contrato com Pessoa Juridica
$NumeroCNPJ = "00108217015060";		// CNPJ do contrato 
$Convenio = str_repeat(" ",20);     
$Agencia = "043320";
$Conta = str_pad("47600",13,"0", STR_PAD_LEFT);
$DV_Ag_Cta = str_repeat(" ",1);	     										
$Empresa = str_pad("MITRA ARQUIDIOCESANA BRASILIA",30," ");
$Nome_Banco = str_pad("SICOOB",30," ");
$CNAB2 = str_repeat(" ",10);
$Codigo = "1";													 // Indica arquivo de debito
$Data_Geracao = $dia.$mes.$ano;									 // Data do arquivo no formato ddmmaaaa
$Hora_Geracao = str_pad($horaReferencia,6,"0", STR_PAD_LEFT);	 // Hora do arquivo "172618"
$Numero_Arquivo = str_pad($numeroDaRemessa,6,"0", STR_PAD_LEFT); // Numero do arquivo "000441"
$Versao = "081";												 // Versao do arquivo CNAB240 no SICOOB
$Densidade = str_repeat("0",5);
$Reservado_Banco = str_repeat(" ",20);
$Reservado_Empresa = str_repeat(" ",20);
$Reservado_CNAB = str_repeat(" ",29);

$linha = $Banco.$Lote_serviço.$Registro.$CNAB1.$Insc_Tipo.$NumeroCNPJ.$Convenio.$Agencia.$Conta."0".$Empresa.$Nome_Banco.$CNAB2.$Codigo.$Data_Geracao.$Hora_Geracao.$Numero_Arquivo.$Versao.$Densidade.$Reservado_Banco.$Reservado_Empresa.$Reservado_CNAB."\r\n";
fwrite($arquivoAberto, $linha);$regGrav +=1;

// Compor Lote

$cmdSQL = "SELECT MAX(REMESSA_ID) as Remessa FROM sce_remessa WHERE EVENTO_ID = 4";
$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
		while ($row = mysql_fetch_assoc($exec)){$Remessa = $row['Remessa'];}

$Remessa += 1;		
$Lote =	str_pad("1",4,"0", STR_PAD_LEFT);
$Registro = "1";
$Operacao = "R";
$Servico = "01";
$CNAB3 = str_repeat(" ",2);
$Layout_Lote = "040";
$CNAB4 = str_repeat(" ",1);
$Informacao = str_repeat(" ",80);
$Numero_Remessa = str_pad($numeroDaRemessa,8,"0", STR_PAD_LEFT);
$Data_Credito = str_repeat("0",8);
$CNAB5 = str_repeat(" ",33);
$linha = $Banco.$Lote.$Registro.$Operacao.$Servico.$CNAB3.$Layout_Lote.$CNAB4.$Insc_Tipo."0".$NumeroCNPJ.$Convenio.$Agencia.$Conta.$DV_Ag_Cta.$Empresa.$Informacao.$Numero_Remessa.$Data_Geracao.$Data_Credito.$CNAB5."\r\n";
fwrite($arquivoAberto, $linha);$regGrav +=1;

// Compor Seguimento P

$Registro = "3";
$Cod_Mov = "01";
$RemGravada = "N";
$FormaPag = "B";
$TituloQuota 		= Array();
$TituloNossoNum     = Array();
$TituloRemDoc     	= Array();
$TitValor			= Array();
$TitID  			= Array();

$TQuota 		= Array();
$TNossoNum      = Array();
$TRemDoc     	= Array();
$TValor			= Array();
$TID            = Array();

$sql = "SELECT * FROM sce_titulo WHERE TIT_REM_GRAVADA = '$RemGravada' AND TIT_FORMA_PAG = '$FormaPag' ORDER BY TIT_NUMERO_DOCUMENTO ASC LIMIT 0, 9";
$exec =  mysql_query($sql, $conn) or die(mysql_error());
$achou = mysql_affected_rows();

$d = 1;
$row = mysql_num_rows($exec);
ECHO $achou."=".$row;
while ($row = mysql_fetch_assoc($exec)){
	$TituloQuota[$row['TIT_NUMERO_DOCUMENTO']] 	  = $row['TIT_QUO_ID'];
	$TituloNossoNum[$row['TIT_NUMERO_DOCUMENTO']] = $row['TIT_NOSSO_NUMERO'];
	$TituloRemDoc[$row['TIT_NUMERO_DOCUMENTO']]   = $row['TIT_NUMERO_DOCUMENTO'];
	$TitValor[$row['TIT_NUMERO_DOCUMENTO']]		  = $row['TIT_VALOR_TITULO'];
	$TitID[$row['TIT_NUMERO_DOCUMENTO']]		  = $row['TIT_ID'];

	$TQuota[$d]    = $TituloQuota[$row['TIT_NUMERO_DOCUMENTO']];
	$TNossoNum[$d] = $TituloNossoNum[$row['TIT_NUMERO_DOCUMENTO']];
	$TRemDoc[$d]   = $TituloRemDoc[$row['TIT_NUMERO_DOCUMENTO']];
	$TValor[$d]	   = $TitValor[$row['TIT_NUMERO_DOCUMENTO']];
	$TID[$d]	   = $TitID[$row['TIT_NUMERO_DOCUMENTO']];

	$d = $d + 1;	
}	

$QtdeIni = 1;
$QtdeFim = $achou;

for ( $c = $QtdeIni; $c <= $QtdeFim; $c += 1) {
	
	$Regitro_Lote +=1;
	$Regitro_Lote = str_pad($Regitro_Lote,5,"0", STR_PAD_LEFT);
	
	// Montar Nosso Número
	$NumTitulo = $TNossoNum[$c];
	$Dv_NN = DvNN("43320000066044".$NumTitulo);
	
	$NumTitulo1 = $NumTitulo.$Dv_NN;
	$NumTitulo1 = str_pad($NumTitulo1,10,"0", STR_PAD_LEFT);

	if (substr($TRemDoc[$c],5,2) == "00") {
		$Parcela = "01";
		$ParcelaDT = "00";
	}
	else {
		$Parcela = substr($TRemDoc[$c],5,2);
		$ParcelaDT = substr($TRemDoc[$c],5,2);
	}

	$Modalidade = "01";
	$TipForm = "1";
	$Espaço = str_repeat(" ",5);
	$Nosso_Num = $NumTitulo1.$Parcela.$Modalidade.$TipForm.$Espaço;

	// Fim montar Nosso Número
	
	$Seguimento = "P";
	$Carteira = "1";
	$Cadastramento = "0";
	$Documento = " ";
	$EmissaoBoleto = "2";
	$DistrBoleto = "2";
	$NumDoc = str_pad($TNossoNum[$c],15,"0", STR_PAD_LEFT);
	$DataVencimento = dataVenc($ParcelaDT);
	$DiaVC = substr($DataVencimento,0,2);
	$MesVC = substr($DataVencimento,3,2);
	$AnoVC = substr($DataVencimento,6,4);
	$DataVencimento = $DiaVC.$MesVC.$AnoVC; 
	$Valor = $TValor[$c];
	$Valor = str_replace("." , "" , $Valor );
	$Valor1 = str_pad($Valor,15,"0", STR_PAD_LEFT);
	$ValorTotal = $ValorTotal + $Valor1;
	$AgCobradoraDV = "00000 ";
	$EspecieTit = "02";
	$Aceite = "N";
	$DTEmissao = $dia.$mes.$ano;
	$Juros_Desc = str_pad($NumTitulo,103,"0", STR_PAD_LEFT);
	$CodProtesto = "3";
	$PrazoProtesto = "00";
	$BaixaDevol = "0";
	$Espaço1 = str_repeat(" ",3);
	$Moeda = "09";
	$NumContrato = str_repeat("0",10);
	$Conta = str_pad("4760",12,"0", STR_PAD_LEFT)."0";
	
	$linha = $Banco.$Lote.$Registro.$Regitro_Lote.$Seguimento.$CNAB4.$Cod_Mov.$Agencia.$Conta.$DV_Ag_Cta.$Nosso_Num.$Carteira.$Cadastramento.$Documento.$EmissaoBoleto.$DistrBoleto.$NumDoc.$DataVencimento.$Valor1.$AgCobradoraDV.$EspecieTit.$Aceite.$DTEmissao.$Juros_Desc.$CodProtesto.$PrazoProtesto.$BaixaDevol.$Espaço1.$Moeda.$NumContrato.$CNAB4."\r\n";
	fwrite($arquivoAberto, $linha);
	$regGrav +=1;
	
	// Compor Seguimento Q
	
	$Regitro_Lote +=1;
	$Regitro_Lote = str_pad($Regitro_Lote,5,"0", STR_PAD_LEFT);

	$Seguimento = "Q";
	$Quota = $TQuota[$c];
	
	$sql = "SELECT QUO_ID_CONTRIBUINTE FROM sce_quota WHERE QUO_ID_QUOTA = $Quota";
	
	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	while ($row = mysql_fetch_assoc($exec)){
		$NumContribuinte = $row['QUO_ID_CONTRIBUINTE'];
	}	
	
	$sql = "SELECT * FROM sce_contribuinte WHERE CTB_ID = $NumContribuinte";
	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	while ($row = mysql_fetch_assoc($exec)){
		$Pagador = str_pad(($row['CTB_NOME']),40," ");
		$CPF = soNumero($row['CTB_CPF']);
		$Endereço = str_pad(($row['CTB_ENDERECO']),40," ");
		$Bairro = str_pad(($row['CTB_BAIRRO']),15," ");
		$CEP = soNumero($row['CTB_CEP']);
		$Cidade = str_pad(($row['CTB_MUNICIPIO']),15," ");
		$UF = $row['CTB_UF'];
	}
	$Nome = substr($Pagador, 0, 40);
	$End = substr($Endereço, 0, 40);
	$Bair = substr($Bairro, 0, 15);
	$Cid = substr($Cidade, 0, 15);
	$NumCPF = str_pad($CPF,14,"0", STR_PAD_LEFT);
	$Aval = str_repeat("0",16);
	$Corresp = str_repeat(" ",40);
	$CodComp = "000";
	$Corresp1 = str_repeat(" ",28);
	
	if ($CEP == ""){$CEP = 71900000;}

	$linha = $Banco.$Lote.$Registro.$Regitro_Lote.$Seguimento.$CNAB4.$Cod_Mov."10".$NumCPF.$Nome.$End.$Bair.$CEP.$Cid.$UF.$Aval.$Corresp.$CodComp.$Corresp1."\r\n";
	fwrite($arquivoAberto, $linha);
	$regGrav +=1;	
	
	// Compor Seguimento R
	
	$Regitro_Lote +=1;
	$Regitro_Lote = str_pad($Regitro_Lote,5,"0", STR_PAD_LEFT);

	$Seguimento = "R";
	$Desc2_3_Multa = str_repeat("0",72);
	$Pagador = str_repeat(" ",110);
	$DtLimite = str_repeat("0",16);
	$CC = str_repeat("0",12);
	$DV = str_repeat(" ",2);
	$CNAB6 = str_repeat(" ",9);

	$linha = $Banco.$Lote.$Registro.$Regitro_Lote.$Seguimento.$CNAB4.$Cod_Mov.$Desc2_3_Multa.$Pagador.$DtLimite.$CNAB4.$CC.$DV."0".$CNAB6."\r\n";
	fwrite($arquivoAberto, $linha);
	$regGrav +=1;	

	// Atualiza tabela Título, arquivo de cadastro dos boletos gerado			
	$Grav = "S";
	$sql = "UPDATE sce_titulo SET TIT_REM_GRAVADA = '$Grav' WHERE TIT_ID = $TID[$c]"; 
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	//
}
	// Compor TRAILLER do Lote
	
	$Qtde_Tit = $Regitro_Lote / 3; 
	$Qtde_Tit = str_pad($Qtde_Tit,6,"0", STR_PAD_LEFT);
	
	$Regitro_Lote +=2;
	$Regitro_Lote = str_pad($Regitro_Lote,6,"0", STR_PAD_LEFT);
	$ValorTotal = str_pad($ValorTotal,17,"0", STR_PAD_LEFT);
	$VlrCobranca = str_repeat("0",69);
	$NumAviso = str_repeat(" ",125);

	$linha = $Banco.$Lote."5".$CNAB6.$Regitro_Lote.$Qtde_Tit.$ValorTotal.$VlrCobranca.$NumAviso."\r\n";
	fwrite($arquivoAberto, $linha);
	$regGrav +=1;	

	// Compor TRAILLER do Arquivo

	$Regitro_Lote +=2;
	$Regitro_Lote = str_pad($Regitro_Lote,6,"0", STR_PAD_LEFT);
	
	$ValorTotal = str_pad($ValorTotal,17,"0", STR_PAD_LEFT);
	$VlrCobranca = str_repeat("0",69);
	$CNAB7 = str_repeat(" ",205);

	$linha = $Banco."99999".$CNAB6."000001".$Regitro_Lote."000000".$CNAB7."\r\n";
	fwrite($arquivoAberto, $linha);
	$regGrav +=1;	
	
	$ValorTotal = $ValorTotal / 100;

	$comando = "INSERT INTO sce_remessa";
	$campos  = " (EVENTO_ID, COD_BANCO, REMESSA_ID, CONVENIO_COD, NOME_ARQ, REMESSA_DATA, TOTAL_REGISTROS, VALOR_TOTAL_REGISTROS)";
	$dados   = " VALUES (4, 756, ".$Remessa.", 66044, '".$nomeArquivoSICOOB."', ".$dataReferencia.", ".$regGrav.", ".$ValorTotal." )";
	$cmdSQL  = $comando.$campos.$dados;

	$exec =  mysql_query($cmdSQL, $conn) or die(mysql_error());
	if (mysql_error() == ""){
		echo("Arquivo: ".$nomeArquivoSICOOB." gerado.<br>");
	}
	else{
		echo("<tr><td>ERRO na inclusão dos titulos:".mysql_error()."</td></tr>");
		echo("<tr><td>cmdSQL=".$cmdSQL."</td></tr>");
		echo("</table>");
		exit();
	}
	exit;
	fclose($arquivoAberto);
?>