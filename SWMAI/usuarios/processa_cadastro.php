<?php
session_start();
require_once('../geral/conexao.php');

// Garante que o script só seja acessado via método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario_cadastro_usuarios.php');
    exit();
}

// Coleta e sanitiza os dados principais
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = filter_input(INPUT_POST, 'senha');
$tipoPessoa = filter_input(INPUT_POST, 'tipoPessoa', FILTER_SANITIZE_SPECIAL_CHARS);

// Coleta e limpa dados com formatação
$telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
$cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj'] ?? '');
$dataNasc = $_POST['dataNasc'] ?? null;

// Validação dos campos obrigatórios
if (!$nome || !$email || !$telefone || !$senha || !$tipoPessoa) {
    $_SESSION['form_error'] = "Erro: Todos os campos principais são obrigatórios.";
    header('Location: formulario_cadastro_usuarios.php');
    exit();
}

// Validação específica para Pessoa Física e Jurídica
if ($tipoPessoa === 'fisica') {
    $tipoPessoaLabel = 'Física';
    // Se for física, CPF e Data de Nascimento são obrigatórios
    if (empty($cpf) || empty($dataNasc)) {
        $_SESSION['form_error'] = "Erro: CPF e Data de Nascimento são obrigatórios para Pessoa Física.";
        header('Location: formulario_cadastro_usuarios.php');
        exit();
    }
    // Define CNPJ como nulo para Pessoa Física
    $cnpj = null;
} else if ($tipoPessoa === 'juridica') {
    $tipoPessoaLabel = 'Jurídica';
    // Se for jurídica, CNPJ é obrigatório
    if (empty($cnpj)) {
        $_SESSION['form_error'] = "Erro: CNPJ é obrigatório para Pessoa Jurídica.";
        header('Location: formulario_cadastro_usuarios.php');
        exit();
    }
    // Define CPF e Data de Nascimento como nulos para Pessoa Jurídica
    $cpf = null;
    $dataNasc = null;
} else {
    // Caso o tipo de pessoa seja inválido
    $_SESSION['form_error'] = "Erro: Tipo de pessoa inválido.";
    header('Location: formulario_cadastro_usuarios.php');
    exit();
}

// Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserção no Banco de Dados
try {
    $sql = 'INSERT INTO Usuario (Nome, Email, Senha, Telefone, TipoPessoa, DataNasc, CPF, CNPJ, NivelAcesso) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $email, $senha_hash, $telefone, $tipoPessoaLabel, $dataNasc, $cpf, $cnpj, 1]);

    $_SESSION['login_success'] = "Cadastro realizado com sucesso! Faça o login para continuar.";
    header('Location: formulario_login.php');
    exit();
} catch (Exception $e) {
    if (str_contains($e->getMessage(), '1062')) {
        $_SESSION['form_error'] = "Erro: O e-mail ou documento informado já está cadastrado.";
    } else {
        $_SESSION['form_error'] = "Ocorreu um erro ao processar o cadastro. Tente novamente.";
        error_log("Erro no cadastro de usuário: " . $e->getMessage());
    }

    header('Location: formulario_cadastro_usuarios.php');
    exit();
}
