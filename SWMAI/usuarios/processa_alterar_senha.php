<?php
session_start();
require_once('logica-autenticacao.php');
verificarAcesso(1);
require_once('../geral/conexao.php');

// --- VERIFICAÇÕES DE SEGURANÇA ---

// 1. Garante que o script só seja acessado via método POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral/index.php');
    exit();
}

// 2. Garante que o usuário está logado.
if (!autenticado()) {
    header('Location: formulario_login.php');
    exit();
}

// --- COLETA E VALIDAÇÃO DOS DADOS ---

$senhaAtual = $_POST['senhaAtual'] ?? '';
$novaSenha = $_POST['novaSenha'] ?? '';
$confirmaNovaSenha = $_POST['confirmaNovaSenha'] ?? '';

// 3. Verifica se todos os campos foram preenchidos.
if (empty($senhaAtual) || empty($novaSenha) || empty($confirmaNovaSenha)) {
    $_SESSION['config_error'] = "Todos os campos de senha são obrigatórios.";
    header('Location: configuracoes.php');
    exit();
}

// 4. Verifica se a nova senha e a confirmação são idênticas.
if ($novaSenha !== $confirmaNovaSenha) {
    $_SESSION['config_error'] = "A nova senha e a confirmação não coincidem.";
    header('Location: configuracoes.php');
    exit();
}

// --- LÓGICA DE ALTERAÇÃO DA SENHA ---

try {
    $id_logado = id_usuario();

    $id_logado = id_usuario();

    // SQL CORRIGIDO PARA MYSQL
    $sql = 'SELECT Senha FROM Usuario WHERE ID_Usuario = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_logado]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senhaAtual, $usuario['Senha'])) {
        $nova_senha_hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // SQL CORRIGIDO PARA MYSQL
        $sqlUpdate = 'UPDATE Usuario SET Senha = ? WHERE ID_Usuario = ?';
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->execute([$nova_senha_hash, $id_logado]);

        $_SESSION['config_success'] = "Senha alterada com sucesso!";
        header('Location: configuracoes.php');
        exit();
    } else {

        $_SESSION['config_error'] = "A senha atual informada está incorreta.";
        header('Location: configuracoes.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['config_error'] = "Ocorreu um erro no servidor. Tente novamente mais tarde.";
    error_log("Erro ao alterar senha: " . $e->getMessage());
    header('Location: configuracoes.php');
    exit();
}
