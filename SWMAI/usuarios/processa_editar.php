<?php
session_start();
require_once('logica-autenticacao.php');
require_once('../geral/conexao.php');

// --- VERIFICAÇÕES DE SEGURANÇA ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral/index.php');
    exit();
}
if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Faça o login para editar seu perfil.";
    header('Location: formulario_login.php');
    exit();
}

$id_form = $_POST['id_usuario'] ?? null;
$id_sessao = id_usuario();

if ($id_form != $id_sessao) {
    $_SESSION['perfil_error'] = "Erro de permissão. Você não pode editar este perfil.";
    header('Location: perfil.php');
    exit();
}

// --- COLETA E LIMPEZA DOS DADOS ---
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
$cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj'] ?? '');
$dataNasc = filter_input(INPUT_POST, 'dataNasc');

if (!$nome || !$email || !$telefone) {
    $_SESSION['form_edit_error'] = "Erro: Nome, e-mail e telefone são obrigatórios.";
    header('Location: formulario_editar_perfil.php');
    exit();
}

// --- ATUALIZAÇÃO NO BANCO DE DADOS ---
try {
    // Busca o TipoPessoa do usuário (SQL CORRIGIDO)
    $stmt_tipo = $conn->prepare('SELECT TipoPessoa FROM Usuario WHERE ID_Usuario = ?');
    $stmt_tipo->execute([$id_sessao]);
    $usuario = $stmt_tipo->fetch();

    if ($usuario && $usuario['TipoPessoa'] === 'Física') {
        // Se for Pessoa Física (SQL CORRIGIDO)
        $sql = 'UPDATE Usuario SET Nome = ?, Email = ?, Telefone = ?, DataNasc = ?, CPF = ? WHERE ID_Usuario = ?';
        $params = [$nome, $email, $telefone, $dataNasc, $cpf, $id_sessao];
    } else {
        // Se for Pessoa Jurídica (SQL CORRIGIDO)
        $sql = 'UPDATE Usuario SET Nome = ?, Email = ?, Telefone = ?, CNPJ = ? WHERE ID_Usuario = ?';
        $params = [$nome, $email, $telefone, $cnpj, $id_sessao];
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $_SESSION['nome_usuario'] = $nome;
    $_SESSION['perfil_success'] = "Perfil atualizado com sucesso!";
    header('Location: perfil.php');
    exit();

} catch (Exception $e) {
    // CÓDIGO DE ERRO CORRIGIDO PARA MYSQL (1062)
    if (str_contains($e->getMessage(), '1062')) { 
        $_SESSION['form_edit_error'] = "Erro: O e-mail informado já está em uso por outra conta.";
    } else {
        $_SESSION['form_edit_error'] = "Ocorreu um erro ao atualizar o perfil. Tente novamente.";
        error_log("Erro na edição de perfil: " . $e->getMessage());
    }

    header('Location: formulario_editar_perfil.php?id=' . $id_form); // Adicionado ID no redirect
    exit();
}