<?php
session_start();
require_once('../usuarios/logica_autenticacao.php');
verificarAcesso(1);

require_once('../geral/conexao.php');

// --- VERIFICAÇÕES DE SEGURANÇA ---

// 1. Garante que o usuário está logado
if (!autenticado()) {
    header('Location: ../usuarios/formulario_login.php');
    exit();
}

// 2. Coleta e valida o ID da horta e o ID do usuário logado
$id_horta_para_excluir = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$id_usuario_logado = id_usuario();

if (!$id_horta_para_excluir) {
    // Define uma mensagem genérica de erro e redireciona
    $_SESSION['cultura_error'] = "ID de horta inválido.";
    header('Location: ../geral/minhas_culturas.php');
    exit();
}

try {
    // --- LÓGICA DE PERMISSÃO (O PONTO-CHAVE) ---

    // 3. Busca o ID do proprietário da horta no banco de dados
    $sql_check = 'SELECT fk_Usuario FROM Horta WHERE ID_Horta = ?';
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id_horta_para_excluir]);
    $horta = $stmt_check->fetch();

    $id_dono_da_horta = $horta['fk_Usuario'] ?? null;

    // 4. Verifica se o usuário tem permissão para excluir
    // A exclusão é permitida se:
    // a) O usuário logado é o dono da horta OU b) o usuário logado é um admin.
    if (($id_usuario_logado == $id_dono_da_horta) || admin()) {

        // --- SE A PERMISSÃO FOI CONCEDIDA, INICIA A EXCLUSÃO ---
        $conn->beginTransaction();

        try {
            // 5. Excluir todos os Registros associados à Horta
            $sql_delete_registros = 'DELETE FROM Registro WHERE fk_Horta = ?';
            $stmt_registros = $conn->prepare($sql_delete_registros);
            $stmt_registros->execute([$id_horta_para_excluir]);

            // 6. Excluir a Horta
            $sql_delete_horta = 'DELETE FROM Horta WHERE ID_Horta = ?';
            $stmt_horta = $conn->prepare($sql_delete_horta);
            $stmt_horta->execute([$id_horta_para_excluir]);

            $conn->commit();
            $_SESSION['cultura_success'] = 'Horta excluída com sucesso!';
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['cultura_error'] = 'Erro ao excluir a horta. Nenhuma alteração foi feita.';
            error_log("Erro ao excluir horta: " . $e->getMessage());
        }
    } else {
        // Se o usuário não é o dono nem admin, a operação não é permitida
        $_SESSION['cultura_error'] = "Operação não permitida.";
    }
} catch (Exception $e) {
    $_SESSION['cultura_error'] = "Ocorreu um erro ao verificar as permissões da horta.";
    error_log("Erro na verificação de permissão de exclusão: " . $e->getMessage());
}

// Redireciona de volta para a página de onde o usuário veio
// (Pode ser a listagem de admin ou a página de culturas do usuário)
header('Location: ../geral/minhas_culturas.php');
exit();
