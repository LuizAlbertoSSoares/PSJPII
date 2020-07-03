<?php
require_once('busConsistirPemissao.php');
$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
if ($perfilDoUsuario != "Master"){echo "Usuário sem permisão para esta transação!";exit;};

echo("<html><head><title></title>");
echo("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />");
echo("<link  href='jquery-ui.css' rel='stylesheet' type='text/css'/>");
echo("<script src='jquery.min.js'></script>");
echo("<script src='jquery-ui.min.js'></script>");
echo("</head>");
echo "<style type='text/css'>";
echo ".cor1 { background-color: #efefef;}";
echo ".cor2 {	background-color: #ccc;}";
echo ".cor3 {	background-color: #9bbcff;} /* azul     */";
echo ".cor4 {	background-color: #ff9191;} /* vermelho */";
echo ".fonte1 {color: white;}";
echo "#divrETORNAR {";
echo "    border: 1px solid #999999;";
echo "    border-radius: 4px 4px 4px 4px;";
echo "    font-size: 0.95em;";
echo "    width : 400px;";
echo "	  height: 200px;";
echo "	  background-color:#FFFFFF;";
echo "}";
echo "</style>";
echo("<body  style='font-size:62.5%;' >");
echo("<form id='retornarParaIndex' action='index.html' method='POST'>");
echo("<div id='divRetornar' title='Retorno apos processamento' style='display:block'>");

$destino = 'uploads//';
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
	$arquivoRetorno = "uploads\\".$arquivoOrigem;
}

if(!$_FILES){
	echo 'Nenhum arquivo enviado!<br>';
}else{
	$file_name = $_FILES['userfile']['name'];
	$file_type = $_FILES['userfile']['type'];
	$file_size = $_FILES['userfile']['size'];
	$file_tmp_name = $_FILES['file']['tmp_name'];
	$error = $_FILES['userfile']['error'];
}

switch ($error){
	case 0:
		break;
	case 1:
		echo 'O tamanho do arquivo é maior que o definido nas configurações do PHP!<br>';
		break;
	case 2:
		echo 'O tamanho do arquivo é maior do que o permitido!<br>';
		break;
	case 3:
		echo 'O upload não foi concluído!<br>';
		break;
	case 4:
		echo 'O upload não foi feito!<br>';
		break;
}

if($error == 0){
	if(!move_uploaded_file($_FILES['userfile']['tmp_name'], $destino . $_FILES['userfile']['name'])){
		echo 'Não foi possível salvar o arquivo!<br>';
	}else{
			echo "<div class='cor4' style='text-align: center;'><a class='fonte1'>UPLOAD CONCLUIDO</a></div>";
			echo 'Processo concluído com sucesso!<br>';
			echo "Nome do arquivo:".$file_name."<br>";
			echo "Tipo de arquivo:".$file_type."<br>";
			echo "Tamanho em byte:".$file_size."<br>";
	}
}

echo("<input type='submit' id='btnOK' value='Clique AQUI para retornar...' />");
echo("</div>");
echo("</form>");
echo("</body>");
echo("</html>");
exit;
?>

