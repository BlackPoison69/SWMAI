<?php
// Arquivo: SWMAI/sensores/processa_teste_conexao.php

session_start();
require_once('../geral/conexao.php');

// Pega o ID do sensor e da horta para o teste
$id_sensor_recebido = filter_input(INPUT_GET, 'id_sensor', FILTER_SANITIZE_SPECIAL_CHARS);
$fk_horta_recebida = filter_input(INPUT_GET, 'fk_horta', FILTER_SANITIZE_NUMBER_INT);

// Valida os parâmetros
if (empty($id_sensor_recebido) || empty($fk_horta_recebida)) {
    http_response_code(400); // Bad Request
    echo "ERRO: O ID do sensor e o ID da horta são obrigatórios para o teste.";
    exit();
}

try {
    // Insere o registro de teste na tabela 'Registro'
    $stmt_insert = $conn->prepare("
        INSERT INTO Registro (ID_Sensor, MomentoCaptura, UmidAr, TempAr, UmidSolo, Chuva, Luminosidade, fk_Horta, SaudePlanta)
        VALUES (?, NOW(), 0, 0.0, 0, 0, 0, ?, 0.0)
    ");
    $stmt_insert->execute([
        $id_sensor_recebido,
        $fk_horta_recebida
    ]);
    
    http_response_code(200); // OK
    echo "TESTE_CONEXAO: Conexão bem-sucedida e registro de teste salvo.";
    exit();

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("Erro no registro de teste de conexao: " . $e->getMessage());
    echo "TESTE_CONEXAO: Erro ao salvar o registro de teste.";
    exit();
}
?>