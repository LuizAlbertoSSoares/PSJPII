<?php
	session_start("Senha");
	session_start("usuario");
	session_start("nomeDoUsuario");
	session_start("perfilDoUsuario");
	
    $_SESSION["Senha"] = 'EventosOK';
	$_SESSION["Senha"] = "ERRO";
	$usuario = $_POST["frmUsuario"];
	$senha   = $_POST["frmSenha"];
	
	$codificada = sha1($_POST['frmSenha']);
	require_once('Conn.php');	
	$sql = "SELECT * FROM sce_usuario WHERE usuario = '".$_POST["frmUsuario"]."'";
	$exe = mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	$usuarioNabase = "";
	$nomeNaBase = "";
	$senhaNabase = "";
	$perfilNaBase = "";
	while ($row = mysql_fetch_assoc($exe)){
		$usuarioNaBase = $row['usuario'];
		$senhaNaBase = $row['senha'];
		$nomeNaBase = $row['nome'];
		$perfilNaBase = $row['perfil'];
	}
	
	if (strtoupper($usuario) == strtoupper($usuarioNaBase)){
		if (strtoupper($codificada) == strtoupper($senhaNaBase)) {
			$_SESSION["Senha"] = 'EventosOK';
			$_SESSION["usuario"] = $_POST["frmUsuario"];
			$_SESSION["nomeDoUsuario"] = $nomeNaBase;
			$_SESSION["perfilDoUsuario"] = $perfilNaBase;
		}
	}

?>