<?php
set_time_limit(0); 
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");

echo "<html><head><title></title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";

$arquivoRetorno = "downloads//".$_GET['arquivo'];
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$arquivoRetorno = "downloads\\".$_GET['arquivo'];}
$arquivo = fopen ($arquivoRetorno, "r") or die("Nao consigo abrir este arquivo");
$regLido = 0;
while (!feof($arquivo)){
		$linha = fgets($arquivo, 4096);
		$regLido += 1;
		if ($regLido == 1){		
			echo "<pre>";
			echo "Registro A - Header";
			echo "<hr>";
			echo "A01 - Codigo do registro                    = ".substr($linha,1-1,1)."<br>";
			echo "A02 - Codigo da remessa                     = ".substr($linha,2-1,1)."<br>";
			echo "A03 - Codigo do Convênio                    = ".substr($linha,3-1,20)."<br>";
			echo "A04 - Nome da empresa                       = ".substr($linha,23-1,20)."<br>";
			echo "A05 - Codigo do Banco                       = ".substr($linha,43-1,3)."<br>";
			echo "A06 - Nome do Banco                         = ".substr($linha,46-1,20)."<br>";
			echo "A07 - Data de Geração                       = ".substr($linha,66-1,8)."<br>";
			echo "A08 - Número sequencial do Arquivo(NSA)     = ".substr($linha,74-1,6)."<br>";
			echo "A09 - Versão do leiaute                     = ".substr($linha,80-1,2)."<br>";
			echo "A10 - identificação do Serviço              = ".substr($linha,82-1,17)."<br>";
			echo "A11 - reservado para o futuro               = ".substr($linha,99-1,52)."<br>";
			echo "</pre>";
		}
#       DETALHE
		if ($regLido == 2){		
			echo "<pre>";
			echo "Registro E - Débito em conta Corrente";
			echo "<hr>";
			echo "E01 - Codigo do registro                    = ".substr($linha,1-1,1)."<br>";
			echo "E02 - Identificação do Cliente na Empresa   = ".substr($linha,2-1,25)."<br>";
			echo "E03 - Agência para Débito                   = ".substr($linha,27-1,4)."<br>";
			echo "E04 - Identificação do Cliente no Banco     = ".substr($linha,31-1,14)."<br>";
			echo "E05 - Data do Vencimento                    = ".substr($linha,45-1,8)."<br>";
			echo "E06 - Valor do Débito                       = ".substr($linha,53-1,15)."<br>";
			echo "E07 - Código da moeda                       = ".substr($linha,68-1,2)."<br>";
			echo "E08 - Uso da Empresa                        = ".substr($linha,70-1,49)."<br>";
			echo "E09 - Reservado para o futuro               = ".substr($linha,130-1,20)."<br>";
			echo "E10 - Código do Movimento                   = ".substr($linha,150-1,1)."<br>";
			echo "</pre>";
		}
#       TRAILLER	
		if (substr($linha,1-1,1) == "Z"){
			echo "<pre>";
			echo "Registro Z - Trailler";
			echo "<hr>";
			echo "Z01 - Codigo do registro                    = ".substr($linha,1-1,1)."<br>";
			echo "Z02 - Total de registros do arquivo         = ".substr($linha,2-1,6)."<br>";
			echo "Z03 - Valor total dos registros do arqquivo = ".substr($linha,8-1,17)."<br>";
			echo "Z04 - Reservado para o futuro               = ".substr($linha,25-1,126)."<br>";
		}
}
			



?>