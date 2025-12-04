<?php
// Define o tipo de conteúdo como texto plano para uma resposta limpa
header('Content-Type: text/plain');

// Pega o valor do parâmetro "dado" enviado via GET.
// Se o parâmetro não existir, a variável $dado_recebido será nula (null).
$dado_recebido = $_GET['dado'] ?? null;

// Verifica se algum dado foi recebido
if ($dado_recebido !== null) {
    
    // Converte o dado para um formato seguro para exibição
    $dado_seguro = htmlspecialchars($dado_recebido);

    echo "=== Teste de Conexão ESP32 -> Servidor ===\n\n";

    // Verifica o valor do dado recebido e exibe uma resposta correspondente
    if ($dado_seguro == '1') {
        http_response_code(200); // Código de Sucesso
        echo "STATUS: SUCESSO\n";
        echo "MENSAGEM: Comando '1' recebido. Ação (ex: ligar irrigação) seria executada aqui.";

    } elseif ($dado_seguro == '0') {
        http_response_code(200); // Código de Sucesso
        echo "STATUS: SUCESSO\n";
        echo "MENSAGEM: Comando '0' recebido. Ação (ex: desligar irrigação) seria executada aqui.";

    } else {
        http_response_code(400); // Código de Erro (Requisição Inválida)
        echo "STATUS: ERRO\n";
        echo "MENSAGEM: Dado recebido ('" . $dado_seguro . "'), mas o comando não é reconhecido.";
    }

} else {
    // Se nenhum parâmetro "dado" foi enviado na URL
    http_response_code(400); // Código de Erro (Requisição Inválida)
    echo "STATUS: AGUARDANDO DADOS\n";
    echo "MENSAGEM: Nenhum parâmetro 'dado' foi recebido.\n\n";
    echo "COMO USAR: Acesse esta página adicionando '?dado=VALOR' ao final da URL.\n";
    echo "Exemplo: .../teste_esp.php?dado=1";
}