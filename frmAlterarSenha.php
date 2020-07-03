<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<div id='divAlterarSenha' style='display:none'>
	<table class='tbNormal'>
		<tr><td align='right'>Usuário</td><td><input type='text' class='input' id='alterarSenhaUsuario'></td></tr>
		<tr><td align='right'>Senha atual</td><td><input type='password' class='input' id='alterarSenhaSenhaAtual'></td></tr>
		<tr><td align='right'>Senha Nova</td><td><input type='password' class='input' id='alterarSenhaSenhaNova'></td></tr>
		<tr><td align='right'>Confirmar Senha Nova</td><td><input type='password' class='input' id='alterarSenhaSenhaNovaConfirmar'></td></tr>
	</table>
</div>
<script type='text/javascript'>
	$("#divAlterarSenha").dialog({autoOpen:false, bgiframe: true, resizable: false, height: 210, width: 340, modal: true,
		Overlay: {backgroundColor: '#000', opacity: 0.5},
		buttons: {
			'Sair': function(){$(this).dialog('close');},
			'Alterar': function(){
				if ($('#alterarSenhaUsuario').val() == ''){alert('Usuário deve ser informado!');return;};
				if ($('#alterarSenhaSenhaAtual').val() == ''){alert('Senha atual deve ser informada!');return;};
				if ($('#alterarSenhaSenhaNova').val() == ''){alert('Nova senha deve ser informada!');return;};
				if ($('#alterarSenhaSenhaNova').val() != $('#alterarSenhaSenhaNovaConfirmar').val()){alert('Senha invalida!!');return;};
				$.ajax({
					type	: 'POST', datatype: 'php', url: 'busAlterarSenha.php',
					data	: 'Operacao=ALTERARSENHA'
							+ '&alterarSenhaUsuario=' + $('#alterarSenhaUsuario').val()
							+ '&alterarSenhaSenhaAtual=' + $('#alterarSenhaSenhaAtual').val()
							+ '&alterarSenhaSenhaNova=' + $('#alterarSenhaSenhaNova').val(),
					async	: true, cache: true, timeout: 12000,
					complete: function(resposta){
						$('#divAjaxRetornoPequeno').empty().append(resposta.responseText).dialog('open');
						$('input:text').each(function(){$(this).val('');});
						$('input:password').each(function(){$(this).val('');});
						$('textarea').each(function(){$(this).val('');});
					}
				});
			}
		}
	});
	$('#divAlterarSenha').dialog('open');
	$('span.ui-dialog-title').text('Alterar Senha');
	$('.ui-widget-content').css('background',"url('bgMain.gif')");
</script>