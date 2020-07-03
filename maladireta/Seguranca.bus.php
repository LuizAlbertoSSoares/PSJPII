<?php
# CONSITIR SENHA DE ACESSO AO SISTEMA
if ($_POST['Operacao'] == "VerificarSenha"){
	session_start("Senha");
	session_start("usuario");
	session_start("nomeDoUsuario");
	session_start("perfilDoUsuario");
	
    $_SESSION["Senha"] = "ERRO";
	$usuario = $_POST["frmUsuario"];
	$senha   = $_POST["frmSenha"];
	
	$codificada = sha1($_POST['frmSenha']);
	require_once('../Conn.php');	
	$sql = "SELECT * FROM sce_usuario WHERE usuario = '".$_POST["frmUsuario"]."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$usuarioNabase  = "";
	$senhaNabase    = "";
	$cpf_cnpjNaBase = "";
	$perfilDoUsuario = "";
	while ($row = mysql_fetch_assoc($exe)){
		$usuarioNaBase  	= $row['usuario'];
		$senhaNaBase    	= $row['senha'];
		$nomeNaBase     	= $row['nome'];
		$cpf_cnpjNaBase 	= $row['cpf_cnpj'];
		$perfilDoUsuario	= $row['perfil'];
	}
	if ($usuario == $usuarioNaBase){
		if ($codificada == $senhaNaBase) {
			$_SESSION["Senha"]           = 'PermissaoAutorizada';
			$_SESSION["usuario"]         = $_POST["frmUsuario"];
			$_SESSION["nomeDoUsuario"]   = $nomeNaBase;
			$_SESSION["cpfcnpj"]       	 = $cpf_cnpjNaBase;
			$_SESSION["perfilDoUsuario"] = $perfilDoUsuario;
		}
	}
	exit;
}	

# CONSISTIR SE O USUARIO ESTA LOGADO NO SISTEMA
if ($_POST['Operacao'] == "VerificarPermissao"){
	$dado = "ERRO";
	session_start("Senha");
	if ($_SESSION["Senha"] == "PermissaoAutorizada"){$dado = "PermissaoAutorizada";}
	echo "<table>";
	echo "<tr>";
	echo "<td>";
	echo "<input type='hidden' id='frmPermissaoDeAcesso' value='".$dado."'>";
	echo "</td>";
	echo "<input type='hidden' id='frmNomeDoUsuario' value='".$_SESSION["nomeDoUsuario"]."'>";
	echo "<input type='hidden' id='frmCpfCnpj' value='".$_SESSION["cpfcnpj"]."'>";
	echo "</tr>";
	echo "</table>";
	exit;
}
# CONSISTIR PERMISSÃO 
session_start('Senha');
$dado = "ERRO";
if ($_SESSION["Senha"] != "PermissaoAutorizada"){ echo("Usuario não esta logado no sistema!");exit(); }

?>