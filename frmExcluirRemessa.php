<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<div id='divExcluirRemessa' style='display:none'>
	<table class='tbNormal'>
	<tr><td align='right'>Banco</td><td>
	 <select id='cmbNumeroBanco'>
	 <option value='001'>BB - Banco do Brasil</option>
	 <option value='070'>BRB - Banco de Brasilia</option>
	 <option value='104'>CEF - Caixa Economica Federal</option>			 
	 </select>
	</td></tr>
	<tr><td align='right'>Remessa</td><td><input type='text' class='input' id='numeroRemessa'></td></tr>
	<tr><td align='right'>Quota específica</td><td><input type='text' class='input' id='numeroQuota'></td></tr>
	</table>
</div>
<script type='text/javascript'>
	$("#divExcluirRemessa").dialog({autoOpen:false, bgiframe: true, resizable: false, height: 210, width: 340, modal: true,
		Overlay: {backgroundColor: '#000', opacity: 0.5},
		buttons: {
			'Sair': function(){$(this).dialog('close');},
			'Excluir': function(){
				if ($('#numeroRemessa').val() == ''){alert('Remessa deve ser informado!');return;};
				decisao = confirm('Confirma exclusão da remessa?');    
				if(decisao==true){  
					$.ajax({
						type	: 'POST', datatype: 'php', url: 'busExcluirRemessa.php',
						data	: 'Operacao=Excluir'
								+ '&numeroRemessa=' + $('#numeroRemessa').val()
								+ '&numeroQuota=' + $('#numeroQuota').val()
								+ '&numeroBanco=' + $('#cmbNumeroBanco').val(),
						async	: true, cache: true, timeout: 12000,
						complete: function(resposta){
							$('#divAjaxRetornoPequeno').empty().append(resposta.responseText).dialog('open');
						}
					});
				}
			}
		}
	});
	$('#divExcluirRemessa').dialog('open');
	$('span.ui-dialog-title').text('Excluir Remessa');
	$('.ui-widget-content').css('background',"url('bgMain.gif')");
</script>