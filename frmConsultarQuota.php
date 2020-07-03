<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<div id='divConsultarQuota' title='Consultar Quota' style='display:none'>
	<table width='100%' class='tbNormal'>
		<tr><td align='right'>Quota número  </td><td><input type='text' id='frmQuotaConsultar'  size='06' value=''></td></tr>
		<tr><td align='right'>Período </td><td><input type='text' id='frmEventoConsultarData1' size = '08' value=''> a <input type='text' id='frmEventoConsultarData2' size = '08' value=''></td></tr>
		<tr><td></td><td>
		<select size="1" id="cmbConsultarQuota">
		<option value="consultarQuotaPorNumero">Quota por número</option>
		<option value="consultarQuotasAssociadasPorPeriodo">Quotas associadas no período</option>
		<option value="consultarQuotasNaoAssociadas">Quotas não associadas</option>
		</select>
		</td></tr>
	</table>
</div>
<script type='text/javascript'>
	$('#frmEventoConsultarData1').datepicker();
	$('#frmEventoConsultarData2').datepicker();

	$('span.ui-dialog-title').text('Consultar quota');
	$('#divConsultarQuota').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 230, width: 450, modal: true,
	overlay:{backgroundColor:'#000', opacity:0.5},
	buttons:{
		'Sair'		: function() {$(this).dialog('close');},
		'Consultar'	: function() {$(this).dialog('close');acaoConsultarQuotaContribuinte();}
	}
	});
	$('#divConsultarQuota').dialog('open');
	
	function acaoConsultarQuotaContribuinte(){
		$.ajax({
			type		: "POST", datatype : "php", url	: "busQuotaBuscarPor.php",
			data		: "TextoConsulta=" + $("#frmQuotaConsultar").val()
						+ "&DataInicio="+$("#frmEventoConsultarData1").val()
						+ "&DataFim="+$("#frmEventoConsultarData2").val()
						+ "&Consulta=" + $("#cmbConsultarQuota").val(),		
			async		:  true, cache : true, timeout : 120000,
			complete	:  function(resposta){
				$("span.ui-dialog-title").text("Extrato da quota");
				$("#divAjaxRetornoGrande").empty().append(resposta.responseText).dialog({autoOpen:true});
			}
		});
	};


</script>	