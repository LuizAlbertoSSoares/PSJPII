<div id='divEnviarCorreio' title='Enviar correio' style='display:none'>
	<table class='tbNormal'>
		<tr>
			<td align='right'>Mensagem</td>
			<td>
			<select id='mensagemCorreio' name='mensagemCorreio'>
				<option value='Agradecimento'>Agradecimento</option>			
				<option value='Sorteio'      >Sorteio</option>
				<option value='Lembrete'     >Lembrete</option>
			</select>			
			</td>
		</tr>
	</table>
	<div id='divDestinatario'></div>
</div>

<script type='text/javascript'>
	$("#divEnviarCorreio").dialog({autoOpen:false, bgiframe: true, resizable: false, height: 505, width: 720, position: [430,105], modal: true,
		Overlay: {backgroundColor: '#000', opacity: 0.5},
		buttons: {
			'Sair': function(){
				$('input:text').each(function(){$(this).val('');});
				$(this).dialog('close');
			},
			'Buscar destinatário': function(){
				$.ajax({type: 'POST', datatype: 'php', url: 'EnviarCorreio.bus.php',
					data	: 'Mensagem=' + $("#mensagemCorreio option:selected").val() + '&Operacao=BuscarDestinatario',
					async	: true, cache: true, timeout: 12000,
					complete: function(resposta){ $('#divDestinatario').empty().append(resposta.responseText); }
				});				
			},
			'Marcar todos': function() {$(':checkbox').each(function(){ this.checked = true; });},
			'Desmarcar todos': function() {$('input:checked').each(function(){this.checked = false;});},
			'Exibir correio': function(){
				var listaEmail = "";
				$('input:checked').each(function(){listaEmail += this.id;listaEmail += ";";});
				$('#divDestinatario').empty();
				$.ajax({type: 'POST', datatype: 'php', url: 'EnviarCorreio.bus.php',
					data	: 'Mensagem=' + $("#mensagemCorreio option:selected").val()
							+ '&Operacao=ExibirCorreio'
							+ '&ListaEmail=' + listaEmail ,
					async	: true, cache: true, timeout: 12000,
					complete: function(resposta){ $('#divDestinatario').empty().append(resposta.responseText); }
				});				
			},
			'Enviar correio': function(){
				decisao = confirm('Confirma?');    
					if(decisao == true){
					var listaEmail = "";
					$('input:checked').each(function(){listaEmail += this.id;listaEmail += ";";});
					$('#divDestinatario').empty();
					$.ajax({type: 'POST', datatype: 'php', url: 'EnviarCorreio.bus.php',
						data	: 'Mensagem=' + $("#mensagemCorreio option:selected").val()
								+ '&Operacao=EnviarCorreio'
								+ '&ListaEmail=' + listaEmail ,
						async	: true, cache: true, timeout: 12000,
						complete: function(resposta){ $('#divDestinatario').empty().append(resposta.responseText); }
					});									
				}
			}
		}
	});
	$('#divEnviarCorreio').dialog('open');
	$('.ui-widget-content').css('background',"url('img/bgMain.gif')");
	$('#divDestinatario').empty();
</script>