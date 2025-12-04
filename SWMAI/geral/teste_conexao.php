<?php
require_once('header.php');

// Ativa a exibição de todos os erros para um diagnóstico claro.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclui o cabeçalho do site, que contém o HTML inicial e o CSS.
// O arquivo 'header.php' contém o 'session_start()', então não é necessário chamar de novo
// se este arquivo 'teste_conexao.php' já estiver fazendo o 'require_once' do header.

// ----- INÍCIO DO TESTE DE CONEXÃO -----
echo '<main class="blocoPrincipal">';
echo '<div class="container mt-5">';
echo "<h1>Teste de Conexão com o Banco de Dados</h1>";

// O seu arquivo 'conexao.php' já tem um tratamento de erro que mostra uma mensagem genérica.
// Vamos tentar nos conectar e verificar o resultado.

$conn = null; // Garante que a variável começa como nula.

echo "<p>Tentando incluir 'conexao.php' e estabelecer a conexão...</p>";

try {
    // **IMPORTANTE**: Verifique se o caminho para 'conexao.php' está correto.
    // O seu arquivo 'header.php' está no mesmo diretório que este 'teste_conexao.php',
    // então o caminho para 'conexao.php' deve ser relativo a eles.
    require_once('conexao.php');

    if (isset($conn) && $conn instanceof PDO) {
        echo "<p style='color: green; font-size: 1.2rem; font-weight: bold;'>✅ SUCESSO!</p>";
        echo "<p>A conexão com o banco de dados Supabase foi estabelecida corretamente.</p>";

        $stmt = $conn->query("SELECT 1");
        if ($stmt) {
            echo "<p>Consulta de verificação executada com sucesso no banco.</p>";
        }
    } else {
        echo "<p style='color: red; font-size: 1.2rem; font-weight: bold;'>❌ FALHA!</p>";
        echo "<p>O arquivo 'conexao.php' foi incluído, mas a variável de conexão \$conn não foi criada corretamente.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 1.2rem; font-weight: bold;'>❌ ERRO CAPTURADO!</p>";
    echo "<p>Ocorreu um erro durante a tentativa de conexão: </p>";
    echo "<pre style='background-color: #f1f1f1; border: 1px solid #ccc; padding: 10px;'>" . $e->getMessage() . "</pre>";
}




echo '</div>';
echo '</main>';
// ----- FIM DO TESTE DE CONEXÃO -----

// Inclui o rodapé do site, que contém os scripts e fecha as tags HTML.
require_once('footer.php');
