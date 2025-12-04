<?php

/**
 * Lembrete dos Níveis de Acesso:
 * Nível 1: Usuário Padrão
 * Nível 2: Admin
 * Nível 3: Desenvolvedor (Super Admin)
 */

// ========================================================================
// NOVA FUNÇÃO OTIMIZADA PARA CONTROLE DE ACESSO
// ========================================================================

/**
 * Verifica se o usuário tem o nível de acesso mínimo necessário para ver a página.
 * Se não tiver, redireciona o usuário para uma página segura.
 *
 * @param int $nivelNecessario O nível mínimo de acesso requerido (1, 2 ou 3).
 */
function verificarAcesso($nivelNecessario)
{
    // Passo 1: Verifica se o usuário está logado.
    if (!isset($_SESSION["email_usuario"])) {
        $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login.";
        header('Location: ../usuarios/formulario_login.php');
        exit();
    }

    // Passo 2: Pega o nível de acesso do usuário.
    $nivelUsuarioLogado = $_SESSION["nivel_acesso"] ?? 0;

    // Passo 3: Compara o nível do usuário com o nível necessário.
    if ($nivelUsuarioLogado < $nivelNecessario) {
        // Define a mensagem de erro na sessão
        $_SESSION['index_message'] = ['type' => 'danger', 'text' => 'Acesso negado. Você não tem permissão para acessar esta página.'];

        // A MUDANÇA ESTÁ AQUI: Adiciona #aviso ao final da URL
        header('Location: ../geral/index.php#aviso');
        exit();
    }
}
function autenticado()
{
    // Verifica a existência da sessão 'email_usuario', que é criada no login.
    return isset($_SESSION["email_usuario"]);
}


function admin()
{
    // Verifica se a sessão 'nivel_acesso' existe E se o seu valor é 2 ou maior.
    return isset($_SESSION["nivel_acesso"]) && $_SESSION["nivel_acesso"] >= 2;
}


function desenvolvedor() // Renomeado para maior clareza
{
    // Verifica se a sessão 'nivel_acesso' existe E se o seu valor é exatamente 3.
    return isset($_SESSION["nivel_acesso"]) && $_SESSION["nivel_acesso"] == 3;
}


function nome_usuario()
{
    if (isset($_SESSION["nome_usuario"])) {
        // Pega o nome completo da sessão
        $nomeCompleto = $_SESSION["nome_usuario"];

        // Divide o nome em partes usando o espaço como separador
        $partesNome = explode(' ', $nomeCompleto);

        // Retorna apenas a primeira parte (o primeiro nome)
        return $partesNome[0];
    }
    return "Visitante";
}


function email_usuario()
{
    if (isset($_SESSION["email_usuario"])) {
        return $_SESSION["email_usuario"];
    }
    return null;
}


function id_usuario()
{
    if (isset($_SESSION["id_usuario"])) {
        return $_SESSION["id_usuario"];
    }
    return null;
}


function redireciona($pagina = "index.php")
{
    header("Location: " . $pagina);
    exit(); // É crucial chamar exit() após um redirecionamento.
}
function formatarCNPJ($cnpj)
{
    $cnpj_limpo = preg_replace('/[^0-9]/', '', $cnpj);

    if (strlen($cnpj_limpo) != 14) {
        return $cnpj; // Retorna o original se não for um CNPJ válido
    }

    $parte1 = substr($cnpj_limpo, 0, 2);
    $parte2 = substr($cnpj_limpo, 2, 3);
    $parte3 = substr($cnpj_limpo, 5, 3);
    $parte4 = substr($cnpj_limpo, 8, 4);
    $parte5 = substr($cnpj_limpo, 12, 2);

    return "$parte1.$parte2.$parte3/$parte4-$parte5";
}
function formatarTelefone($telefone)
{
    $tel_limpo = preg_replace('/[^0-9]/', '', $telefone);
    $tamanho = strlen($tel_limpo);

    if ($tamanho == 11) { // Celular com 9º dígito
        $ddd = substr($tel_limpo, 0, 2);
        $parte1 = substr($tel_limpo, 2, 5);
        $parte2 = substr($tel_limpo, 7, 4);
        return "($ddd) $parte1-$parte2";
    } elseif ($tamanho == 10) { // Fixo ou celular sem 9º dígito
        $ddd = substr($tel_limpo, 0, 2);
        $parte1 = substr($tel_limpo, 2, 4);
        $parte2 = substr($tel_limpo, 6, 4);
        return "($ddd) $parte1-$parte2";
    }

    return $telefone; // Retorna o original se o tamanho for inválido
}
function formatarCPF($cpf)
{
    // 1. Limpa a string, removendo tudo que não for número.
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

    // 2. Verifica se o CPF limpo tem 11 dígitos.
    if (strlen($cpf_limpo) != 11) {
        return $cpf; // Se não tiver, retorna o valor original sem formatar.
    }

    // 3. Corta a string em pedaços usando substr().
    $bloco1 = substr($cpf_limpo, 0, 3);
    $bloco2 = substr($cpf_limpo, 3, 3);
    $bloco3 = substr($cpf_limpo, 6, 3);
    $bloco4 = substr($cpf_limpo, 9, 2);

    // 4. Junta os pedaços com os pontos e o traço e retorna o resultado.
    return "$bloco1.$bloco2.$bloco3-$bloco4";
}
