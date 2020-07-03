<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
$perfilDoUsuario     = $_SESSION["perfilDoUsuario"];
if ($perfilDoUsuario != "Master"){echo "Usuário sem permisão para esta transação!";exit;};
?>
<div id='divGerarArquivoRemessa' title='Gerar Arquivo de Remessa' style='display:none'>
	<table width='100%'>
		<tr><td align='right'>Data para Débito</td><td><input type='text' id='frmRemessaDataDebito' value=''></td></tr>
		<tr><td align='right'>Banco</td>
		     <td>
			 <select id='cmbRemessaBanco'>
			 <option value='756'>SICOOB</option>
			 <option value='001'>BB - Banco do Brasil</option>    
			 <option value='104'>CEF - Caixa Economica Federal</option>			 
			 </select>
			 </td>
		</tr>
		<tr><td align='right'>Mes e Ano da Parcela</td><td><input type='text' id='frmRemessaMesAnoParcela' size='5' value=''></td></tr>
	</table>
</div>
<script type='text/javascript'>

	$("span.ui-dialog-title").text("Gerar Arquivo de Remessa");
	$('#frmRemessaDataDebito').datepicker();
	$("span.ui-dialog-title").text("Gerar Arquivo de Remessa");
	$('#divGerarArquivoRemessa').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 280, width: 500, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
			'Sair': function() {$(this).dialog('close');},
			Gerar: function() {
				$(this).dialog('close');
				acaoGerarArquivoRemessa();
			}
		}
	});
	$('#divGerarArquivoRemessa').dialog('open');
	
	function acaoGerarArquivoRemessa(){
	
		$('#divAjaxGerarRemessa').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 620, width: 950, modal: true,
			overlay:{backgroundColor:'#000', opacity:0.5},
			buttons:{
				'Sair': function() {$(this).dialog('close');},
				'Marcar todos': function() {$(':checkbox').each(function(){ this.checked = true; });},
				'Desmarcar todos': function() {$('input:checked').each(function(){this.checked = false;});},
				'Gerar arquivo': function() { 
					var listaEmail = "";
					$('input:checked').each(function(){
						listaEmail += this.id;
						listaEmail += ";";
					});
					$("span.ui-dialog-title").text("Processando");
					$("#divAguarde").dialog('open');
					if ($('#cmbRemessaBanco').val() == '756'){ paginaPHP = 'busGerarArquivoRemessaSICOOB.php';			}
					if ($('#cmbRemessaBanco').val() == '001'){ paginaPHP = 'busGerarArquivoRemessaParaDebitoBB.php';}
					if ($('#cmbRemessaBanco').val() == '104'){ paginaPHP = 'busGerarArquivoRemessaParaDebitoCEF.php';}
					$(this).dialog('close');
					$.ajax({
						type	: "POST", datatype : "php", url : paginaPHP,
						data	: "frmMensagem=" + $("#btnEnviar").val()
								+ "&dataParaDebito=" + $('#frmRemessaDataDebito').val()
								+"&listaEmail=" + listaEmail,
						async	:  true, cache : true, timeout : 120000,
						complete:    function(resposta){
							$("#divAguarde").dialog('close');
							$("span.ui-dialog-title").text('Resultado do processamento');
							$("#divAjaxRetornoGrande").empty().append(resposta.responseText).css({display:"block"});
							$("#divAjaxRetornoGrande").dialog('open');
						}
					});				
				}
			}
		});	
	
     	$("span.ui-dialog-title").text("Processando");
		$("#divAguarde").dialog('open');
		var paginaPHP = "#";
		if ($('#cmbRemessaBanco').val() == '756'){ paginaPHP = 'busSelecionarTitulosParaDebitoBRB.php';}
		if ($('#cmbRemessaBanco').val() == '001'){ paginaPHP = 'busSelecionarTitulosParaDebitoBB.php';}
		if ($('#cmbRemessaBanco').val() == '104'){ paginaPHP = 'busSelecionarTitulosParaDebitoCEF.php';}		
		$.ajax({
			type	: "POST", datatype : "php", url : paginaPHP,
			data	: "parametroMesAnoDaParcela=" + $('#frmRemessaMesAnoParcela').val()
					+ "&parametroDataParaDebito=" + $('#frmRemessaDataDebito').val()			,
			async	:  true, cache : true, timeout : 120000,
			complete:    function(resposta){
				$("#divAguarde").dialog('close');
				$("span.ui-dialog-title").text("Titulos para debito em conta");
				$("#divAjaxGerarRemessa").empty().append(resposta.responseText).dialog('open');
		   }
	 	});	
	}
	function acaoGerandoRemessa(){
		$("#btnEnviar").click(function(){
			var listaEmail = "";
			$('input:checked').each(function(){
				listaEmail += this.id;
				listaEmail += ";";
			});
			$("span.ui-dialog-title").text("Processando");
			$("#divAguarde").dialog('open');
			if ($('#cmbRemessaBanco').val() == '756'){ paginaPHP = 'busGerarArquivoRemessaSICOOB.php';			}
			if ($('#cmbRemessaBanco').val() == '001'){ paginaPHP = 'busGerarArquivoRemessaParaDebitoBB.php';}
			if ($('#cmbRemessaBanco').val() == '104'){ paginaPHP = 'busGerarArquivoRemessaParaDebitoCEF.php';}
			$.ajax({
			type	: "POST", datatype : "php", url : paginaPHP,
			data	: "frmMensagem=" + $("#btnEnviar").val()
					+ "&dataParaDebito=" + $('#frmRemessaDataDebito').val()
					+"&listaEmail=" + listaEmail,
			async	:  true, cache : true, timeout : 120000,
			complete:    function(resposta){
						$("#divAguarde").dialog('close');
						$("span.ui-dialog-title").text('Resultado do processamento');
						$("#divAjaxRetornoGrande").empty().append(resposta.responseText).css({display:"block"});
						$("#divAjaxRetornoGrande").dialog('open');
					}
			});
		});
	}

</script>	
