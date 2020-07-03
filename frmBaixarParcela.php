<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<div id='divBaixarParcela' title='Baixar Parcela Paga' style='display:none'>
	<table class='tbNormal'><tr><td>Pesquisar Quota por número: <input type='text' class='inputPesquisa' id='quotaPesquisarQuota' value=''></td></tr></table><hr>
	<table class='tbNormal'>
		<tr>
		<td align=right width='10%'>Quota</td><td width='50%'><input type='text' class='input' id='frmQuota' disabled="disabled" maxlength='20' size='15'></td>
		<td align=right width='10%'>Valor pago</td><td width='30%'><input type='text' class='input' id='frmValorPago' maxlength='20' size='15'></td>
		</tr>
		<tr>
		<td align=right width='10%'>Documento</td><td width='50%'><input type='text' disabled="disabled" class='input' id='frmDocumento' maxlength='20' size='15'></td>
		<td align=right width='10%'>Desconto</td><td width='30%'><input type='text' class='input' id='frmValorDesconto' maxlength='20' size='15' value='0'></td>
		</tr>
		<tr>
		<td align=right width='10%'>Valor</td><td width='50%'><input type='text' class='input' disabled="disabled" id='frmValor' maxlength='20' size='15'></td>
		<td align=right width='10%'>Juros</td><td width='30%'><input type='text' class='input' id='frmValorJuros' maxlength='20' size='15' value='0'></td>
		</tr>
		<tr>
		<td align=right width='10%'>Data</td><td width='50%'><input type='text' class='input' id='frmDataBaixar' maxlength='10' size='10'></td>		
		<td align=right width='10%'></td><td width='30%'></td>
		</tr>
		<tr>
		<td align=right width='10%'></td><td width='50%'></td>
		<td align=right width='10%'>Observação</td><td width='50%'><input type='text' class='input' id='frmObservacao' maxlength='20' size='25'></td>
		</tr>	
	</table>
</div>
<script type='text/javascript'>
	var wss_valorPago = '0,00';
	$('#frmDataBaixar').datepicker();
	$('#divBaixarParcela').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 450, width: 700, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
			'Sair': function() {$(this).dialog('close');},
			'Excluir Baixa': function() {
				decisao = confirm('Confirma exclusão de baixa da parcela?');     
				if (wss_valorPago == '0,00'){alert("Esta parcela esta pendente!");return;}				
				if(decisao==true){  
					if ($('#frmQuota').val() < 1){alert("Quota deve ser informado!");return;}
					$.ajax({type	: "POST", datatype : "php", url : "busParcelaPersistir.php",
					data	: " frmOperacao=EXCLUIRBAIXADEPARCELA"
							+ "&frmDocumento=" + $('#frmDocumento').val(),
						async	:  true, cache : true, timeout : 120000,
						complete:    function(resposta){
								$("#divAguarde").dialog('close');
								$("span.ui-dialog-title").text("Excluindo baixa de parcela...");
								$("#divAjaxRetornoPequeno").empty().append(resposta.responseText).css({display:"block"});
								$("#divAjaxRetornoPequeno").dialog('open');
								$('input:text').each(function(){$(this).val('');});
								$("span.ui-dialog-title").text("Baixar Parcela");
							}
					});
				}
			},
			'Baixar': function() {
				if (wss_valorPago != '0,00'){alert("Esta parcela já está paga!");return;};
				decisao = confirm('Confirma baixa da parcela?');   
				if(decisao==true){  
//					tinyMCE.triggerSave();
					if ($('#frmQuota').val() < 1){alert("Quota deve ser informado!");return;}
					if ($('#frmValorPago').val() < 1){alert("Valor pago deve ser informado!");return;}
					if ($('#frmValorDesconto').val() == ''){alert("Desconto deve ser informado!");return;}
					if ($('#frmValorJuros').val() == ''){alert("Juros deve ser informado!");return;}
					if ($('#frmObservacao').val() == ''){alert("Observação deve ser informado!");return;}
					$.ajax({type	: "POST", datatype : "php", url : "busParcelaPersistir.php",
					data	: " frmOperacao=BAIXAR"
							+ "&frmValorDesconto=" + $('#frmValorDesconto').val()
							+ "&frmValorJuros=" + $('#frmValorJuros').val()
							+ "&frmValorPago=" + $('#frmValorPago').val()
							+ "&frmObservacao=" + $('#frmObservacao').val()
							+ "&frmDataBaixar=" + $('#frmDataBaixar').val()
							+ "&frmDocumento=" + $('#frmDocumento').val(),
						async	:  true, cache : true, timeout : 120000,
						complete:    function(resposta){
								$("#divAguarde").dialog('close');
								$("span.ui-dialog-title").text("Baixando parcela paga...");
								$("#divAjaxRetornoPequeno").empty().append(resposta.responseText).css({display:"block"});
								$("#divAjaxRetornoPequeno").dialog('open');
								$('input:text').each(function(){$(this).val('');});
								$("span.ui-dialog-title").text("Baixar Parcela");
							}
					});
				}
			}
		}
	});
	$("span.ui-dialog-title").text("Baixar Parcela");
	$('#divBaixarParcela').dialog('open');
//***** BUSCAR QUOTA PRESSIONAR ENTER OU TAB *******
	$('#quotaPesquisarQuota').keypress(function(e){
		if (e.keyCode == 13 || e.keyCode == 9 ){
			$.ajax({ type	: "POST", datatype : "php", url	: "busQuotaBuscarPor.php",
				data		: "TextoConsulta=" + $("#quotaPesquisarQuota").val(),		
				async		:  true, cache : true, timeout : 120000,
				complete	:  function(resposta){
					$("span.ui-dialog-title").text("Extrato da quota");
					$("#divAjaxRetornoGrande").empty().append(resposta.responseText).dialog({autoOpen:true});
					$("table#tbConsultarQuota tr").click(function(){
						$('#frmQuota').val($("#quotaPesquisarQuota").val());
						$('#frmDocumento').val($(this).find('td').eq(0).html());
						$('#frmValor').val($(this).find('td').eq(1).html());
						$('#frmValorPago').val($(this).find('td').eq(3).html());
						wss_valorPago = $(this).find('td').eq(3).html();
						$('#frmObservacao').val($(this).find('td').eq(6).html());
						$("#divAjaxRetornoGrande").dialog('close');
						$("span.ui-dialog-title").text("Baixar Parcela");
					});	
				}
			});
		};
	});	
</script>		