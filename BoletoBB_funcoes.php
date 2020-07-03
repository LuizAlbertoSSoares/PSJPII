<?php
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
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>              					                               |
// | Desenvolvimento Boleto Banco do Brasil: Daniel William Schultz / Leandro Maniezo / Rogério Dias Pereira / Romeu Medeiros|
// +-------------------------------------------------------------------------------------------------------------------------+

// FUNÇÕES
// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco

function formata_numero($numero,$loop,$insert,$tipo = "geral") {
	if ($tipo == "geral") {
		$numero = str_replace(",","",$numero);
		while(strlen($numero)<$loop){
			$numero = $insert . $numero;
		}
	}
	if ($tipo == "valor") {
		/*
		retira as virgulas
		formata o numero
		preenche com zeros
		*/
		$numero = str_replace(",","",$numero);
		while(strlen($numero)<$loop){
			$numero = $insert . $numero;
		}
	}
	if ($tipo == "convenio") {
		while(strlen($numero)<$loop){
			$numero = $numero . $insert;
		}
	}
	return $numero;
}

function fbarcode($valor){

$fino = 1 ;
$largo = 3 ;
$altura = 50 ;

  $barcodes[0] = "00110" ;
  $barcodes[1] = "10001" ;
  $barcodes[2] = "01001" ;
  $barcodes[3] = "11000" ;
  $barcodes[4] = "00101" ;
  $barcodes[5] = "10100" ;
  $barcodes[6] = "01100" ;
  $barcodes[7] = "00011" ;
  $barcodes[8] = "10010" ;
  $barcodes[9] = "01010" ;
  for($f1=9;$f1>=0;$f1--){
    for($f2=9;$f2>=0;$f2--){
      $f = ($f1 * 10) + $f2 ;
      $texto = "" ;
      for($i=1;$i<6;$i++){
        $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
      }
      $barcodes[$f] = $texto;
    }
  }

//Desenho da barra

//Guarda inicial

?><img src=imagens/p.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
src=imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
src=imagens/p.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
src=imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
<?php
$texto = $valor ;
if((strlen($texto) % 2) <> 0){
	$texto = "0" . $texto;
}

// Draw dos dados

while (strlen($texto) > 0) {
  $i = round(esquerda($texto,2));
  $texto = direita($texto,strlen($texto)-2);
  $f = $barcodes[$i];
  for($i=1;$i<11;$i+=2){
    if (substr($f,($i-1),1) == "0") {
      $f1 = $fino ;
    }else{
      $f1 = $largo ;
    }
?>
    src=imagens/p.png width=<?php echo $f1?> height=<?php echo $altura?> border=0><img
<?php
    if (substr($f,$i,1) == "0") {
      $f2 = $fino ;
    }else{
      $f2 = $largo ;
    }
?>
    src=imagens/b.png width=<?php echo $f2?> height=<?php echo $altura?> border=0><img
<?php
  }
}

// Draw guarda final
?>
src=imagens/p.png width=<?php echo $largo?> height=<?php echo $altura?> border=0><img
src=imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img
src=imagens/p.png width=<?php echo 1?> height=<?php echo $altura?> border=0>
  <?php
} //Fim da função

function esquerda($entra,$comp){
	return substr($entra,0,$comp);
}

function direita($entra,$comp){
	return substr($entra,strlen($entra)-$comp,$comp);
}

function fator_vencimento($data) {
	$data = split("/",$data);
	$ano = $data[2];
	$mes = $data[1];
	$dia = $data[0];
    return((abs((_dateToDays("2000","07","03")) - (_dateToDays($ano, $mes, $dia))))+1000);
}

function _dateToDays($year,$month,$day) {
    $century = substr($year, 0, 2);
    $year = substr($year, 2, 2);
    if ($month > 2) {
        $month -= 3;
    } else {
        $month += 9;
        if ($year) {
            $year--;
        } else {
            $year = 99;
            $century --;
        }
    }

    return ( floor((  146097 * $century)    /  4 ) +
            floor(( 1461 * $year)        /  4 ) +
            floor(( 153 * $month +  2) /  5 ) +
                $day +  1721119);
}

/*
FUNÇÃO DO MÓDULO 10 RETIRADA DO PHPBOLETO

ESTA FUNÇÃO PEGA O DÍGITO VERIFICADOR DO PRIMEIRO, SEGUNDO
E TERCEIRO CAMPOS DA LINHA DIGITÁVEL
*/

function modulo_10($num) {
	$numtotal10 = 0;
	$fator = 2;

	for ($i = strlen($num); $i > 0; $i--) {
		$numeros[$i] = substr($num,$i-1,1);
		$parcial10[$i] = $numeros[$i] * $fator;
		$numtotal10 .= $parcial10[$i];
		if ($fator == 2) {
			$fator = 1;
		}
		else {
			$fator = 2;
		}
	}

	$soma = 0;
	for ($i = strlen($numtotal10); $i > 0; $i--) {
		$numeros[$i] = substr($numtotal10,$i-1,1);
		$soma += $numeros[$i];
	}
	$resto = $soma % 10;
	$digito = 10 - $resto;
	if ($resto == 0) {
		$digito = 0;
	}

	return $digito;
}

/*
FUNÇÃO DO MÓDULO 11 RETIRADA DO PHPBOLETO

MODIFIQUEI ALGUMAS COISAS...

ESTA FUNÇÃO PEGA O DÍGITO VERIFICADOR:

NOSSONUMERO
AGENCIA
CONTA
CAMPO 4 DA LINHA DIGITÁVEL
*/

function modulo_11($num, $base=9, $r=0) {
	$soma = 0;
	$fator = 2;
	for ($i = strlen($num); $i > 0; $i--) {
		$numeros[$i] = substr($num,$i-1,1);
		$parcial[$i] = $numeros[$i] * $fator;
		$soma += $parcial[$i];
		if ($fator == $base) {
			$fator = 1;
		}
		$fator++;
	}
	if ($r == 0) {
		$soma *= 10;
		$digito = $soma % 11;

		//corrigido
		if ($digito == 10) {
			$digito = "X";
		}

		/*
		alterado por mim, Daniel Schultz

		Vamos explicar:

		O módulo 11 só gera os digitos verificadores do nossonumero,
		agencia, conta e digito verificador com codigo de barras (aquele que fica sozinho e triste na linha digitável)
		só que é foi um rolo...pq ele nao podia resultar em 0, e o pessoal do phpboleto se esqueceu disso...

		No BB, os dígitos verificadores podem ser X ou 0 (zero) para agencia, conta e nosso numero,
		mas nunca pode ser X ou 0 (zero) para a linha digitável, justamente por ser totalmente numérica.

		Quando passamos os dados para a função, fica assim:

		Agencia = sempre 4 digitos
		Conta = até 8 dígitos
		Nosso número = de 1 a 17 digitos

		A unica variável que passa 17 digitos é a da linha digitada, justamente por ter 43 caracteres

		Entao vamos definir ai embaixo o seguinte...

		se (strlen($num) == 43) { não deixar dar digito X ou 0 }
		*/

		if (strlen($num) == "43") {
			//então estamos checando a linha digitável
			if ($digito == "0" or $digito == "X" or $digito > 9) {
					$digito = 1;
			}
		}
		return $digito;
	}
	elseif ($r == 1){
		$resto = $soma % 11;
		return $resto;
	}
}


//Montagem da linha digitável - Função tirada do PHPBoleto

function monta_linha_digitavel($linha) {

	/* 1. Campo
	A=	Código do Sicoob na câmara de compensação - "756"				
	B=	Código da moeda - "9"				
	C=	Código da carteira - verificar na planilha "Capa" deste arquivo				
	D=	Código da agência/cooperativa - verificar na planilha "Capa" deste arquivo				
	E=	Dígito verificador do Campo 1 - vide demonstrativo de cálculo a seguir				
	
	AAABC.DDDDE
	          1         2         3         4 
	01234567890123456789012345678901234567890123456
	75691814200000020001433201000476000000101001
	*/
	$pA = substr($linha, 0, 3);
    $pB = substr($linha, 3, 1);
	$pC = substr($linha, 19, 1);
	$pD = substr($linha, 20, 4); 
	$pE = modulo_10("$pA$pB$pC$pD");
	$p1 = "$pA$pB$pC.$pD$pE";
	$campo1 = "$p1";

    /* 2. Campo
	F=	Código da modalidade - verificar na planilha "Capa" deste arquivo				
	G=	Código do beneficiário/cliente - verificar na planilha "Capa" deste arquivo				
	H=	Nosso número do boleto 1º registro				
	I=	Dígito verificador do Campo 2 - vide demonstrativo de cálculo a seguir				

	FFGGG.GGGGHI
	          1         2         3         4 
	01234567890123456789012345678901234567890123456
	75691814200000020001433201000476000000101001
	*/
	$pF	= "01";
    $pG = substr($linha, 26, 7);
	$pH1 = substr($linha, 33, 1);
	$pI = modulo_10("$pF$pG$pH1");
    $p1 = "$pF$pG$pH1$pI";
    $p2 = substr($p1, 0, 5);
    $p3 = substr($p1, 5);
    $campo2 = "$p2.$p3";

	/* 3. Campo
	H=	Nosso número do boleto - do 2º registro 7 posições
	J=	Número da parcela a que o boleto se refere - "001" se parcela única				
	K=	Dígito verificador do Campo 3 - vide demonstrativo de cálculo a seguir			
	
	HHHHH.HHJJJK
			  1         2         3         4 
	01234567890123456789012345678901234567890123456
	75691814200000020001433201000476000000101001
	*/
	$pH2 = substr($linha, 34, 7);
	$pJ = substr($linha, 41, 3);
	$pK = modulo_10("$pH2$pJ");
    $p1 = "$pH2$pJ$pK";
    $p2 = substr($p1, 0, 5);
    $p3 = substr($p1, 5);
    $campo3 = "$p2.$p3";

    /* 4. Campo - digito verificador do codigo de barras
	L=	Dígito verificador do Código de Barras - vide demonstrativo de cálculo a seguir				
	
	L
			 1         2         3         4 
	01234567890123456789012345678901234567890123456
	75691814200000020001433201000476000000101001
	*/
	$campo4 = substr($linha, 4, 1);

	/* 5. Campo 
	M=	Fator de vencimento - vide demonstrativo de cálculo a seguir				
	N=	Valor do boleto - Em casos de cobrança com valor em aberto (o valor a ser pago é preenchido pelo próprio pagador) ou cobrança em moeda variável, deve ser preenchido com zeros				
	
	MMMMNNNNNNNNNN
			  1         2         3         4 
	01234567890123456789012345678901234567890123456
	75691814200000020001433201000476000000101001
	*/
	$pM = substr($linha, 5, 4);
	$pN = substr($linha, 9, 10);
	$campo5 = "$pM$pN";

    return "$campo1 $campo2 $campo3 $campo4 $campo5";
}

function geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}

function soNumero($str) {
	return preg_replace("/[^0-9]/", "", $str);
}

function DvNN($DvNN) {
	$Dig00 = substr($DvNN,0,1) * 3;
	$Dig01 = substr($DvNN,1,1) * 1;
	$Dig02 = substr($DvNN,2,1) * 9;
	$Dig03 = substr($DvNN,3,1) * 7;
	$Dig04 = substr($DvNN,4,1) * 3;
	$Dig05 = substr($DvNN,5,1) * 1;
	$Dig06 = substr($DvNN,6,1) * 9;
	$Dig07 = substr($DvNN,7,1) * 7;
	$Dig08 = substr($DvNN,8,1) * 3;
	$Dig09 = substr($DvNN,9,1) * 1;
	$Dig10 = substr($DvNN,10,1) * 9;
	$Dig11 = substr($DvNN,11,1) * 7;
	$Dig12 = substr($DvNN,12,1) * 3;
	$Dig13 = substr($DvNN,13,1) * 1;
	$Dig14 = substr($DvNN,14,1) * 9;
	$Dig15 = substr($DvNN,15,1) * 7;
	$Dig16 = substr($DvNN,16,1) * 3;
	$Dig17 = substr($DvNN,17,1) * 1;
	$Dig18 = substr($DvNN,18,1) * 9;
	$Dig19 = substr($DvNN,19,1) * 7;
	$Dig20 = substr($DvNN,20,1) * 3;
	
	$TotalDig = $Dig00 + $Dig01 + $Dig02 + $Dig03 + $Dig04 + $Dig05 + $Dig06 + $Dig07 + $Dig08 + $Dig09 + $Dig10 + $Dig11 + $Dig12 + $Dig13 + $Dig14 + $Dig15 + $Dig16 + $Dig17 + $Dig18 + $Dig19 + $Dig20;
	$Resto = $TotalDig % 11;
		
	if ($Resto < 2 ){$Dv = 0;}
	else {$Dv = 11 - $Resto;}
	return $Dv;
}

function retiraAcentos($string){
	$acentos      =  'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$sem_acentos  =  'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = strtr($string, utf8_decode($acentos), $sem_acentos);
	//$string = str_replace(" ","-",$string);
	return utf8_decode($string);
 }
 
 function dataVenc($ParcelaVenc){ 
	switch ($ParcelaVenc){		
		case 1 :
			$Data2 = "25/03/2020";
		break;
		case 2 :
			$Data2 = "21/04/2020";
		break;
		case 3 :
			$Data2 = "27/05/2020";
		break;
		case 4 :
			$Data2 = "23/06/2020";
		break;
		case 5 :
			$Data2 = "22/07/2020";
		break;
		case 6 :
			$Data2 = "25/08/2020";
		break; 
		case 7 :
			$Data2 = "23/09/2020";
		break;
		case 8 :
			$Data2 = "20/10/2020";
		break;
		case 9 :
			$Data2 = "25/11/2020";
		break;
		case 10 :
			$Data2 = "21/12/2020";
		break;		
		default: $DataFinal = date("d/m/Y", time() + 86400);
	}
	if ($ParcelaVenc <> 0){
		$data = split("/",$Data2);
		$ano = $data[2];
		$mes = $data[1];
		$dia = $data[0];
		$Dt2 = ((abs((_dateToDays("2000","07","03")) - (_dateToDays($ano, $mes, $dia))))+1000);
		
		$Data1 = date("d/m/Y");
		$data = split("/",$Data1);
		$ano = $data[2];
		$mes = $data[1];
		$dia = $data[0];
		$Dt1 = ((abs((_dateToDays("2000","07","03")) - (_dateToDays($ano, $mes, $dia))))+1000);		

		$DataFinal = $Dt2 - $Dt1;
		
		if ($DataFinal < 0){$DataFinal = date("d/m/Y", time() + 86400);}
		else {$DataFinal = $Data2;}
	}	
	return $DataFinal;
}

function validaCPF($cpf) {
 
    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf{$c} * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf{$c} != $d) {
            return false;
        }
    }
    return true;

}
?>
