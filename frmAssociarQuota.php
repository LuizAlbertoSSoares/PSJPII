<div id='divAssociarQuota' title='Associar Quota ao Contribuinte:' style='display:none'>

<input type='text' class='inputPesquisa' id='quotaPesquisaContribuinte' value=''>

	<table class='tbNormal'>
		<tr><td align='right'>Quota</td><td><input type='text' class='inputNumero' id='frmQuotaAssociar' size='14' value=''></td></tr>
		<tr><td align='right'>Código do contribuinte      </td><td><input type='text' class='inputNumero' id='frmContribuinteAssociar'  size='14' value='' disabled='disabled'></td></tr>
		<tr><td align='right'>Forma de pagamento</td>
		<td>
		<select size="1" id="cmbFormaDePagamento">
		<option value="B">Boleto</option>
		<option value="D">Depósito em conta</option>
		<option value="C">Cartão (crédito/débito)</option>
		</select>
		</td></tr>
		<tr><td align='right'>Tipo quota</td>
		<td>
		<select size="1" id="cmbTipoDeQuota">
		<option value="N">10 Parcelas</option>
		<option value="U">Parcela Única</option>
		</select>
		</td></tr>		
	</table>
</div>

<script type='text/javascript'>

	$('#opcaoAssociarQuota').click(function() {	
		$("span.ui-dialog-title").text('Associar Quota');
		$('#divAssociarQuota').dialog('open');
	});

	$('#divAssociarQuota').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 250, width: 450, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
		'Sair': function() {$(this).dialog('close');},
		'Alterar Forma de Pagamento': function() {
			if ($("#frmQuotaAssociar").val() == ''){alert('Quota deve ser informado!');return;};
			if ($("#frmContribuinteAssociar").val() == ''){alert('Contribuinte deve ser informado!');return;};		
			decisao = confirm('Confirma Alteração na Forma de Pagamento ?');   
			if(decisao==true){  
				$.ajax({
				type	: "POST", datatype : "php", url : "busAssociarQuota.php",
				data	: " Operacao=AlterarFormaDePagamento"
						+ "&Quota=" + $("#frmQuotaAssociar").val()
						+ "&Contribuinte="  + $("#frmContribuinteAssociar").val()
						+ "&FormaDePagamento=" +$("#cmbFormaDePagamento").val(),
				async	:  true, cache : true, timeout : 120000,
				complete:    function(resposta){
						$("#divAjaxRetornoPequeno").empty().append(resposta.responseText).dialog('open');
				   }
				});							
			}
		},
		'Excluir Associação': function() {
			if ($("#frmQuotaAssociar").val() == ''){alert('Quota deve ser informado!');return;};
			if ($("#frmContribuinteAssociar").val() == ''){alert('Contribuinte deve ser informado!');return;};
			decisao = confirm('Confirma Excluir Associação ?');                  
			if(decisao==true){  
				$("span.ui-dialog-title").text("Processando");
				$("#divAguarde").dialog('open');
				$.ajax({
				type	: "POST", datatype : "php", url : "busAssociarQuota.php",
				data	: " Operacao=ExcluirAssociacao"
						+ "&Quota=" + $("#frmQuotaAssociar").val()
						+ "&Contribuinte="  + $("#frmContribuinteAssociar").val()
						+ "&FormaDePagamento=" +$("#cmbFormaDePagamento").val(),
				async	:  true, cache : true, timeout : 120000,
				complete:    function(resposta){
						$("#divAguarde").dialog('close');
						$("span.ui-dialog-title").text('Resultado da associação de quota');
						$("#divAjaxRetornoPequeno").empty().append(resposta.responseText).css({display:"block"});
						$("#divAjaxRetornoPequeno").dialog('open');
				   }
				});				
			}
		},
		'Associar': function() {
				if ($("#frmQuotaAssociar").val() == ''){alert('Quota deve ser informado!');return;};
				if ($("#frmContribuinteAssociar").val() == ''){alert('Contribuinte deve ser informado!');return;};
				decisao = confirm('Confirma esta operação ?');                  
				if(decisao==true){  
					$("span.ui-dialog-title").text("Processando");
					$("#divAguarde").dialog('open');
					$.ajax({
					type	: "POST", datatype : "php", url : "busAssociarQuota.php",
					data	: " Operacao=Associar"
							+ "&Quota=" + $("#frmQuotaAssociar").val()
							+ "&Contribuinte="  + $("#frmContribuinteAssociar").val()
							+ "&TipoDeQuota="  + $("#cmbTipoDeQuota").val()
							+ "&FormaDePagamento=" +$("#cmbFormaDePagamento").val(),
					async	:  true, cache : true, timeout : 120000,
					complete:    function(resposta){
							$("#divAguarde").dialog('close');
							$("span.ui-dialog-title").text('Resultado da associação de quota');
							$("#divAjaxRetornoPequeno").empty().append(resposta.responseText).css({display:"block"});
							$("#divAjaxRetornoPequeno").dialog('open');
//							$('input:text').each(function(){$(this).val('');});
					   }
					});
				}  
			}
		}
	});

//***** BUSCAR CONTRIBUINTE AO PRESSIONAR ENTER *******
	$('#quotaPesquisaContribuinte').keypress(function(e){
		if (e.keyCode == 13){
			$.ajax({
				type	: "POST", datatype : "php", url : "busContribuinteBucarPor.php",
				data	: "TextoConsulta=" + $('#quotaPesquisaContribuinte').val(),
				async	:  true, cache : true, timeout : 120000,
				complete:    function(resposta){
					$("#divAjaxRetornoGrande").empty().append(resposta.responseText).dialog('open');
					$("table#tbConsultaContribuinte tr").click(function(){
						$('#quotaPesquisaContribuinte').val($(this).find('td').eq(0).html());
						$('#frmContribuinteAssociar').val($(this).find('td').eq(3).html());
					});
				}
			});
		};
	});	

</script>	