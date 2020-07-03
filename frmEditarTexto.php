<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
?>
<table class='tbCadastro'><tr><td align='right'>
	Editar Texto Padrão para E-mail de :
	<select id='cmbArquivoTexto'>
	<option value='Agradecimento'>Agradecimento</option>
	<option value='Parcelas_Pendentes'>Parcelas_Pendentes</option>
	<option value='Comunicar_Sorteio'>Comunicar_Sorteio</option>			 
	</select>
	<input type='button' id='btnAbrirTexto' value='Abrir'> 
	&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp | &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
	<input type='button' id='btnSalvarTexto' value='Salvar'>
</td></tr></table>

<table id='tbCadastro' class='tbNormal'>
<tr><td><textarea cols="70" rows="27" id="frmTexto" style="width:100%"></textarea></td></tr>
</table>
<hr>

<script type='text/javascript' CHARSET='ISO-8859-1'> 

	tinyMCE.init({
//-------	execcommand_callback : "tratarCallback",
			entity_encoding : "raw",
			mode : "textareas",
			theme : "advanced",
			plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",

    		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,code,|,preview,|,forecolor,backcolor",
    		theme_advanced_buttons3 : "",
    		theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_resizing : true,
			skin : "o2k7",
			skin_variant : "silver",
			content_css : "css/example.css",
			template_external_list_url : "js/template_list.js",
			external_link_list_url : "js/link_list.js",
			external_image_list_url : "js/image_list.js",
			media_external_list_url : "js/media_list.js",
			template_replace_values : {
					username : "Some User",
					staffid : "991234"
			}
	});
	
	$('#btnAbrirTexto').click(function() {
		var ed = tinyMCE.get('frmTexto');
		$('#frmTexto').val(ed.getContent());
		$.ajax({type	: "POST", datatype: "php", url: "busEditarTexto.php", 
		data: "Operacao=Abrir&NomeArquivo="+$('#cmbArquivoTexto').val()+"&Texto=" + $('#frmTexto').val(), 
		async: true, cache:true, timeout:12000,
			complete	:  function(resposta){
				var ed   = tinyMCE.get('frmTexto');
				var novo = resposta.responseText;
				var val  = novo.replace(/nbsp{1}/g,"&nbsp;");
				ed.setContent(val);
			}	
		});			
	});			
	
	$('#btnSalvarTexto').click(function() {
		decisao = confirm('Confima salvar arquivo: ' + $('#cmbArquivoTexto').val());    
		if(decisao==true){  
			var ed   = tinyMCE.get('frmTexto');
			$('#frmTexto').val(ed.getContent());
			$.ajax({type	: "POST", datatype: "php", url: "busEditarTexto.php", 
			data: "Operacao=Salvar&NomeArquivo="+$('#cmbArquivoTexto').val()+"&Texto=" + $('#frmTexto').val(), 
			async: true, cache:true, timeout:12000,
			complete	:  function(resposta){$("#divCentro").empty().append(resposta.responseText);}	
			});					
		}
	});				

//	function tratarCallback(editor_id, elm, command, user_interface, value){
//		switch (command){
//			case "mceSave":
//				var ed   = tinyMCE.get('frmTexto');
//				$('#frmTexto').val(ed.getContent());
//				$.ajax({type	: "POST", datatype: "php", url: "busEditarTexto.php", 
//				data: "Operacao=Salvar&Texto=" + $('#frmTexto').val(), 
//				async: true, cache:true, timeout:12000,
//		//			complete	:  function(resposta){$("#divCentro").empty().append(resposta.responseText);}	
//				});					
//				return true;
//		}
//		return false;
//	}

</script>	