<?php
require_once('busConsistirPemissao.php');

$permissaoPerfil = $_SESSION["perfilDoUsuario"];
if ($permissaoPerfil != "Master"){ echo "Usuário sem premissão";exit;};

echo("<!doctype html>");
echo("<html lang='en'><head><title></title>");
echo("<meta http-equiv='Content-Type' content='text/html; charset=utf8' />");
echo("<style>");
echo(".tbCaixa	   	{FONT-SIZE: 12px; COLOR: #7d7d7d; LINE-HEIGHT: 13px; FONT-FAMILY: Tahoma; width:100%;}");
echo(".tdCaixa		{BACKGROUND-COLOR:#F8F8F8;}");
echo("</style>");
echo("</head><body><form>");
echo("<table class='tbCaixa'>");
echo("<tr><td>Selecione o arquivo a ser processado...</td></tr>");
echo("<tr><td class='tdCaixa'>");
echo("<table class='tbCaixa'>");
echo("<tr><td></td.</tr>");

//echo "<input type='radio' name='TipoDeArquivoRetorno' value='DebitoBB' checked >Débitos do Banco do Brasil<br>";
//echo "<input type='radio' name='TipoDeArquivoRetorno' value='DebitoCEF'>Débitos da Caixa Economica Federal<br>";
//echo "<input type='radio' name='TipoDeArquivoRetorno' value='DebitoBRB'>Débitos do Banco de Brasília<br>";
//echo "<input type='radio' name='TipoDeArquivoRetorno' value='BoletoBB'>Boletos do Banco do Brasil<br>";

$diretorio = dir('uploads');
echo("<tr><td><select id='cmbArquivoRetorno'>");
while (false !== ($nome = $diretorio->read())) {
	if ($nome != '.'){
		if ($nome != '..') {
			if (substr($nome, -5) != '.html') {
				if (substr($nome, 0,3) != 'bkp') {			
					if (substr($nome, -10) != 'PROCESSADO') {
						echo("<option value='".$nome."'>".$nome."</option>");
					}
				}
			}
		}
	}
}
echo("</select><input type='button' id='btnProcessarRetorno' value='Processar'>  <input type='button' id='btnExcluirArquivoRetorno' value='Excluir Arquivo'> </td></tr>");
$diretorio->close();
echo("<tr><td></td></tr>");
echo("</table>");
echo("</form></body></html>");

?>
