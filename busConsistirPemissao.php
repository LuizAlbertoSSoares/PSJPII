<?php
session_start('Senha');
//$dado = 'EventosOK';
$dado = "ERRO";
if ($_SESSION["Senha"] != "EventosOK"){
	echo("Usuario sem permissão!");
	exit();
}
?>