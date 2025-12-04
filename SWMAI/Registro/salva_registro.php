<?php
// Arquivo: SWMAI/sensores/salva_registro.php
session_start();
require_once('../geral/conexao.php');

// A ÚNICA responsabilidade deste script é processar o teste de conexão.
// Ele verifica se a requisição tem o parâmetro 'sucesso' com o valor 'sim'.
if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'sim') {

    // Pega os dados do sensor para o teste
    $id_sensor_recebido = filter_input(INPUT_GET, 'id_sensor', FILTER_SANITIZE_SPECIAL_CHARS);
    $temp_ar_recebido = filter_input(INPUT_GET, 'TempAr', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $umid_ar_recebido = filter_input(INPUT_GET, 'UmidAr', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // ID da horta a ser atribuída para o teste.
    $fk_horta_teste = 7;

    // A requisição só é válida se tiver o ID do sensor.
    if (empty($id_sensor_recebido)) {
        http_response_code(400); // Bad Request
        echo "ERRO: O ID do sensor e obrigatorio para o teste.";
        exit();
    }

    try {
        // Insere o registro de teste na tabela 'Registro'.
        // Agora, os valores de temperatura e umidade do ar serão os enviados pelo ESP32.
        $stmt_insert = $conn->prepare("
            INSERT INTO Registro (ID_Sensor, MomentoCaptura, UmidAr, TempAr, UmidSolo, Chuva, Luminosidade, fk_Horta, SaudePlanta)
            VALUES (?, NOW(), ?, ?, 0, 0, 0, ?, 0.0)
        ");
        $stmt_insert->execute([
            $id_sensor_recebido,
            $umid_ar_recebido,
            $temp_ar_recebido,
            $fk_horta_teste
        ]);

        http_response_code(200); // OK
        echo "SUCESSO: Conexão e inserção no banco de dados bem-sucedidas. Registro de teste criado com dados de temperatura e umidade do ar.";
        exit();
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        error_log("Erro no registro de teste de conexao: " . $e->getMessage());
        echo "ERRO: Falha ao salvar o registro de teste no banco de dados.";
        exit();
    }
} else {
    // Se a URL não tiver o parâmetro de sucesso, significa que a requisição é inválida.
    http_response_code(400); // Bad Request
    echo "ERRO: Requisicao invalida. O parametro 'sucesso' e obrigatorio e deve ser 'sim' para o teste de conexao.";
    exit();
}
