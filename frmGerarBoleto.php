<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<div id='divGerarBoleto' title='Gerar Boleto' style='display:none'>
<table class='tbNormal'>
	<tr><td align='right'>Número da Quota:</td><td><input type='text' id='frmBoletoQuotaInicial' value='' size='4'>
</table>
</div>

<script type='text/javascript'>

	$("span.ui-dialog-title").text("Gerar Boleto");
	$('#divGerarBoleto').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 230, width: 320, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
			'Sair': function() {$(this).dialog('close');},
			'Gerar': function() {
				$("span.ui-dialog-title").text("Processando");
				$("#divAguarde").dialog('open');
				$.ajax({
				type	: "POST", datatype : "php", url : "BoletoBB.php",
				data	: "QuotaInicio="+$('#frmBoletoQuotaInicial').val()
						+"&QuotaFim="+$('#frmBoletoQuotaFinal').val(),
					async	:  true, cache : true, timeout : 120000,
					complete:    function(resposta){
							$("#divAguarde").dialog('close');
							$('#divPrincipal').removeClass().css('background', '#ffffff'); 
							$("#divPrincipal").empty().append(resposta.responseText).css({display:"block"});
						}
				});
				$(this).dialog('close');
			}
		}
	});
	$('#divGerarBoleto').dialog('open');

</script>	