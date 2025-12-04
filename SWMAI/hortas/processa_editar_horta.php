<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
verificarAcesso(1);

require_once('../geral/conexao.php');

// --- VERIFICAÇÕES DE SEGURANÇA ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral/index.php');
    exit();
}

if (!autenticado()) {
    header('Location: ../usuarios/formulario_login.php');
    exit();
}

// --- COLETA E LIMPEZA DOS DADOS ---

$id_horta = filter_input(INPUT_POST, 'id_horta', FILTER_SANITIZE_NUMBER_INT);
$id_usuario_logado = id_usuario();
$nomeHorta = trim(filter_input(INPUT_POST, 'nomeHorta', FILTER_SANITIZE_SPECIAL_CHARS));
$localizacao = trim(filter_input(INPUT_POST, 'localizacao', FILTER_SANITIZE_SPECIAL_CHARS));


// --- ALTERAÇÃO INICIA AQUI ---

// 1. Pega os valores dos dois campos relacionados ao tipo de plantio.
// --- ALTERAÇÃO CORRIGIDA INICIA AQUI ---

// 1. Pega os valores dos dois campos relacionados ao tipo de plantio.
$tipoPlantioSelect = filter_input(INPUT_POST, 'tipoPlantio', FILTER_SANITIZE_SPECIAL_CHARS);
$tipoPlantioOutro = trim(filter_input(INPUT_POST, 'tipoPlantioOutro', FILTER_SANITIZE_SPECIAL_CHARS));

// 2. Decide qual valor usar.
// O valor do campo de texto só é usado se a opção 'Outro' for selecionada.
// Caso contrário, usamos o valor selecionado no select.
if ($tipoPlantioSelect === 'Outro' && !empty($tipoPlantioOutro)) {
    $tipoPlantioFinal = $tipoPlantioOutro;
} else {
    $tipoPlantioFinal = $tipoPlantioSelect;
}

// --- ALTERAÇÃO TERMINA AQUI ---

// --- ALTERAÇÃO TERMINA AQUI ---


if (!$id_horta || !$nomeHorta || !$localizacao || !$tipoPlantioFinal) {
    // Redireciona de volta para a página de edição com erro
    // Esta validação agora funciona corretamente. Se 'outro' for escolhido e o campo de texto ficar em branco,
    // $tipoPlantioFinal será vazio e a condição será verdadeira.
    $_SESSION['cultura_error_edit'] = "Erro: Todos os campos são obrigatórios.";
    header('Location: formulario_config_horta.php?id=' . $id_horta);
    exit();
}

// --- ATUALIZAÇÃO SEGURA NO BANCO DE DADOS ---
try {
    // 1. VERIFICAÇÃO DE PROPRIEDADE: Confirma se a horta pertence ao usuário logado
    // CONSULTA SQL CORRIGIDA PARA MYSQL
    $sql_check = 'SELECT ID_Horta FROM Horta WHERE ID_Horta = ? AND fk_Usuario = ?';
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id_horta, $id_usuario_logado]);

    if ($stmt_check->rowCount() === 0) {
        // Se a horta não pertence ao usuário, nega a operação
        $_SESSION['cultura_error'] = "Operação não permitida.";
        header('Location: ../hortas/minhas_culturas.php');
        exit();
    }

    // 2. Se a verificação passou, executa o UPDATE
    // CONSULTA SQL CORRIGIDA PARA MYSQL
    $sql_update = 'UPDATE Horta SET NomeHorta = ?, Localizacao = ?, TipoDoPlantio = ? 
                   WHERE ID_Horta = ? AND fk_Usuario = ?';

    $stmt_update = $conn->prepare($sql_update);
    // A variável $tipoPlantioFinal agora contém o valor correto (seja do select ou do campo de texto)
    $stmt_update->execute([$nomeHorta, $localizacao, $tipoPlantioFinal, $id_horta, $id_usuario_logado]);

    // Define mensagem de sucesso e redireciona
    $_SESSION['cultura_success'] = "Horta '" . htmlspecialchars($nomeHorta) . "' atualizada com sucesso!";
    header('Location: ../hortas/minhas_culturas.php');
    exit();
} catch (Exception $e) {
    $_SESSION['cultura_error_edit'] = "Ocorreu um erro ao atualizar a horta. Tente novamente.";
    error_log("Erro na edição de horta: " . $e->getMessage());
    header('Location: formulario_config_horta.php?id=' . $id_horta);
    exit();
}
