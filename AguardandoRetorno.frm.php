<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
if ($perfilDoUsuario != "Master"){echo "Usuário sem permisão para esta transação!";exit;};
?>
<div id='divAguardandoRetorno' title='Aguardando retorno' style='display:none'>
	<table width='100%'>
		<tr><td align='right'>Banco</td>
		     <td>
			 <select id='cmbRemessaBanco'>
			 <option value='001'>BB - Banco do Brasil</option>
			 <option value='070'>BRB - Banco de Brasilia</option>
			 <option value='104'>CEF - Caixa Economica Federal</option>			 
			 </select>
			 </td>
		</tr>
	</table>
</div>
<script type='text/javascript'>

	$("span.ui-dialog-title").text("Aguardando retorno");
	$('#divAguardandoRetorno').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 280, width: 500, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
			'Sair': function() {$(this).dialog('close');},
			Consultar: function() {
				acaoConsultarAguardandoRetorno();
			}
		}
	});
	$('#divAguardandoRetorno').dialog('open');
	
	function acaoConsultarAguardandoRetorno(){
		var paginaPHP = "#";
		if ($('#cmbRemessaBanco').val() == '070'){ banco = '2';}
		if ($('#cmbRemessaBanco').val() == '001'){ banco = '1';}
		if ($('#cmbRemessaBanco').val() == '104'){ banco = '3';}
		$.ajax({
			type	: "POST", datatype : "php", url : 'AguardandoRetorno.bus.php',
			data	: "parametroBanco="+banco,
			async	:  true, cache : true, timeout : 120000,
			complete:    function(resposta){
				$("#divAguarde").dialog('close');
				$("span.ui-dialog-title").text("Aguardando retorno");
				$("#divAjaxRetornoGrande").empty().append(resposta.responseText).dialog('open');
		   }
	 	});	
	}

</script>	
