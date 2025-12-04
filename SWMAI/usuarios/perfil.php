<?php
session_start();
require_once('../geral/conexao.php');
require_once('../usuarios/logica-autenticacao.php');


// 1. PROTEÇÃO DA PÁGINA: Redireciona o usuário se ele não estiver logado.
if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login.";
    header('Location: formulario_login.php');
    exit();
}

// 2. BUSCA DE DADOS: Pega o ID do usuário da sessão e busca seus dados e suas hortas.
$usuario = null;
$hortas = [];
$erro = null;

if ($conn) {
    try {
        $id_logado = id_usuario();

        // Busca os dados do usuário
        $sql_usuario = 'SELECT * FROM Usuario WHERE ID_Usuario = ?';
        $stmt_usuario = $conn->prepare($sql_usuario);
        $stmt_usuario->execute([$id_logado]);
        $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

        // SQL CORRIGIDO PARA MYSQL
        $sql_hortas = 'SELECT NomeHorta, TipoDoPlantio FROM Horta WHERE fk_Usuario = ? ORDER BY NomeHorta ASC';
        $stmt_hortas = $conn->prepare($sql_hortas);
        $stmt_hortas->execute([$id_logado]);
        $hortas = $stmt_hortas->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $erro = "Erro ao carregar os dados do perfil: " . $e->getMessage();
    }
} else {
    $erro = "Não foi possível conectar ao banco de dados.";
}

// Helper para converter NivelAcesso para um texto legível
function getNivelAcessoTexto($nivel)
{
    switch ($nivel) {
        case 3:
            return 'Desenvolvedor';
        case 2:
            return 'Administrador';
        default:
            return 'Usuário Padrão';
    }
}
?>

<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>

<main class="blocoPrincipal">
    <div class="container mt-5 mb-5">
        <?php if (isset($_SESSION['perfil_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['perfil_success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['perfil_success']); ?>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php elseif ($usuario): ?>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <h4 class="card-title"><?= htmlspecialchars($usuario['Nome']) ?></h4>
                            <p class="text-muted mb-2"><?= htmlspecialchars(getNivelAcessoTexto($usuario['NivelAcesso'])) ?></p>
                            <hr>
                            <a href="../usuarios/formulario_editar_perfil.php" class="btn btn-primary"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i class="bi bi-person-lines-fill me-2"></i>Detalhes da Conta</h5>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">E-mail</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?= htmlspecialchars($usuario['Email']) ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Telefone</p>
                                </div>
                                <div class="col-sm-9">
                                    <?= htmlspecialchars(formatarTelefone(telefone: $usuario['Telefone'])) ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Tipo de Conta</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?= htmlspecialchars($usuario['TipoPessoa']) ?></p>
                                </div>
                            </div>
                            <?php if ($usuario['TipoPessoa'] === 'Física'): ?>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0 fw-bold">Data de Nasc.</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0"><?= htmlspecialchars((new DateTime($usuario['DataNasc']))->format('d/m/Y')) ?></p>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0 fw-bold">CPF</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">
                                            <?= htmlspecialchars(formatarCPF(cpf: $usuario['CPF'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0 fw-bold">CNPJ</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">
                                            <?= htmlspecialchars(formatarCNPJ(cnpj: $usuario['CNPJ'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3"><i class="bi bi-flower1 me-2"></i>Minhas Hortas</h5>
                            <?php if (count($hortas) > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($hortas as $horta): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?= htmlspecialchars($horta['NomeHorta']) ?>
                                            <span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($horta['TipoDoPlantio']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">Você ainda não possui hortas cadastradas.</p>
                                <a href="../hortas/formulario_cadastro_horta.php" class="btn btn-outline-primary">Cadastrar Nova Horta</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
require_once('../geral/footer.php');
?>