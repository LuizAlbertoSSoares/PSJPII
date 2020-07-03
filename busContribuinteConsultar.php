<?php
require_once('busConsistirPemissao.php');
require_once('phpQuery/phpQuery.php');
$pqTela = phpQuery::newDocumentFile('telas/frmContribuinteCadastro.html');

$caminhoBase = 'telas/';
$itensComCaminhoRelativoSrc = $pqTela->find('*[src]');
function itensComCaminhoRelativoSrcEach($item){
	$item = pq($item);
	$iten->attr('src', $GLOBALS['caminhoBase'].$item->attr('src'));
}
$itensComCaminhoRelativoSrc->each('itensComCaminhorelativoSrcEach');
$itensComCaminhoRelativoHref = $pqTela->find('*[href]');
function itensComCaminhorelativoHrefEach($item){
	$item = pq($item);
	$item->attr('href', $GLOBALS['caminhoBase'].$item->attr('href'));
}
$itensComCaminhoRelativoHref->each('itensCaminhoRelativoHrefEach');

include "Conn.php";
$ContribuinteCodigo = $_POST['TextoConsulta'];
$Banco = '1';

if ($ContribuinteCodigo == ''){
	$linha = "<tr>"
	."<td align=right width='20%' height='20%'></td><td width='50%'></td>"
	."<td align=right width='20%' height='20%'></td><td>"
	."<input type='radio' name='TipoDeConta' value='Corrente' checked >Corrente <input type='radio' name='TipoDeConta' value='Poupanca'>Poupança"
	."</tr>";
	$pqTela->find('#divTipoConta')->append($linha);
}

if ($ContribuinteCodigo != ''){

	$sql = "SELECT * FROM sce_contribuinte WHERE CTB_ID = ".$ContribuinteCodigo;
	$exec =  mysql_query($sql, $conn) or die(mysql_error());
	$row = mysql_num_rows($exec);
	$row = mysql_fetch_assoc($exec);

	$pqTela->find('#frmIDcontribuinte')->val($row['CTB_ID']);
	$pqTela->find('#frmTipoPes')->val($row['CTB_TIPO']);
	$pqTela->find('#frmCPFCNPJ')->val($row['CTB_CPF']);
	if ($row['CTB_CNPJ'] != null){$pqTela->find('#frmCPFCNPJ')->val($row['CTB_CNPJ']);};
	$pqTela->find('#frmRG')->val($row['CTB_RG']);
	$pqTela->find('#frmNome')->val($row['CTB_NOME']);
	$pqTela->find('#frmEndereco')->val($row['CTB_ENDERECO']);
	$pqTela->find('#frmBairro')->val($row['CTB_BAIRRO']);
	$pqTela->find('#frmCidade')->val($row['CTB_MUNICIPIO']);
	$pqTela->find('#frmEstado')->val($row['CTB_UF']);
	$pqTela->find('#frmCep')->val($row['CTB_CEP']);
	$pqTela->find('#frmEmail')->val($row['CTB_EMAIL']);
	$pqTela->find('#frmTelCelular')->val($row['CTB_FONE_MOBILE']);
	$pqTela->find('#frmTelComercial')->val($row['CTB_FONE_COMERCIAL']);
	$pqTela->find('#frmTelResidencial')->val($row['ctb_fone_residencial']);
	$pqTela->find('#frmDiaMesAniversario')->val($row['CTB_DIA_MES_ANIVERSARIO']);
	$pqTela->find('#frmAgencia')->val($row['CTB_NM_AGENCIA']);
	$pqTela->find('#frmConta')->val($row['CTB_NM_CONTA_CORRENTE']);
	
	if ($row['CTB_CONTA_POUPANCA'] == 'S'){
		$linha = "<tr>"
		."<td align=right width='20%' height='20%'></td><td width='50%'></td>"
		."<td align=right width='20%' height='20%'></td><td>"
		."<input type='radio' name='TipoDeConta' value='Corrente'>Corrente <input type='radio' name='TipoDeConta' value='Poupanca' checked >Poupança"
		."</tr>";
	}
	if ($row['CTB_CONTA_POUPANCA'] != 'S'){
		$linha = "<tr>"
		."<td align=right width='20%' height='20%'></td><td width='50%'></td>"
		."<td align=right width='20%' height='20%'></td><td>"
		."<input type='radio' name='TipoDeConta' value='Corrente' checked >Corrente <input type='radio' name='TipoDeConta' value='Poupanca'>Poupança"
		."</tr>";
	}
	$pqTela->find('#divTipoConta')->append($linha);	
	
	$Banco = $row['CTB_ID_BANCO'];

    $sql = "SELECT QUO_ID_EVENTO, QUO_ID_QUOTA, QUO_FORMA_PAGAMENTO
            FROM sce_quota
            WHERE quo_id_contribuinte = ".$ContribuinteCodigo." ORDER BY QUO_ID_EVENTO DESC";
			$exec =  mysql_query($sql, $conn) or die(mysql_error());
			$row = mysql_num_rows($exec);
			while ($row = mysql_fetch_assoc($exec))
			{
				$cmbQuota = "<option value='".sprintf("%05d", $row['QUO_ID_QUOTA'])." ".sprintf("%02d", $row['QUO_ID_EVENTO'])
				."'>Quota numero ".sprintf("%05d", $row['QUO_ID_QUOTA'])." - " .$row['QUO_ID_EVENTO']." Campanha"
				."</option>";
				$pqTela->find('#cmbQuota')->append($cmbQuota);
			}
}

$sql = "SELECT * FROM sce_banco ORDER BY BAN_NOME ASC";
$exec =  mysql_query($sql, $conn) or die(mysql_error());
$row = mysql_num_rows($exec);
while ($row = mysql_fetch_assoc($exec))
{
	$cmbBanco = "<option value='".$row['BAN_ID']."'>".$row['BAN_NOME']."</option>";
	$pqTela->find('#cmbBanco')->append($cmbBanco);
	if ($row['BAN_ID'] == $Banco){$pqTela->find('#cmbBanco')->val($Banco);};
 }

echo $pqTela->html();

