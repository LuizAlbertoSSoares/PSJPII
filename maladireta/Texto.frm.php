<html><head><title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">

#divListar{
	width:566px;
    height:30px;
	background-color:#9BCD9B;
    border-radius: 10px;
	border-radius: 10px 10px 0px 0px;
}

#divCabecalho{
	width:716px;
    height:30px;
	background-color:#9BCD9B;
    border-radius: 10px;
	border-radius: 10px 10px 0px 0px;
}
#divEditorDeTexto{
	width:710px;
	border: 3px solid #9BCD9B;
}
input[type=text]{     
    border-radius:4px;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    box-shadow: 1px 1px 2px #333333;    
    -moz-box-shadow: 1px 1px 2px #333333;
    -webkit-box-shadow: 1px 1px 2px #333333;
    background: #0000; 
    border:1px solid #000000;
	font-family: arial, helvetica, serif;
	font-size: 12px;
	width:370px;
	margin-top:6px;
	margin-left:5px;
	padding: 3px 10px;
	
	
}
.botao-abrir {
     background: -webkit-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: -moz-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: -o-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: -ms-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     border: 1px solid #CCCCCE;
     border-radius: 3px;
     box-shadow: 0 3px 0 rgba(0, 0, 0, .3), 0 2px 7px rgba(0, 0, 0, 0.2);
     color: #616165;
     display: inline-block;
     font-family: "Trebuchet MS";
     font-size: 14px;
     font-weight: bold;
     line-height: 10px;
     text-align: center;
     text-decoration: none;
     text-transform: uppercase;
     text-shadow:1px 1px 0 #FFF;
     padding: 5px 15px;
     position: relative;
     width: 50px;
	 margin-left:5px;
}
.botao-salvar {
     background: -webkit-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: -moz-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: -o-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: -ms-linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     background: linear-gradient(bottom, #E0E0E0, #F9F9F9 70%);
     border: 1px solid #CCCCCE;
     border-radius: 3px;
     box-shadow: 0 3px 0 rgba(0, 0, 0, .3), 0 2px 7px rgba(0, 0, 0, 0.2);
     color: #616165;
     display: inline-block;
     font-family: "Trebuchet MS";
     font-size: 14px;
     font-weight: bold;
     line-height: 10px;
     text-align: center;
     text-decoration: none;
     text-transform: uppercase;
     text-shadow:1px 1px 0 #FFF;
     padding: 5px 15px;
     position: relative;
     width: 50px;
	 margin-left:5px;
}

</style>

 </head>

<body>
&nbsp&nbsp Editar texto...
<div id='divCabecalho'>
	 <input type='text' id='nomeTexto' value='' placeholder="Nome do texto">
	<a id='botao-abrir' class="botao-abrir">Abrir</a>
	<a id='botao-salvar' class="botao-salvar">Salvar</a>
</div>
<div id='divEditorDeTexto'>
	<textarea cols="70" rows="27" id="descricaoDoTexto" style="width:100%" class ='estiloTextArea' ></textarea>
<div>
<div id="divAjaxListaTextos"></div>
</body>
</html>
 
<script type='text/javascript' CHARSET='ISO-8859-1'>
 
    $('#botao-abrir').click(function() {
        var ed = tinyMCE.get('descricaoDoTexto');
        $('#descricaoDoTexto').val(ed.getContent());
        $.ajax({type    : "POST", datatype: "php", url: "Texto.bus.php",
        data: "Operacao=Abrir&NomeArquivo="+$('#nomeTexto').val()+"&Texto=" + $('#descricaoDoTexto').val(),
        async: true, cache:true, timeout:12000,
            complete    :  function(resposta){
                var ed   = tinyMCE.get('descricaoDoTexto');
                var novo = resposta.responseText;
				var val  = novo;
                ed.setContent(val);
            }  
        });                  
    });          
    $('#botao-salvar').click(function() {
		var ed   = tinyMCE.get('descricaoDoTexto');
		$('#descricaoDoTexto').val(ed.getContent());
		$.ajax({type    : "POST", datatype: "php", url: "Texto.bus.php",
			data: "Operacao=Salvar&NomeArquivo="+$('#nomeTexto').val()+"&Texto=" + $('#descricaoDoTexto').val(),
			async: true, cache:true, timeout:12000, complete    :  function(resposta){ if(resposta.responseText != ""){alert(resposta.responseText);}}  
		});                  
    });       
	$('#botao-abrir').mouseover(function()  { $(this).css("cursor","hand"); })	
	$('#botao-salvar').mouseover(function() { $(this).css("cursor","hand"); })	
 
	$("#divAjaxListaTextos").dialog({autoOpen:false,
		bgiframe: true, resizable: false, height: 380, width: 445, modal: true,
		Overlay: {backgroundColor: '#000', opacity: 0.5},
		buttons: {'Sair': function(){$(this).dialog('close');},
				  'Imprimir'	: function() {$(this).dialog('close');printPage('divAjaxListaTextos');}
		}
	});
 
 	$('#nomeTexto').keypress(function(e){
		if (e.keyCode == 13){
			$.ajax({type: "POST", datatype : "php", url : "Texto.bus.php",
			  data	: "Operacao=Listar",
			  async	:  true, cache : true, timeout : 12000,
			  complete:    function(resposta){
					 $("#divAjaxListaTextos").empty().append(resposta.responseText).dialog('open');
					 $("table#tbArquivos tr").click(function(){
						$('#nomeTexto').val($(this).find('td').eq(0).html())
						$("#divAjaxListaTextos").dialog('close');
						$('#botao-abrir').trigger("click");
					});
				}
			});	
		};
	});	
 
    tinyMCE.init({
	  entity_encoding : "raw",
	  mode : "textareas",
	  theme : "advanced",
	  plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	  theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	  theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,code,|,preview,|,forecolor,backcolor",
	  theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,print,|,ltr,rtl,|,fullscreen",
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
	  template_replace_values : { username : "Some User", staffid : "991234"}
    });
</script>   
 