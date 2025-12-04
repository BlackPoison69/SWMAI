<?php
// 1. Lê as configurações do arquivo .ini
$conf = parse_ini_file("../config/configNET.ini");

// 2. Valida se todas as chaves necessárias existem
if (!$conf || !isset($conf['driver'], $conf['database'], $conf['server'], $conf['port'], $conf['user'], $conf['password'])) {
    // Para a execução e mostra um erro genérico para o usuário
    die("Erro: As configurações de conexão com o banco de dados estão incompletas ou ausentes.");
}

// 3. Monta a DSN (Data Source Name) apenas com as informações de localização
$dsn = $conf["driver"] .
    ":host=" . $conf["server"] .
    ";port=" . $conf["port"] .
    ";dbname=" . $conf["database"];

// 4. Armazena as credenciais em variáveis separadas
$user = $conf["user"];
$password = $conf["password"];

try {
    // 5. Tenta a conexão usando o formato padrão do PDO: (DSN, usuário, senha)
    $conn = new PDO($dsn, $user, $password);

    // Define o modo de erro do PDO para lançar exceções, o que é uma boa prática
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Log de sucesso (caso a opção debug esteja ativada no .ini)
    if (isset($conf["debug"]) && $conf["debug"] == "true") {
        error_log("Conexão com o banco de dados realizada com sucesso.");
    }
} catch (Exception $e) {
    // Em caso de falha, registra o erro detalhado no log do servidor
    error_log("Erro ao se conectar ao banco de dados: " . $e->getMessage());

    echo "<p>Erro ao se conectar ao banco de dados. Tente novamente mais tarde.</p>";
    // Interrompe a execução do script para que a página não tente carregar sem banco de dados
    exit();
}
