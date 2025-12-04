<?php
session_start();
require_once('../geral/conexao.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario_login.php');
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    $_SESSION['login_error'] = "E-mail ou senha não fornecidos.";
    header('Location: formulario_login.php');
    exit();
}

try {
    // CONSULTA SQL CORRIGIDA PARA MYSQL
    $sql = 'SELECT ID_Usuario, Nome, Email, Senha, NivelAcesso FROM Usuario WHERE Email = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['Senha'])) {
        session_regenerate_id(true);

        // NOMES DAS COLUNAS CORRIGIDOS AO SALVAR NA SESSÃO
        $_SESSION['id_usuario'] = $usuario['ID_Usuario'];
        $_SESSION['nome_usuario'] = $usuario['Nome'];
        $_SESSION['email_usuario'] = $usuario['Email'];
        $_SESSION['nivel_acesso'] = $usuario['NivelAcesso'];

        header('Location: ../geral/dashboard.php');
        exit();
    } else {
        $_SESSION['login_error'] = "E-mail ou senha inválidos.";
        header('Location: formulario_login.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['login_error'] = "Ocorreu um erro no servidor. Tente novamente mais tarde.";
    error_log("Erro no login: " . $e->getMessage());
    header('Location: formulario_login.php');
    exit();
}
