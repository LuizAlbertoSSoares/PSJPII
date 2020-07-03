<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<table class='tbNormal'><tr><td>Pesquisar por
<select id="cmbPesquisarPor">
<option value='Nome'>Nome</option>
<option value='Email'>Email</option>
<option value='CPF'>CPF</option>
<option value='Conta'>Conta</option>
</select>
<input type='text' class='inputPesquisa' id='contribuintePesquisaContribuinte' value=''>Codigo<input type='text' class='input' id='frmIDcontribuinte' maxlength='20' size='10' value='NOVO' disabled="disabled"></td></tr></table>
<hr>
<table id='tbCadastro' class='tbNormal'>

<tr><td align='right'>CPF	   				</td><td><input type='text' class='input' id='frmCPFCNPJ' maxlength='16' size='16'>&nbsp RG &nbsp <input type='text' class='input' id='frmRG' maxlength='10' size='09'>&nbsp Aniversario (Dia/Mes)&nbsp<input type='text' class='input' id='frmDiaMesAniversario' size='05'></td></tr>
<tr><td align='right'>Nome					</td><td><input type='text' class='input' id='frmNome' maxlength='40' size='45' value=''></td></tr>
<tr><td align='right'>Endereço				</td><td><input type='text' class='input' id='frmEndereco' size='45'></td></tr>
<tr><td align='right'>Bairro				</td><td><input type='text' class='input' id='frmBairro' size='45' maxlength='40' value='Aguas Claras'></td></tr>
<tr><td align='right'>Cidade				</td><td><input type='text' class='input' id='frmCidade' size='45'maxlength='40' value='Brasília'></td></tr>
<tr><td align='right'>CEP					</td><td><input type='text' class='input' id='frmCep' size='10' maxlength='08'> &nbsp UF &nbsp <input type='text' class='input' id='frmEstado' size='03' maxlength='02' value='DF'></td></tr>
<tr><td align='right'>E-mail				</td><td><input type='text' class='input' id='frmEmail' size='45' maxlength='40'></td></tr>
<tr><td align='right'>Telefone Residencial	</td><td><input type='text' class='input' id='frmTelResidencial' size='15' value='(61) '></td></tr>
<!-- <tr><td align='right'>Comercial				</td><td><input type='text' class='input' id='frmTelComercial' size='15' value=''></td></tr> -->
<tr><td align='right'>Celular				</td><td><input type='text' class='input' id='frmTelCelular' size='15' value='(61) '></td></tr>
<tr><td align='right'>Tipo de Pessoa		</td><td><input type='text' class='input' id='frmTipoPes' maxlength='01' size='01' value='F'></td></tr>
</table>

<table id='tbCadastro' class='tbNormal'>
<tr>

<td align=right width='20%' height='20%'></td><td></td>
</tr>
<tr>
<td align=right width='120%' height='20%'>Quotas</td><td width='50%'><div id='divQuotasDoCadastro'></div></td>
<!-- <td align=right width='20%' height='20%'>Banco</td><td> 
<select id="cmbBanco"> -->

<!--
</select>
</td>
</tr>

<tr>
<td align=right width='10%' height='20%'></td><td width='50%'></td>
<td align=right width='20%' height='20%'>Agencia</td><td><input type='text' class='inputNumero' id='frmAgencia' size='05'></td>
</tr>
<tr>
<td align=right width='10%' height='20%'></td><td width='50%'></td>
<td align=right width='20%' height='20%'>Conta</td><td><input type='text' class='inputNumero' id='frmConta' size='10'></td>
</tr>
<tr>
<td align=right width='10%' height='20%'></td><td width='50%'></td>
<td align=right width='20%' height='20%'></td><td>
<input type="radio" name="TipoConta" value="C" checked> Corrente
<input type="radio" name="TipoConta" value="P"> Poupança<br>
</tr>
<tr>
<td align=right width='10%' height='20%'></td><td width='50%'></td>
<td align=right width='20%' height='20%'>Dia para débito</td><td><input type='text' class='inputNumero' id='frmDiaDebito' size='1' value='0'></td>
</tr> -->
</table>
<hr>
<table class='tbCadastro'><tr><td align='right'>
<input type='button' id='btnSalvar' value='Salvar'>
<input type='button' id='btnLimpar' value='Limpar'>
<input type='button' id='btnExcluir' value='Excluir'>
</td></tr></table>

<script type='text/javascript'>

	$("#btnLimpar").click(function(){
		$('input:text').each(function(){$(this).val('');});
		$('#frmIDcontribuinte').val('NOVO');
		$('#frmBairro').val('Aguas Claras');
		$('#frmCidade').val('Brasilia');
		$('#frmEstado').val('DF');
		$('#frmTelResidencial').val('(61) ');
	//	$('#frmTelComercial').val('');
		$('#frmTelCelular').val('(61) ');
		$('#frmTipoPes').val('F');
	//	$('#frmDiaDebito').val('0');
	});

//***** BUSCAR CONTRIBUINTE AO PRESSIONAR ENTER OU TAB *******
		$('#contribuintePesquisaContribuinte').keypress(function(e){
			if (e.keyCode == 13 || e.keyCode == 9 ){
				$.ajax({
					type	: "POST", datatype : "php", url : "busContribuinteBucarPor.php",
					data	: "TextoConsulta=" + $('#contribuintePesquisaContribuinte').val()
					        + "&PesquisarPor=" + $('#cmbPesquisarPor').val(),
					async	:  true, cache : true, timeout : 120000,
					complete:    function(resposta){$("#divAjaxRetornoGrande").empty().append(resposta.responseText).dialog('open');}
				});
			};
		});	
//***** MASCARA PARA CPF OU CNPJ CONFORME SELECAO DO TIPO DE PESSOA FISICA OU JURIDICA
		$("#frmCPFCNPJ").mask("999.999.999-99");
		$('#frmTipoPes').keyup(function(e){
			if (e.keyCode != 13){
				if ($('#frmTipoPes').val() == "J" || $('#frmTipoPes').val() == "j"){
					$("#frmCPFCNPJ").unmask("999.999.999-99");
					$("#frmCPFCNPJ").mask("99.999.999/9999-99");
				};
				if ($('#frmTipoPes').val() == "F" || $('#frmTipoPes').val() == "f"){
					$("#frmCPFCNPJ").unmask("99.999.999/9999-99");
					$("#frmCPFCNPJ").mask("999.999.999-99");
				};				
			}
		});	
//***** MASCARA PARA DIA/MES DO ANIVERSARIO
	$("#frmDiaMesAniversario").mask("99/99");
//***** MASCARA PARA CEP
	$("#frmCep").mask("99.999-999");
	

	$("#btnSalvar").click(function(){
		if ($('#frmNome').val() == ''){alert('Nome deve ser informado!');return;};
	//	var n = $('#frmAgencia').val();
	//	var agndv  = n.substr(n.length - 1,1);
	//	var agnnum = n.substr(0, n.length - 1);
	//	n = $('#frmConta').val();
	//	var ctadv  = n.substr(n.length - 1,1);
	//	var ctanum = n.substr(0, n.length - 1);
	//	if ($("#cmbBanco").val() == 1){
	//		if (!numBB(agnnum, agndv)) {alert("Digito da agencia BB invalido!");return;};
	//		if (!numBB(ctanum, ctadv)) {alert("Digito da conta BB invalido!");return;};
	//	};
    //    if ($("#cmbBanco").val() == 2){
	//      if (numBRB(ctanum, ctadv) != ctadv) {alert("Digito da conta BRB invalido!");return;};
	//	};
	    $Operacao = 'ALTERAR';
	    if ($('#frmIDcontribuinte').val() == 'NOVO'){$Operacao = 'INCLUIR';};
		$.ajax({
		type	: "POST",
		datatype: "php",
		url		: "busContribuintePersistir.php",
		data	: "frmNome=" + $('#frmNome').val()
				+ "&frmEndereco="+ $('#frmEndereco').val()
				+ "&frmBairro="+ $('#frmBairro').val()
				+ "&frmCidade="+ $('#frmCidade').val()
				+ "&frmCep="+ $('#frmCep').val()
				+ "&frmEstado="+ $('#frmEstado').val()
				+ "&frmTelCelular="+ $('#frmTelCelular').val()
	//			+ "&frmTelComercial="+ $('#frmTelComercial').val()
				+ "&frmTelResidencial="+ $('#frmTelResidencial').val()
				+ "&frmEmail="+ $('#frmEmail').val()
				+ "&frmRG="+ $('#frmRG').val()
				+ "&frmDiaMesAniversario="+ $('#frmDiaMesAniversario').val()
	//			+ "&frmAgencia="+ $('#frmAgencia').val()
	//			+ "&frmConta="+ $('#frmConta').val()
	//			+ "&frmBanco="+ $("#cmbBanco").val()
	//			+ "&frmFormaDePagamento="+ $("#cmbPagamento").val()
				+ "&frmTipoPes="+ $('#frmTipoPes').val()
				+ "&frmCPF="+ $('#frmCPFCNPJ').val()
				+ "&frmCNPJ="+ $('#frmCPFCNPJ').val()
	//			+ "&frmDiaDebito="+ $('#frmDiaDebito').val()
				+ "&frmContribuinte="+ $('#frmIDcontribuinte').val()
	//			+ "&frmTipoDeConta="+$("input[name='TipoConta']:checked").val()
				+ "&frmOperacao="+ $Operacao,
			async		:  true,
			cache		:  true,
			timeout		:  120000,
			complete	:  function(resposta){
				$("span.ui-dialog-title").text('Resultado do processamento');
 	 	        $("#divAjaxRetornoPequeno").empty().append(resposta.responseText).dialog('open');
				if ($('#frmIDcontribuinte').val() == 'NOVO'){$("#frmIDcontribuinte").val($('#CodigoDocontribuinte').val());};
			}
		});
	});

	function numBB(n, d) {
		for (var i=0, a=9, n = n.toString().split('').reverse(), s = 0; i < n.length; i++, a--)
			s += parseInt(n[i]) * a;
		if ((s %= 11) == 10)
			s = 'x';
		else if (s > 10)
			s = 0;
		return s == d.toString().toLowerCase();
	}
	function numBRB(xn, xd){
	  x = xn;
	  n = xn.substring(0,9);
	  soma = 0;
	  base = 2;
	  for (i=1;i<10;i++){
		soma += n.charAt(9-i) * base;
		base += 1;
		if (base == 10){base = 2;};
	  }
	  dv = soma % 11 < 2 ? 0 : 11 - soma % 11;
	  return dv;
	}

</script>	