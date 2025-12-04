<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
require_once('../geral/conexao.php');

// --- LÓGICA DE VISUALIZAÇÃO CORRIGIDA ---
// 1. Usa a função admin() para verificar se o usuário tem nível 2 ou superior.
if (!admin()) {
    // 2. Se não for, define uma mensagem de erro e redireciona para o dashboard.
    $_SESSION['dashboard_error'] = "Acesso negado. Esta página é restrita a administradores.";
    header('Location: ../geral/dashboard.php');
    exit();
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');

// --- LÓGICA DE PESQUISA E ORDENAÇÃO ---
$busca = $_GET['busca'] ?? '';
$coluna = $_GET['coluna'] ?? 'NomeHorta';
$ordem = $_GET['ordem'] ?? 'ASC';

// Whitelist de colunas permitidas (SINTAXE DO ARRAY CORRIGIDA)
$colunasPermitidas = ["ID_Horta", "NomeHorta", "Localizacao", "TipoDoPlantio", "NomeProprietario"];
if (!in_array($coluna, $colunasPermitidas)) {
    $coluna = 'NomeHorta';
}

$ordensPermitidas = ['ASC', 'DESC'];
if (!in_array($ordem, $ordensPermitidas)) {
    $ordem = 'ASC';
}

$hortas = [];
$erro = null;
$params = [];

// --- BUSCA DINÂMICA NO BANCO DE DADOS (JÁ CORRIGIDA PARA MYSQL) ---
if ($conn) {
    try {
        $sql = 'SELECT 
                    h.ID_Horta, 
                    h.NomeHorta, 
                    h.Localizacao, 
                    h.TipoDoPlantio,
                    u.Nome AS NomeProprietario,
                    u.ID_Usuario -- ALTERAÇÃO 1: Adicionado para puxar o ID do usuário
                FROM Horta h
                INNER JOIN Usuario u ON h.fk_Usuario = u.ID_Usuario';

        if (!empty($busca)) {
            $colunaBusca = ($coluna == 'NomeProprietario') ? 'u.Nome' : 'h.' . $coluna;
            $sql .= ' WHERE ' . $colunaBusca . ' LIKE ?';
            $params[] = '%' . $busca . '%';
        }

        $colunaOrdem = ($coluna == 'NomeProprietario') ? 'NomeProprietario' : 'h.' . $coluna;
        $sql .= ' ORDER BY ' . $colunaOrdem . ' ' . $ordem;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $hortas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $erro = "Erro ao buscar as hortas: " . $e->getMessage();
    }
} else {
    $erro = "Não foi possível conectar ao banco de dados.";
}
?>

<main class="blocoPrincipal">
    <div class="container mt-5">
        <h1 class="mb-4">Lista de Hortas (Administrador)</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="listagem_hortas.php" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="busca" placeholder="Digite sua busca..." value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="coluna" class="form-select">
                            <option value="NomeHorta" <?php if ($coluna == 'NomeHorta') echo 'selected'; ?>>Nome da Horta</option>
                            <option value="Localizacao" <?php if ($coluna == 'Localizacao') echo 'selected'; ?>>Localização</option>
                            <option value="TipoDoPlantio" <?php if ($coluna == 'TipoDoPlantio') echo 'selected'; ?>>Tipo de Plantio</option>
                            <option value="NomeProprietario" <?php if ($coluna == 'NomeProprietario') echo 'selected'; ?>>Proprietário</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="ordem" class="form-select">
                            <option value="ASC" <?php if ($ordem == 'ASC') echo 'selected'; ?>>Crescente (A-Z)</option>
                            <option value="DESC" <?php if ($ordem == 'DESC') echo 'selected'; ?>>Decrescente (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-primary flex-grow-1">Pesquisar</button>
                        <a href="listagem_hortas.php" class="btn btn-secondary ms-2" title="Limpar Busca"><i class="bi bi-x-lg"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome da Horta</th>
                            <th>Localização</th>
                            <th>Tipo de Plantio</th>
                            <th>Proprietário</th>
                            <th>ID Proprietário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($hortas) > 0): ?>
                            <?php foreach ($hortas as $horta): ?>
                                <tr>
                                    <td><?= htmlspecialchars($horta['ID_Horta']) ?></td>
                                    <td><?= htmlspecialchars($horta['NomeHorta']) ?></td>
                                    <td><?= htmlspecialchars($horta['Localizacao']) ?></td>
                                    <td><?= htmlspecialchars($horta['TipoDoPlantio']) ?></td>
                                    <td><?= htmlspecialchars($horta['NomeProprietario']) ?></td>
                                    <td><?= htmlspecialchars($horta['ID_Usuario']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma horta encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php
require_once('../geral/footer.php');
?>