<?php

function tempoExecucao($start = null) {
    
    $mtime = microtime(); 
    $mtime = explode(' ',$mtime); 
    $mtime = $mtime[1] + $mtime[0]; 

    if ($start == null) {
        return $mtime;
    } else {
        return round($mtime - $start, 2);
    }
}

define('mTIME', tempoExecucao());

$protocolo = "http"; //Protocolo http ou https para acessar o web service
$server = "187.94.58.8/pergamum"; // inserir o caminho do web_service dentro da pasta da consulta web conforme exemplo
$chave = "a26f40cb690bf6220f7ba3b252dd53b1"; //Solicitar a chave para a equipe Pergamum.

require_once('./lib/nusoap.php'); //Fixo
//$submit = $_POST['botao']; //Validação da chamada das funções

$servidor = "187.94.60.100:1433";
$usuario = "usr_liceu_tisj01";
$banco = "CORPORE_UNICO";
$senha = "Un1s#4L";
//Não Alterar abaixo
$conmssql = mssql_connect($servidor,$usuario,$senha);
$db = mssql_select_db($banco, $conmssql);

$tipo = $_POST['tipo'];
$filial = $_POST['filial'];

if($_POST['tipo'] == 'colaboradores'){
    $consulta =
      "SELECT TOP 20 F.chapa, 
       F.nome, 
       P.rua, 
       P.numero, 
       P.complemento, 
       P.bairro, 
       P.telefone1, 
       P.cep, 
       P.cidade, 
       P.estado, 
       CASE 
         WHEN P.estadocivil = 'S' THEN '1' 
         WHEN P.estadocivil = 'C' THEN '2' 
         WHEN P.estadocivil = 'I' THEN '3' 
         WHEN P.estadocivil = 'V' THEN '4' 
         ELSE 5 
       END AS estadocivil, 
       P.sexo, 
       CONVERT(varchar, P.dtnascimento, 103) as dtnascimento,
       P.nacionalidade, 
       P.cartidentidade, 
       P.cpf, 
       CASE 
         WHEN F.codsituacao = 'A' THEN '1' 
         ELSE 0 
       END AS SITUACAO, 
       ''  AS CARTEIRA, 
       ''  AS SENHA, 
       'N' AS CRIPTOGRAFADA, 
       '8'  AS CATEGORIA, 
       P.email, 
       'S' AS RECEBE_EMAIL, 
       F.codfuncao, 
       CASE 
         WHEN P.grauinstrucao = '9' THEN '1' 
         WHEN P.grauinstrucao = '8' THEN '2' 
         ELSE 3
       END AS grauinstrucao,
       ''  AS ARQUIVO_FOTO, 
       ''  AS DEMAIS_INFO, 
       ''  AS DATA_VALIDADE, 
       1   AS CODTIPO_EMPRESTIMO, 
       ''  AS LOGIN_LDAP, 
       F.recmodifiedon 
FROM   pfunc F (nolock) 
       INNER JOIN ppessoa P (nolock) 
               ON F.codpessoa = P.codigo 
WHERE  F.codcoligada = 1 
       AND F.codfilial = '".$filial."'
       AND F.codsituacao <> 'D'
ORDER BY F.nome ASC";
} else {
    $consulta = "SELECT DISTINCT TOP 20 F.ra as chapa, 
                p.nome, 
                P.rua, 
                P.numero, 
                P.complemento, 
                P.bairro, 
                P.telefone1, 
                P.cep, 
                P.cidade, 
                P.estado, 
                CASE 
                  WHEN P.estadocivil = 'S' THEN '1' 
                  WHEN P.estadocivil = 'C' THEN '2' 
                  WHEN P.estadocivil = 'I' THEN '3' 
                  WHEN P.estadocivil = 'V' THEN '4' 
                  ELSE 5 
                END AS estadocivil, 
                P.sexo, 
                CONVERT(varchar, P.dtnascimento, 103) as dtnascimento, 
                P.nacionalidade, 
                P.cartidentidade, 
                P.cpf, 
                CASE 
                  WHEN S.codstatus IN( 151, 201, 101, 51, 1 ) THEN '1'
                  ELSE 0 
                END AS SITUACAO, 
                ''  AS CARTEIRA, 
                ''  AS SENHA, 
                'N' AS CRIPTOGRAFADA, 
                '1'  AS CATEGORIA, 
                P.email, 
                'S' AS RECEBE_EMAIL, 
                P.codprofissao, 
                CASE 
                  WHEN P.grauinstrucao = '9' THEN '1' 
                  WHEN P.grauinstrucao = '8' THEN '2' 
                  ELSE 3 
                END AS grauinstrucao, 
                ''  AS ARQUIVO_FOTO, 
                ''  AS DEMAIS_INFO, 
                ''  AS DATA_VALIDADE, 
                1   AS CODTIPO_EMPRESTIMO, 
                ''  AS LOGIN_LDAP, 
                F.recmodifiedon 
FROM   saluno F (nolock) 
       INNER JOIN ppessoa P (nolock) 
               ON F.codpessoa = P.codigo 
       INNER JOIN smatricpl S (nolock) 
               ON F.ra = S.ra 
WHERE  F.codcoligada = 1 
       AND S.codstatus IN( 151, 201, 101, 51, 1 ) 
       AND S.codfilial = '".$filial."'
ORDER BY p.nome asc";
}

$realizar_consulta = mssql_query($consulta);

while($resultado = mssql_fetch_assoc($realizar_consulta)){

$contador = $contador + 1;

$CodPessoa = trim($resultado['chapa']);
$NomePessoa = utf8_encode(trim($resultado['nome']));
$RuaPessoa = trim($resultado['rua']);
$NumEndPessoa = trim($resultado['numero']);
$AptoPessoa = '';
$BairroPessoa = trim($resultado['bairro']);
$TelefonePessoa = trim($resultado['telefone1']);
$CepPessoa = str_replace('-', '', trim($resultado['cep']));
$CidadePessoa = trim($resultado['cidade']);
$CodUf = trim($resultado['estado']);
$CodEstadoCivil = trim($resultado['estadocivil']);
$SexoPessoa = trim($resultado['sexo']);
$DataNascimentoPessoa = trim($resultado['dtnascimento']);
$CodNacionalidade = '10';
$RgPessoa = trim($resultado['cartidentidade']);
$CpfPessoa = trim($resultado['cpf']);
$SituacaoPessoa = trim($resultado['SITUACAO']);
$NumViaCarteira = trim($resultado['CARTEIRA']);
$SenhaPessoa = trim($resultado['SENHA']);
$SenhaCript = 'N';
$CodCategUsuario = trim($resultado['CATEGORIA']);
$Email = trim($resultado['email']);
$RecebeEmail = 'S';
$CodProfissao = trim($resultado['codfuncao']);
$CodEscolaridade = '';
$ArqFoto = trim($resultado['ARQUIVO_FOTO']);
$DemaisInformacoes = trim($resultado['DEMAIS_INFO']);
$DataValidade = trim($resultado['DATA_VALIDADE']);
$CodTipoEmprestimo = '1';
$LoginLdap = trim($resultado['LOGIN_LDAP']);

//print_r($resultado);

$pessoa_temp = pessoa_temp($CodPessoa,$NomePessoa,$RuaPessoa,$NumEndPessoa,$AptoPessoa,$BairroPessoa,$TelefonePessoa,$CepPessoa,$CidadePessoa,$CodUf,$CodEstadoCivil,$SexoPessoa,$DataNascimentoPessoa,$CodNacionalidade,$RgPessoa,$CpfPessoa,$SituacaoPessoa,$NumViaCarteira,$SenhaPessoa,$SenhaCript,$CodCategUsuario,$Email,$RecebeEmail,$CodProfissao,$CodEscolaridade,$ArqFoto,$DemaisInformacoes,$DataValidade,$CodTipoEmprestimo,$LoginLdap,$chave);
echo "$contador - $CodPessoa - $NomePessoa => $pessoa_temp<br>";
}


// FUNÇÕES --------------------------------------------------------------------------------

function pessoa_temp($CodPessoa,$NomePessoa,$RuaPessoa,$NumEndPessoa,$AptoPessoa,$BairroPessoa,$TelefonePessoa,$CepPessoa,$CidadePessoa,$CodUf,$CodEstadoCivil,$SexoPessoa,$DataNascimentoPessoa,$CodNacionalidade,$RgPessoa,$CpfPessoa,$SituacaoPessoa,$NumViaCarteira,$SenhaPessoa,$SenhaCript,$CodCategUsuario,$Email,$RecebeEmail,$CodProfissao,$CodEscolaridade,$ArqFoto,$DemaisInformacoes,$DataValidade,$CodTipoEmprestimo,$LoginLdap,$chave)
{
    global $protocolo,$server;	
      
    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_pessoa_temp', array( 'coluna1' => $CodPessoa,
                                                     'coluna2' => $NomePessoa,
                                                     'coluna3' => $RuaPessoa,
                                                     'coluna4' => $NumEndPessoa,
                                                     'coluna5' => $AptoPessoa,
                                                     'coluna6' => $BairroPessoa,
                                                     'coluna7' => $TelefonePessoa,
                                                     'coluna8' => $CepPessoa,
                                                     'coluna9' => $CidadePessoa,
                                                     'coluna10' => $CodUf,
                                                     'coluna11' => $CodEstadoCivil,
                                                     'coluna12' => $SexoPessoa,
                                                     'coluna13' => $DataNascimentoPessoa,
                                                     'coluna14' => $CodNacionalidade,
                                                     'coluna15' => $RgPessoa,
                                                     'coluna16' => $CpfPessoa,
                                                     'coluna17' => $SituacaoPessoa,
                                                     'coluna18' => $NumViaCarteira,
                                                     'coluna19' => $SenhaPessoa,
                                                     'coluna20' => $SenhaCript,
                                                     'coluna21' => $CodCategUsuario,
                                                     'coluna22' => $Email,
                                                     'coluna23' => $RecebeEmail,
                                                     'coluna24' => $CodProfissao,
                                                     'coluna25' => $CodEscolaridade,
                                                     'coluna26' => $ArqFoto,
                                                     'coluna27' => $DemaisInformacoes,
                                                     'coluna28' => $DataValidade,
                                                     'coluna29' => $CodTipoEmprestimo,
                                                     'coluna30' => $LoginLdap,
                                                     'coluna31' => $chave ) );

    return $result;

}

function excluir_pessoa($CodPessoaExcluir,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_pessoa', array( 'coluna1' => $CodPessoaExcluir,
                                                       'coluna2' => $chave  ) );

    return $result;

}

function pessoa_dep($CodPessoaDep,$CodDepartamentoDep,$CodAfastDepartamentoDep,$AnoVigenteDep,$PeriodoDep,$SerieDep,$TurmaDep,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_pessoa_dep', array( 'coluna1' => $CodPessoaDep,
                                                     'coluna2' => $CodDepartamentoDep,
                                                     'coluna3' => $CodAfastDepartamentoDep,
                                                     'coluna4' => $AnoVigenteDep,
                                                     'coluna5' => $PeriodoDep,
                                                     'coluna6' => $SerieDep,
                                                     'coluna7' => $TurmaDep,
                                                     'coluna8' => $chave ) );

    return $result;

}

function excluir_pessoa_dep($CodPessoaDep,$CodDepartamentoDep,$CodAfastDepartamentoDep,$AnoVigenteDep,$PeriodoDep,$SerieDep,$TurmaDep,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_pessoa_dep', array( 'coluna1' => $CodPessoaDep,
                                                            'coluna2' => $CodDepartamentoDep,
                                                            'coluna3' => $CodAfastDepartamentoDep,
                                                            'coluna4' => $AnoVigenteDep,
                                                            'coluna5' => $PeriodoDep,
                                                            'coluna6' => $SerieDep,
                                                            'coluna7' => $TurmaDep,
                                                            'coluna8' => $chave ) );

    return $result;

}

function departamento($CodDepart,$NomeDepart,$SiglaDepart,$CodPessoaResp,$CodDepartPai,$Ramal,$EmailDepart,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_departamento', array('coluna1' => $CodDepart,
                                                     'coluna2' => $NomeDepart,
                                                     'coluna3' => $SiglaDepart,
                                                     'coluna4' => $CodPessoaResp,
                                                     'coluna5' => $CodDepartPai,
                                                     'coluna6' => $Ramal,
                                                     'coluna7' => $EmailDepart,
                                                     'coluna8' => $chave ) );

    return $result;

}

function biblioteca_departamento($CodDepBib,$CodBiblioteca,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_departamento_bib', array('coluna1' => $CodDepBib,
                                                         'coluna2' => $CodBiblioteca,
                                                         'coluna3' => $chave ) );

    return $result;
}

function exclui_bib_dep($CodDepBib,$CodBiblioteca,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_dep_bib', array('coluna1' => $CodDepBib,
                                                        'coluna2' => $CodBiblioteca,
                                                        'coluna3' => $chave ) );

    return $result;
}

function excluir_departamento($CodDepartamentoExcluir,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_departamento', array( 'coluna1' => $CodDepartamentoExcluir,
                                                              'coluna2' => $chave  ) );

    return $result;

}


function departamento_turma($CodDepTurma,$AnoTurma,$PeriodoTurma,$SerieTurma,$TurmaDepTurma,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_turma', array('coluna1' => $CodDepTurma,
                                              'coluna2' => $AnoTurma,
                                              'coluna3' => $PeriodoTurma,
                                              'coluna4' => $SerieTurma,
                                              'coluna5' => $TurmaDepTurma,
                                              'coluna6' => $chave ) );

    return $result;

}

function excluir_departamento_turma($CodDepTurma,$AnoTurma,$PeriodoTurma,$SerieTurma,$TurmaDepTurma,$chave)
{

   global $protocolo,$server;

   $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
   $result = $client->call('ws_excluir_dep_turma', array('coluna1' => $CodDepTurma,
                                             'coluna2' => $AnoTurma,
                                             'coluna3' => $PeriodoTurma,
                                             'coluna4' => $SerieTurma,
                                             'coluna5' => $TurmaDepTurma,
                                             'coluna6' => $chave ) );

   return $result;

}

function disciplinas($CodDisciplina,$DescDisciplina,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_disciplinas', array('coluna1' => $CodDisciplina,
                                                    'coluna2' => $DescDisciplina,
                                                    'coluna3' => $chave ) );

    return $result;

}

function excluir_disciplina($CodDisciplina,$DescDisciplina,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_disciplina', array('coluna1' => $CodDisciplina,
                                                            'coluna2' => $DescDisciplina,
                                                            'coluna3' => $chave ) );

    return $result;

}

function exporta_multa($BibMulta,$CodPessoaMulta,$CategMulta,$FlagMulta,$num_titulo_exporta_multa,$valor_desconto_exporta_multa,$chave)
{

    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_exporta_multa', array('coluna1' => $BibMulta,
                                                       'coluna2' => $CodPessoaMulta,
                                                       'coluna3' => $CategMulta,
                                                       'coluna4' => $FlagMulta,
													   'coluna5' => $num_titulo_exporta_multa,
													   'coluna6' => $valor_desconto_exporta_multa,
                                                       'coluna7' => $chave ) );

    return $result;

}

function verifica_pessoa($CodPessoaVerifica,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_verifica_pessoa', array('coluna1' => $CodPessoaVerifica,
                                                        'coluna2' => $chave ) );

    return $result;

}

function consulta_plano_ensino($CodDepartoPlano,$AnoPlano,$SemestrePlano,$PeriodoPlano,$CodDisciplinaPlano,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_consulta_plano_ensino', array('coluna1' => $CodDepartoPlano,
                                                              'coluna2' => $AnoPlano,
                                                              'coluna3' => $SemestrePlano,
                                                              'coluna4' => $PeriodoPlano,
                                                              'coluna5' => $CodDisciplinaPlano,
                                                              'coluna6' => $chave ) );

    return $result;
}

function excluir_acervo_plano($CodDepartoPlanoDel,$AnoPlanoDel,$SemestrePlanoDel,$PeriodoPlanoDel,$CodDisciplinaPlanoDel,$CodAcervoPlanoDel,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_acervo_plano', array('coluna1' => $CodDepartoPlanoDel,
                                                              'coluna2' => $AnoPlanoDel,
                                                              'coluna3' => $SemestrePlanoDel,
                                                              'coluna4' => $PeriodoPlanoDel,
                                                              'coluna5' => $CodDisciplinaPlanoDel,
                                                              'coluna6' => $CodAcervoPlanoDel,
                                                              'coluna7' => $chave ) );

    return $result;
}

function perg_programa_disc($CodDepDisc,$AnoDisc,$SemestreDisc,$PeriodoDisc,$CodDisc,$NumAlunosDisc,$chave){
	
	global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_perg_programa_disc', array('coluna1' => $CodDepDisc,
                                                              'coluna2' => $AnoDisc,
                                                              'coluna3' => $SemestreDisc,
                                                              'coluna4' => $PeriodoDisc,
                                                              'coluna5' => $CodDisc,
    														  'coluna6' => $NumAlunosDisc, 	
                                                              'coluna7' => $chave ) );

    return $result;	
}


function importa_perg_programa($CodDepProg,$AnoProg,$SemestreProg,$PeriodoProg,$CodAuxProg,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_importa_perg_programa', array('coluna1' => $CodDepProg,
                                                              'coluna2' => $AnoProg,
                                                              'coluna3' => $SemestreProg,
                                                              'coluna4' => $PeriodoProg,
                                                              'coluna5' => $CodAuxProg,
                                                              'coluna6' => $chave ) );

    return $result;
}

function excluir_perg_programa($CodDepProg,$AnoProg,$SemestreProg,$PeriodoProg,$chave)
{
    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_excluir_perg_programa', array('coluna1' => $CodDepProg,
                                                              'coluna2' => $AnoProg,
                                                              'coluna3' => $SemestreProg,
                                                              'coluna4' => $PeriodoProg,
                                                              'coluna5' => $chave ) );

    return $result;
}

function importa_acesso($CodPessoaAcesso,$NomeLoginAcesso,$CodBibliotecaAcesso,$CodPerfilAcesso,$CodGrupoClassificacao,$CodIdiomaAcesso,$SenhaAcesso,$TipoAreaConhecAcesso,$chave)
{

    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_importa_acesso', array( 'coluna1' => $CodPessoaAcesso,
                                                        'coluna2' => $NomeLoginAcesso,
                                                        'coluna3' => $CodBibliotecaAcesso,
                                                        'coluna4' => $CodPerfilAcesso,
                                                        'coluna5' => $CodGrupoClassificacao,
                                                        'coluna6' => $CodIdiomaAcesso,
                                                        'coluna7' => $SenhaAcesso,
                                                        'coluna8' => $TipoAreaConhecAcesso,
                                                        'coluna9' => $chave
                                                         ) );

    return $result;
    
}

function importar_kosmos($codTabela, $descTabela, $codigo, $descPt, $descCa, $descEs, $descFr, $descEn, $descIt, $infoExtra, $codigoRelacao1, $codigoRelacao2, $fechaExp, $chave){

    global $protocolo,$server;

    $client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
    $result = $client->call('ws_importar_kosmos_presencia', array( 'coluna1' => $codTabela,
                                                     'coluna2' => $descTabela,
                                                     'coluna3' => $codigo,
                                                     'coluna4' => $descPt,
                                                     'coluna5' => $descCa,
                                                     'coluna6' => $descEs,
                                                     'coluna7' => $descFr,
                                                     'coluna8' => $descEn,
                                                     'coluna9' => $descIt,
                                                     'coluna10' => $infoExtra,
                                                     'coluna11' => $codigoRelacao1,
                                                     'coluna12' => $codigoRelacao2,
                                                     'coluna13' => $fechaExp,
                                                     'coluna30' => $chave ) );

    return $result;


}

function importar_pessoa_vinculada($cod_pessoa_vinculo_perga_pes_vinc,$cod_pessoa_principal_perga_pes_vinc,$cod_tipo_responsavel_perga_pes_vinc,$chave){
	
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_importa_perga_pes_vinculada', array( 'coluna1' => $cod_pessoa_vinculo_perga_pes_vinc,
																	'coluna2' => $cod_pessoa_principal_perga_pes_vinc,
																	'coluna3' => $cod_tipo_responsavel_perga_pes_vinc,                                                     
																	'coluna4' => $chave ));
	
	return $result;
}

function importa_lista_tabelas($tabela_importa_lista_tabelas,$somente_cod_descricao_importa_lista_tabelas,$chave){
	
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_importa_lista_tabelas', array( 'coluna1' => $tabela_importa_lista_tabelas,
																'coluna2' => $somente_cod_descricao_importa_lista_tabelas,																	                                                     
																'coluna3' => $chave ));
	
	return $result;
}

function consulta_emp_deb($cod_pessoa_consulta_emp_deb,$chave){
	
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_consulta_emp_deb', array( 'coluna1' => $cod_pessoa_consulta_emp_deb,																																	                                                    
														  'coluna2' => $chave ));
	
	return $result;
}

function emp_estorno_multa($cod_pessoa_emp_estorno_multa,$cod_exemplar_emp_estorno_multa,$data_emprestimo_emp_estorno_multa,$chave){
	
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_emp_estorno_multa', array( 'coluna1' => $cod_pessoa_emp_estorno_multa,
														   'coluna2' => $cod_exemplar_emp_estorno_multa,
														   'coluna3' => $data_emprestimo_emp_estorno_multa,																																	                                                    
														   'coluna4' => $chave ));
	
	return $result;
}

function importa_pessoa_parcial($tipo_senha_importa_pessoa_parcial,$cod_pessoa_importa_pessoa_parcial,$chave){
	
	global $protocolo,$server;
	
	$campo_exemplo = array("rua_pessoa","e_mail","senha_pessoa","cidade_pessoa");
	$valor_exemplo = array("Rua de Teste WS","teste@webservice.com","web123","Webservice");
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_importa_pessoa_parcial', array( 'coluna1' => $tipo_senha_importa_pessoa_parcial,
															   	'coluna2' => $cod_pessoa_importa_pessoa_parcial,
																'coluna3' => $campo_exemplo,
																'coluna4' => $valor_exemplo,
															   	'coluna5' => $chave ));	
	
	return $result;
}

function importa_pessoa_parcial_string($tipo_senha_importa_pessoa_parcial_string,$cod_pessoa_importa_pessoa_parcial_string,$campo_importa_pessoa_parcial_string,$valor_importa_pessoa_parcial_string,$chave){
	global $protocolo,$server;		

	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_importa_pessoa_parcial_string', array( 'coluna1' => $tipo_senha_importa_pessoa_parcial_string,
																	   	'coluna2' => $cod_pessoa_importa_pessoa_parcial_string,
																		'coluna3' => $campo_importa_pessoa_parcial_string,
																		'coluna4' => $valor_importa_pessoa_parcial_string,
																	   	'coluna5' => $chave ));	
	
	return $result;
}

function ws_pessoa_campus($cod_pessoa_pessoa_campus,$cod_campus_pessoa_campus,$flag_apagar_pessoa_campus,$chave){
	
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_pessoa_campus', array( 'coluna1' => $cod_pessoa_pessoa_campus,
													   	'coluna2' => $cod_campus_pessoa_campus,
														'coluna3' => $flag_apagar_pessoa_campus,
													   	'coluna4' => $chave ));
	
	return $result;
}

function ws_importa_endereco($tabela_importa_endereco,$cod_endereco_importa_endereco,$cep_importa_endereco,$logradouro_importa_endereco,$numero_importa_endereco,$complemento_importa_endereco,$bairro_importa_endereco,$cidade_importa_endereco,$uf_importa_endereco,$pais_importa_endereco,$outras_inf_importa_endereco,$chave){
	
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_importa_endereco',array('coluna1' => $tabela_importa_endereco,
													    'coluna2' => $cod_endereco_importa_endereco,
													    'coluna3' => $cep_importa_endereco,
														'coluna4' => $logradouro_importa_endereco,
														'coluna5' => $numero_importa_endereco,
														'coluna6' => $complemento_importa_endereco,
														'coluna7' => $bairro_importa_endereco,
														'coluna8' => $cidade_importa_endereco,
														'coluna9' => $uf_importa_endereco,
														'coluna10' => $pais_importa_endereco,
														'coluna11' => $outras_inf_importa_endereco,
													    'coluna12' => $chave));
	
	return $result;
}

function ws_pessoa_matricula($matricula_pessoa_matricula,$numero_mestre_pessoa_matricula,$cod_pessoa_pessoa_matricula,$situacao_matricula_pessoa_matricula,$cod_categ_usuario_pessoa_matricula,$flag_apagar_pessoa_matricula,$chave){
	global $protocolo,$server;
	
	$client = new nusoap_client("$protocolo://$server/web_service/integracao_sever_ws.php?wsdl");
	$result = $client->call('ws_pessoa_matricula',array('coluna1' => $matricula_pessoa_matricula,
													    'coluna2' => $numero_mestre_pessoa_matricula,
													    'coluna3' => $cod_pessoa_pessoa_matricula,
														'coluna4' => $situacao_matricula_pessoa_matricula,
														'coluna5' => $cod_categ_usuario_pessoa_matricula,
														'coluna6' => $flag_apagar_pessoa_matricula,
													    'coluna7' => $chave));
	return $result;
}

//----------------------------------------------------------------------------------

$tempo = tempoExecucao(mTIME);

echo "<br>Tempo total: $tempo segundos";

?>

<script type="text/javascript">
    $(function(){
        NProgress.done();
        $('.est').show();
        $('.col').show();
    });
</script>