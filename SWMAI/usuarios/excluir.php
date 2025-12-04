<?php
session_start();
require_once('logica-autenticacao.php');
verificarAcesso(1);

require_once('../geral/conexao.php');

// Garante que o usuário está logado
if (!autenticado()) {
    header('Location: formulario_login.php');
    exit();
}

// Pega o ID do usuário que está logado e deseja excluir a própria conta
$id_usuario_para_excluir = id_usuario();

if (!$id_usuario_para_excluir) {
    $_SESSION['config_error'] = "Erro: Não foi possível identificar o usuário a ser excluído.";
    header('Location: configuracoes.php');
    exit();
}

// Inicia uma transação. Ou tudo funciona, ou nada é alterado.
$conn->beginTransaction();

try {
    // 1. Excluir todos os Registros associados às Hortas do usuário
    // Usamos uma subconsulta para encontrar todas as hortas que pertencem ao usuário
    // SQL CORRIGIDO PARA MYSQL
    $sql_delete_registros = 'DELETE FROM Registro WHERE fk_Horta IN (SELECT ID_Horta FROM Horta WHERE fk_Usuario = ?)';
    $stmt_registros = $conn->prepare($sql_delete_registros);
    $stmt_registros->execute([$id_usuario_para_excluir]);

    // SQL CORRIGIDO PARA MYSQL
    $sql_delete_hortas = 'DELETE FROM Horta WHERE fk_Usuario = ?';
    $stmt_hortas = $conn->prepare($sql_delete_hortas);
    $stmt_hortas->execute([$id_usuario_para_excluir]);

    // SQL CORRIGIDO PARA MYSQL
    $sql_delete_usuario = 'DELETE FROM Usuario WHERE ID_Usuario = ?';
    $stmt_usuario = $conn->prepare($sql_delete_usuario);
    $stmt_usuario->execute([$id_usuario_para_excluir]);

    // Se todas as exclusões foram bem-sucedidas, confirma as alterações no banco
    $conn->commit();

    // Define uma mensagem de sucesso para a página de login
    $_SESSION['login_success'] = "Sua conta foi excluída com sucesso.";

    // Redireciona para o script de logout para encerrar a sessão
    header('Location: ../usuarios/sair.php');
    exit();
} catch (Exception $e) {
    // Se qualquer uma das operações falhar, desfaz tudo
    $conn->rollBack();

    // Define uma mensagem de erro e redireciona de volta para as configurações
    $_SESSION['config_error'] = "Ocorreu um erro ao tentar excluir sua conta. Nenhuma alteração foi feita.";
    error_log("Erro ao excluir conta: " . $e->getMessage());
    header('Location: configuracoes.php');
    exit();
}
