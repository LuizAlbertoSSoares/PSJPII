<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
if ($perfilDoUsuario != "Master"){echo "Usuário sem permisão para esta transação!";exit;};
?>
<div id='divGerarArquivoRemessa' title='Gerar Arquivo de Remessa' style='display:none'>
	<table width='100%'>
			<tr><td align='right'>Banco</td>
		     <td>
			 <select id='cmbRemessaBanco'>
			 <option value='756'>SICOOB</option>
			 </td>
		</tr>
	</table>
</div>
<script type='text/javascript'>

	$("span.ui-dialog-title").text("Gerar Arquivo de Remessa");
	$('#divGerarArquivoRemessa').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 280, width: 500, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
			'Sair': function() {$(this).dialog('close');},
			'Gerar': function() {
				$("span.ui-dialog-title").text("Processando");
				$("#divAguarde").dialog('open');
				$.ajax({
					type	: "POST", datatype : "php", url : "busGerarArquivoRemessaSICOOB.php",
					async	:  true, cache : true, timeout : 120000,
					complete:    function(resposta){
						$("#divAguarde").dialog('close');
						$("span.ui-dialog-title").text("Resultado do processamento");
						$("#divPrincipal").empty().append(resposta.responseText).css({display:"block"});
						$("#divPrincipal").dialog('open');
					}
				});
				$(this).dialog('close');
			}
		}
	});
	$('#divGerarArquivoRemessa').dialog('open');
	
</script>