<?php
//$dado = 'EventosOK';
$dado = "ERRO";
session_start("Senha");
if ($_SESSION["Senha"] == "EventosOK"){$dado = "EventosOK";}
echo "<table>";
echo "<tr>";
echo "<td>";
echo "<input type='hidden' id='frmPermissaoDeAcesso' value='".$dado."'>";
echo "</td>";
echo "<input type='hidden' id='frmNomeDoUsuario' value='".$_SESSION["nomeDoUsuario"]."'>";
echo "<input type='hidden' id='frmPerfilDoUsuario' value='".$_SESSION["perfilDoUsuario"]."'>";
echo "</tr>";
echo "</table>";
?>