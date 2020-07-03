<?php
require_once('busConsistirPemissao.php');
include "Conn.php";
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$dataReferencia = "'".$ano."-".$mes."-".$dia."'";
$horaReferencia = date('H').date('i')."00";
$horaMinuto     = date('H').":".date('i');

if ($_POST['Operacao'] == 'QuotasDoContribuinte'){
	$sql = "SELECT * FROM sce_quota WHERE QUO_ID_CONTRIBUINTE = ".$_POST['parametroConribuinte'];
	$exe =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exe);
	echo "(";
	$i = 0;
	while ($row = mysql_fetch_assoc($exe)){
	$i += 1;
		if ($i > 1){echo "-";};
		echo $row['QUO_ID_QUOTA'];
	}
	echo")";
	exit;
}

$TextoConsulta = $_POST['TextoConsulta'];
$PesquisarPor = $_POST['PesquisarPor'];

$sql = "SELECT * FROM sce_contribuinte WHERE CTB_NOME LIKE '%".$TextoConsulta."%' ORDER BY CTB_Nome ASC LIMIT 200";
if ($PesquisarPor == 'Email'){$sql = "SELECT * FROM sce_contribuinte WHERE CTB_EMAIL LIKE '%".$TextoConsulta."%' ORDER BY CTB_Nome ASC LIMIT 200";};
if ($PesquisarPor == 'CPF'){$sql = "SELECT * FROM sce_contribuinte WHERE CTB_CPF LIKE '%".$TextoConsulta."%' ORDER BY CTB_Nome ASC LIMIT 200";};
if ($PesquisarPor == 'Conta'){$sql = "SELECT * FROM sce_contribuinte WHERE CTB_NM_CONTA_CORRENTE LIKE '%".$TextoConsulta."%' ORDER BY CTB_Nome ASC LIMIT 200";};

$exec =  mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exec);
echo "<table id='tbConsultaContribuinte' class='tbNormal'>";
while ($row = mysql_fetch_assoc($exec)){
	echo "<tr>";
	echo "<td align='left'>".$row['CTB_NOME']."</td>";
	$cpf_cnpj = $row['CTB_CPF'];if ($row['CTB_CNPJ'] != ''){$cpf_cnpj = $row['CTB_CNPJ'];};
	echo "<td align='right'>".$cpf_cnpj."</td>";
	echo "<td align='right'>".$row['CTB_TIPO']."</td>";
	echo "<td align='right'>".$row['CTB_ID']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_ENDERECO']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_BAIRRO']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_MUNICIPIO']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_CEP']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_UF']."</td>";
	echo "<td align='left'>".$row['CTB_EMAIL']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_RG']."</td>";
	echo "<td style='display: none' align='left'>".$row['ctb_fone_residencial']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_FONE_COMERCIAL']."</td>";
	echo "<td align='left'>".$row['CTB_FONE_MOBILE']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_DIA_MES_ANIVERSARIO']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_ID_BANCO']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_NM_AGENCIA']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_NM_CONTA_CORRENTE']."</td>";
	echo "<td style='display: none' align='left'>".$row['CTB_CONTA_POUPANCA']."</td>";
	echo "<td style='display: none' align='left'>".$row['diaParaDebito']."</td>";
	echo "</tr>";
}
echo "</table>";
?>
<script type='text/javascript'>
	$('table#tbConsultaContribuinte tr').hover( 
		function(){ $(this).addClass('destaque'); }, 
		function(){ $(this).removeClass('destaque'); } 
	);  
	$("table#tbConsultaContribuinte tr").click(function(){
		$('#frmIDcontribuinte').val($(this).find('td').eq(3).html());
		$('#frmNome').val($(this).find('td').eq(0).html());
		$('#frmEndereco').val($(this).find('td').eq(4).html());
		$('#frmBairro').val($(this).find('td').eq(5).html());
		$('#frmCidade').val($(this).find('td').eq(6).html());
		$('#frmCep').val($(this).find('td').eq(7).html());
		$('#frmEstado').val($(this).find('td').eq(8).html());
		$('#frmEmail').val($(this).find('td').eq(9).html());
		$('#frmTipoPes').val($(this).find('td').eq(2).html());
		$('#frmCPFCNPJ').val($(this).find('td').eq(1).html());
		$('#frmRG').val($(this).find('td').eq(10).html());
		$('#frmTelResidencial').val($(this).find('td').eq(11).html());
		$('#frmTelComercial').val($(this).find('td').eq(12).html());
		$('#frmTelCelular').val($(this).find('td').eq(13).html());
		$('#frmDiaMesAniversario').val($(this).find('td').eq(14).html());
		var banco = $(this).find('td').eq(15).html();
		$('#cmbBanco option').each(function(){if($(this).val() == banco){$(this).attr('selected',true);}});		
		$('#frmAgencia').val($(this).find('td').eq(16).html());
		$('#frmConta').val($(this).find('td').eq(17).html());
		var tipoConta = $(this).find('td').eq(18).html();if (tipoConta != 'S'){tipoConta = 'C'};
		var presetValue = "C"; $("[name=TipoConta]").filter("[value="+presetValue+"]").attr("checked","checked"); 
		if (tipoConta == "S"){presetValue = "P"; $("[name=TipoConta]").filter("[value="+presetValue+"]").attr("checked","checked");};
		$('#frmDiaDebito').val($(this).find('td').eq(19).html());
		$('#divAjaxRetornoGrande').dialog('close');

		$.ajax({
			type	: "POST", datatype : "php", url : "busContribuinteBucarPor.php",
			data	: "Operacao=QuotasDoContribuinte" + "&parametroConribuinte=" + $('#frmIDcontribuinte').val(),
			async	:  true, cache : true, timeout : 120000,
			complete:    function(resposta){$("#divQuotasDoCadastro").empty().append(resposta.responseText);}
		});		
	});	
</script>
