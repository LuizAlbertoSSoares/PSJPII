<?php
//	require_once('Texto.frm.php');
?>
<script language="javascript" type="text/javascript">
	//**** EDITAR TEXTO
	$('#opcaoEditarTexto').click(function() {
		$.ajax({type	: "POST", datatype: "php", url: "Texto.frm.php", 
			data 		: "",
			async		:  true, cache:true, timeout:12000,
			complete	:  function(resposta){
				$("#divCentro").empty().append(resposta.responseText);
			}	
		});							
	});
	$('#opcaoAtalhoEditarTexto').click(function() { $('#opcaoEditarTexto').trigger("click"); });

	//**** ENVIAR CORREIO
	$('#opcaoEnviarCorreio').click(function() {
		$.ajax({type	: "POST", datatype: "php", url: "EnviarCorreio.frm.php", 
			data 		: "",
			async		:  true, cache:true, timeout:12000,
			complete	:  function(resposta){
				$("#divCentro").empty().append(resposta.responseText);
			}	
		});							
	});
	$('#opcaoAtalhoCorreio').click(function() { $('#opcaoEnviarCorreio').trigger("click"); });	
	
	
</script>