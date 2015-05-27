<?php

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
	  "SELECT F.chapa, 
       F.nome, 
       P.rua, 
       P.numero, 
       P.complemento, 
       P.bairro, 
       P.telefone1, 
       P.cep, 
       P.cidade, 
       P.estado, 
       P.estadocivil, 
       P.sexo, 
       P.dtnascimento, 
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
       ''  AS CATEGORIA, 
       P.email, 
       'S' AS RECEBE_EMAIL, 
       F.codfuncao, 
       P.grauinstrucao, 
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
       AND F.CHAPA = '0600000086'
ORDER BY F.nome ASC";
} else {
	$consulta = 'Vazio';
}

$realizar_consulta = mssql_query($consulta);

while($resultado = mssql_fetch_assoc($realizar_consulta)){
      $pessoa_temp = pessoa_temp(
            $resultado['chapa'],
            $resultado['nome'],
            $resultado['rua'],
            $resultado['numero'],
            $resultado['complemento'],
            $resultado['bairro'],
            $resultado['telefone1'],
            $resultado['cep'],
            $resultado['cidade'],
            $resultado['estado'],
            $resultado['estadocivil'],
            $resultado['sexo'],
            $resultado['dtnascimento'],
            $resultado['nacionalidade'],
            $resultado['cartidentidade'],
            $resultado['cpf'],
            $resultado['SITUACAO'],
            $resultado['CARTEIRA'],
            $resultado['SENHA'],
            $resultado['CRIPTOGRAFADA'],
            $resultado['CATEGORIA'],
            $resultado['email'],
            $resultado['RECEBE_EMAIL'],
            $resultado['codfuncao'],
            $resultado['grauinstrucao'],
            $resultado['ARQUIVO_FOTO'],
            $resultado['DEMAIS_INFO'],
            $resultado['DATA_VALIDADE'],
            $resultado['CODTIPO_EMPRESTIMO'],
            $resultado['LOGIN_LDAP'],
            $chave
            ); 
      echo $pessoa_temp;
}

?>

