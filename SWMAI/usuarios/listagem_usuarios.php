<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
verificarAcesso(2);
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
require_once('../geral/conexao.php');

// --- LÓGICA DE PESQUISA E ORDENAÇÃO ---

$busca = $_GET['busca'] ?? '';
$coluna = $_GET['coluna'] ?? 'Nome';
$ordem = $_GET['ordem'] ?? 'ASC';

// Whitelist de colunas permitidas ATUALIZADA para MySQL (sem acentos)
$colunasPermitidas = ["ID_Usuario", "Nome", "Email", "TipoPessoa"];
if (!in_array($coluna, $colunasPermitidas)) {
    $coluna = 'Nome'; // Padrão seguro
}

$ordensPermitidas = ['ASC', 'DESC'];
if (!in_array($ordem, $ordensPermitidas)) {
    $ordem = 'ASC'; // Padrão seguro
}

$usuarios = [];
$erro = null;
$params = [];

if ($conn) {
    try {
        // CONSULTA SQL ATUALIZADA PARA SINTAXE MYSQL (sem aspas duplas, sem acentos)
        $sql = 'SELECT ID_Usuario, Nome, Email, Telefone, TipoPessoa FROM Usuario';

        if (!empty($busca)) {
            // No MySQL, o padrão para busca case-insensitive é usar a collation da tabela, mas LIKE funciona bem.
            $sql .= ' WHERE ' . $coluna . ' LIKE ?';
            $params[] = '%' . $busca . '%';
        }

        // Cláusula ORDER BY ATUALIZADA
        $sql .= ' ORDER BY ' . $coluna . ' ' . $ordem;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $erro = "Erro ao buscar os usuários: " . $e->getMessage();
    }
} else {
    $erro = "Não foi possível conectar ao banco de dados.";
}
?>

<main class="blocoPrincipal">
    <div class="container mt-5">
        <h1 class="mb-4">Lista de Usuários Cadastrados</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="listagem_usuarios.php" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="busca" placeholder="Digite sua busca..." value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="coluna" class="form-select">
                            <option value="Nome" <?php if ($coluna == 'Nome') echo 'selected'; ?>>Nome</option>
                            <option value="Email" <?php if ($coluna == 'Email') echo 'selected'; ?>>Email</option>
                            <option value="ID_Usuario" <?php if ($coluna == 'ID_Usuario') echo 'selected'; ?>>ID</option>
                            <option value="TipoPessoa" <?php if ($coluna == 'TipoPessoa') echo 'selected'; ?>>Tipo</option>
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
                        <a href="listagem_usuarios.php" class="btn btn-secondary ms-2" title="Limpar Busca"><i class="bi bi-x-lg"></i></a>
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
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($usuarios) > 0): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['ID_Usuario']) ?></td>
                                    <td><?= htmlspecialchars($usuario['Nome']) ?></td>
                                    <td><?= htmlspecialchars($usuario['Email']) ?></td>
                                    <td><?= htmlspecialchars($usuario['Telefone']) ?></td>
                                    <td><?= htmlspecialchars($usuario['TipoPessoa']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <?php if (!empty($busca)): ?>
                                        Nenhum usuário encontrado com o termo "<?= htmlspecialchars($busca) ?>".
                                    <?php else: ?>
                                        Nenhum usuário encontrado no banco de dados.
                                    <?php endif; ?>
                                </td>
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