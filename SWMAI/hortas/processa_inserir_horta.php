<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
verificarAcesso(1);

require_once('../geral/conexao.php');

// --- VERIFICAÇÕES DE SEGURANÇA ---

// 1. Garante que o script só seja acessado via método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral/index.php');
    exit();
}

if (!autenticado()) {
    header('Location: ../usuarios/formulario_login.php');
    exit();
}

// --- COLETA E TRATAMENTO DOS DADOS ---

$nomeHorta = filter_input(INPUT_POST, 'nomeHorta', FILTER_SANITIZE_SPECIAL_CHARS);
$localizacao = filter_input(INPUT_POST, 'localizacao', FILTER_SANITIZE_SPECIAL_CHARS);
$tipoPlantioSelecionado = filter_input(INPUT_POST, 'tipoPlantio', FILTER_SANITIZE_SPECIAL_CHARS);
$tipoPlantioOutro = filter_input(INPUT_POST, 'tipoPlantioOutro', FILTER_SANITIZE_SPECIAL_CHARS);
$id_usuario_logado = id_usuario();

if (!$nomeHorta || !$localizacao || !$tipoPlantioSelecionado || !$id_usuario_logado) {
    $_SESSION['horta_error'] = "Erro: Todos os campos são obrigatórios.";
    header('Location: formulario_cadastro_horta.php');
    exit();
}

$tipoPlantioFinal = $tipoPlantioSelecionado;
if ($tipoPlantioSelecionado === 'outro') {
    if (!empty($tipoPlantioOutro)) {
        $tipoPlantioFinal = $tipoPlantioOutro;
    } else {
        $_SESSION['horta_error'] = "Erro: Você selecionou 'Outro' mas não especificou o tipo de plantio.";
        header('Location: formulario_cadastro_horta.php');
        exit();
    }
}

try {
    // ===== CONSULTA SQL CORRIGIDA PARA MYSQL =====
    // Removemos as aspas duplas e usamos os nomes de tabela/coluna corretos.
    $sql = 'INSERT INTO Horta (NomeHorta, Localizacao, TipoDoPlantio, fk_Usuario) 
            VALUES (?, ?, ?, ?)';

    $stmt = $conn->prepare($sql);
    $stmt->execute([$nomeHorta, $localizacao, $tipoPlantioFinal, $id_usuario_logado]);

    $_SESSION['cultura_success'] = "Horta '" . htmlspecialchars($nomeHorta) . "' cadastrada com sucesso!";
    // Corrigido o redirect para a página correta
    header('Location: ../hortas/minhas_culturas.php');
    exit();
} catch (Exception $e) {
    $_SESSION['horta_error'] = "Ocorreu um erro ao cadastrar a horta. Tente novamente.";
    error_log("Erro no cadastro de horta: " . $e->getMessage());
    header('Location: formulario_cadastro_horta.php');
    exit();
}
