<div id='divConfiguracao' title='Configuração' style='display:none'>
	<table class='tbNormal' border=0>
		<tr><td align='right'>Campanha</td><td><input type='text' class='inputNumero' id='configuracaoCampanha' size='1' value=''></td><td align='right'>Agência sem dígito</td><td><input type='text' class='inputNumero' id='configuracaoAgencia' size='6' value=''></td></tr>
		<tr><td align='right'>Descrição</td><td><input type='text' class='inputTexto' id='configuracaoDescricao' size='30' value=''></td><td align='right'>Conta sem dígito</td><td><input type='text' class='inputNumero' id='configuracaoConta' size='6' value=''></td></tr>
		<tr><td align='right'>Total de parcelas</td><td><input type='text' class='inputNumero' id='configuracaoParcelas' size='1' value=''></td><td align='right'>Convênio</td><td><input type='text' class='inputNumero' id='configuracaoConvenio' size='6' value=''></td></tr>
		<tr><td align='right'>Valor da parcela</td><td><input type='text' class='inputNumero' id='configuracaoValorDaParcela' size='10' value=''></td><td></td><td></td></tr>
		<tr><td align='right'>Valor da parcela única</td><td><input type='text' class='inputNumero' id='configuracaoValorDaParcelaUnica' size='10' value=''></td><td></td><td></td></tr>
		<tr><td align='right'>Instituição</td><td><input type='text' class='inputTexto' id='configuracaoInstituicao' size='60' value=''></td><td></td><td></td></tr>
	</table>
	<table class='tbNormal'>
		<tr><td>Parcela</td><td>Descrição do prêmio</td><td>Data do sorteio</td><td>Data para quitação</td></tr>
		<tr><td>01</td><td><input type='text' class='inputTexto' id='configuracaoDesPar01' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar01' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta01' size='10' value=''></td></tr>
		<tr><td>02</td><td><input type='text' class='inputTexto' id='configuracaoDesPar02' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar02' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta02' size='10' value=''></td></tr>
		<tr><td>03</td><td><input type='text' class='inputTexto' id='configuracaoDesPar03' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar03' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta03' size='10' value=''></td></tr>
		<tr><td>04</td><td><input type='text' class='inputTexto' id='configuracaoDesPar04' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar04' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta04' size='10' value=''></td></tr>
		<tr><td>05</td><td><input type='text' class='inputTexto' id='configuracaoDesPar05' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar05' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta05' size='10' value=''></td></tr>
		<tr><td>06</td><td><input type='text' class='inputTexto' id='configuracaoDesPar06' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar06' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta06' size='10' value=''></td></tr>
		<tr><td>07</td><td><input type='text' class='inputTexto' id='configuracaoDesPar07' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar07' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta07' size='10' value=''></td></tr>
		<tr><td>08</td><td><input type='text' class='inputTexto' id='configuracaoDesPar08' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar08' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta08' size='10' value=''></td></tr>
		<tr><td>09</td><td><input type='text' class='inputTexto' id='configuracaoDesPar09' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar09' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta09' size='10' value=''></td></tr>
		<tr><td>10</td><td><input type='text' class='inputTexto' id='configuracaoDesPar10' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar10' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta10' size='10' value=''></td></tr>
		<tr><td>11</td><td><input type='text' class='inputTexto' id='configuracaoDesPar11' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar11' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta11' size='10' value=''></td></tr>
		<tr><td>12</td><td><input type='text' class='inputTexto' id='configuracaoDesPar12' size='60' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaPar12' size='10' value=''></td><td><input type='text' class='inputTexto' id='configuracaoDtaQta12' size='10' value=''></td></tr>
	</table>
</div>

<script type='text/javascript'>

	$('#opcaoConfiguracao').click(function() {	
		$("span.ui-dialog-title").text('Configuração');
		$('#divConfiguracao').dialog('open');
	});

	$('#divConfiguracao').dialog({autoOpen:false, bgiframe:true, resizable:false, height: 600, width: 800, modal: true,
		overlay:{backgroundColor:'#000', opacity:0.5},
		buttons:{
		'Sair': function() {$(this).dialog('close');},
		'Consultar': function() {
				$.ajax({
				type	: "POST", datatype : "php", url : "Configuracao.bus.php",
				data	: " Operacao=Consultar",
				async	:  true, cache : true, timeout : 120000,
				complete:    function(resposta){
						$("#divAjaxRetornoGrande").empty().append(resposta.responseText).dialog('open');
						$("table#tbConfiguracao tr").click(function(){
							$('#configuracaoCampanha').val($(this).find('td').eq(0).html());
							$('#configuracaoDescricao').val($(this).find('td').eq(1).html());
							$('#configuracaoParcelas').val($(this).find('td').eq(2).html());
							$('#configuracaoValorDaParcela').val($(this).find('td').eq(3).html());
							$('#configuracaoValorDaParcelaUnica').val($(this).find('td').eq(4).html());
							$('#configuracaoInstituicao').val($(this).find('td').eq(5).html());
							$('#configuracaoAgencia').val($(this).find('td').eq(6).html());
							$('#configuracaoConta').val($(this).find('td').eq(7).html());
							$('#configuracaoConvenio').val($(this).find('td').eq(8).html());
							
							$('#tbPremios tr').each(function() {
								if ($(this).find("td").eq(0).html() == '01'){  
								    $('#configuracaoDesPar01').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar01').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta01').val($(this).find("td").eq(3).html());
								}
								if ($(this).find("td").eq(0).html() == '02'){  
								    $('#configuracaoDesPar02').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar02').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta02').val($(this).find("td").eq(3).html());
								}
								if ($(this).find("td").eq(0).html() == '03'){  
								    $('#configuracaoDesPar03').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar03').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta03').val($(this).find("td").eq(3).html());
								}
								if ($(this).find("td").eq(0).html() == '04'){  
								    $('#configuracaoDesPar04').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar04').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta04').val($(this).find("td").eq(3).html());
								}								
								if ($(this).find("td").eq(0).html() == '05'){  
								    $('#configuracaoDesPar05').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar05').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta05').val($(this).find("td").eq(3).html());
								}								
								if ($(this).find("td").eq(0).html() == '06'){  
								    $('#configuracaoDesPar06').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar06').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta06').val($(this).find("td").eq(3).html());
								}							
								if ($(this).find("td").eq(0).html() == '07'){  
								    $('#configuracaoDesPar07').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar07').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta07').val($(this).find("td").eq(3).html());
								}							
								if ($(this).find("td").eq(0).html() == '08'){  
								    $('#configuracaoDesPar08').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar08').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta08').val($(this).find("td").eq(3).html());
								}							
								if ($(this).find("td").eq(0).html() == '09'){  
								    $('#configuracaoDesPar09').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar09').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta09').val($(this).find("td").eq(3).html());
								}							
								if ($(this).find("td").eq(0).html() == '10'){  
								    $('#configuracaoDesPar10').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar10').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta10').val($(this).find("td").eq(3).html());
								}							
								if ($(this).find("td").eq(0).html() == '11'){  
								    $('#configuracaoDesPar11').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar11').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta11').val($(this).find("td").eq(3).html());
								}							
								if ($(this).find("td").eq(0).html() == '12'){  
								    $('#configuracaoDesPar12').val($(this).find("td").eq(1).html());
									$('#configuracaoDtaPar12').val($(this).find("td").eq(2).html());
									$('#configuracaoDtaQta12').val($(this).find("td").eq(3).html());
								}															});
							
							
							$("#divAjaxRetornoGrande").dialog('close');
						});						
				   }
				});							
		},
		'Alterar': function() {
				decisao = confirm('Confirma esta operação?');                  
				if(decisao==true){  
					$.ajax({
					type	: "POST", datatype : "php", url : "Configuracao.bus.php",
					data	: " Operacao=Alterar"
							+ "&Campanha=" + $("#configuracaoCampanha").val()
							+ "&Descricao="  + $("#configuracaoDescricao").val()
							+ "&TotalDeParcelas="  + $("#configuracaoParcelas").val()
							+ "&ValorDaParcela="  + $("#configuracaoValorDaParcela").val()
							+ "&ValorDaParcelaUnica="  + $("#configuracaoValorDaParcelaUnica").val()
							+ "&Instituicao=" +$("#configuracaoInstituicao").val()
							+ "&Agencia=" +$("#configuracaoAgencia").val()
							+ "&Conta=" +$("#configuracaoConta").val()
							+ "&Convenio=" +$("#configuracaoConvenio").val()
							+ "&DesPar01="+$('#configuracaoDesPar01').val() + "&DtaPar01="+$('#configuracaoDtaPar01').val() + "&DtaQta01="+$('#configuracaoDtaQta01').val()
							+ "&DesPar02="+$('#configuracaoDesPar02').val() + "&DtaPar02="+$('#configuracaoDtaPar02').val() + "&DtaQta02="+$('#configuracaoDtaQta02').val()
							+ "&DesPar03="+$('#configuracaoDesPar03').val() + "&DtaPar03="+$('#configuracaoDtaPar03').val() + "&DtaQta03="+$('#configuracaoDtaQta03').val()
							+ "&DesPar04="+$('#configuracaoDesPar04').val() + "&DtaPar04="+$('#configuracaoDtaPar04').val() + "&DtaQta04="+$('#configuracaoDtaQta04').val()
							+ "&DesPar05="+$('#configuracaoDesPar05').val() + "&DtaPar05="+$('#configuracaoDtaPar05').val() + "&DtaQta05="+$('#configuracaoDtaQta05').val()
							+ "&DesPar06="+$('#configuracaoDesPar06').val() + "&DtaPar06="+$('#configuracaoDtaPar06').val() + "&DtaQta06="+$('#configuracaoDtaQta06').val()
							+ "&DesPar07="+$('#configuracaoDesPar07').val() + "&DtaPar07="+$('#configuracaoDtaPar07').val() + "&DtaQta07="+$('#configuracaoDtaQta07').val()
							+ "&DesPar08="+$('#configuracaoDesPar08').val() + "&DtaPar08="+$('#configuracaoDtaPar08').val() + "&DtaQta08="+$('#configuracaoDtaQta08').val()
							+ "&DesPar09="+$('#configuracaoDesPar09').val() + "&DtaPar09="+$('#configuracaoDtaPar09').val() + "&DtaQta09="+$('#configuracaoDtaQta09').val()
							+ "&DesPar10="+$('#configuracaoDesPar10').val() + "&DtaPar10="+$('#configuracaoDtaPar10').val() + "&DtaQta10="+$('#configuracaoDtaQta10').val()
							+ "&DesPar11="+$('#configuracaoDesPar11').val() + "&DtaPar11="+$('#configuracaoDtaPar11').val() + "&DtaQta11="+$('#configuracaoDtaQta11').val()
							+ "&DesPar12="+$('#configuracaoDesPar12').val() + "&DtaPar12="+$('#configuracaoDtaPar12').val() + "&DtaQta12="+$('#configuracaoDtaQta12').val()
					,async	:  true, cache : true, timeout : 120000,
					complete:    function(resposta){
							$("#divAjaxRetornoPequeno").empty().append(resposta.responseText).css({display:"block"});
							$("#divAjaxRetornoPequeno").dialog('open');
					   }
					});
				}  
			}
		}
	});

</script>	