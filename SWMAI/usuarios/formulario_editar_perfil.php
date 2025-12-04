<?php
session_start();
// Inclui os arquivos de autenticação e conexão
require_once('logica-autenticacao.php');
verificarAcesso(1);

require_once('../geral/conexao.php');

// 1. PROTEÇÃO DA PÁGINA
if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login.";
    header('Location: formulario_login.php');
    exit();
}

// 2. BUSCA DE DADOS
$usuario = null;
$erro = null;

if ($conn) {
    try {
        $id_logado = id_usuario();
        // ===== CONSULTA SQL CORRIGIDA PARA MYSQL =====
        $sql = 'SELECT * FROM Usuario WHERE ID_Usuario = ?';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_logado]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $erro = "Não foi possível encontrar os dados do seu perfil.";
        }
    } catch (Exception $e) {
        $erro = "Erro ao carregar os dados do perfil: " . $e->getMessage();
    }
} else {
    $erro = "Não foi possível conectar ao banco de dados.";
}
?>

<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>
<style>
    /* Novo estilo para campos readonly */
    .form-control-readonly {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .form-control-readonly:focus {
        background-color: #e9ecef;
        box-shadow: none;
    }
</style>
<main class="blocoPrincipal">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h3 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['form_edit_error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['form_edit_error']) ?>
                            </div>
                            <?php unset($_SESSION['form_edit_error']); ?>
                        <?php endif; ?>

                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                        <?php else: ?>
                            <form action="processa_editar.php" method="POST">
                                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['ID_Usuario']) ?>">

                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($usuario['Nome']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control form-control-readonly" id="email" name="email" value="<?= htmlspecialchars($usuario['Email']) ?>" readonly>
                                        <span class="input-group-text">
                                            <i class="bi bi-lock-fill text-muted"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">
                                        Este campo não pode ser alterado.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($usuario['Telefone']) ?>" required maxlength="11">
                                </div>

                                <?php if ($usuario['TipoPessoa'] === 'Física'): ?>
                                    <div class="mb-3">
                                        <label for="dataNasc" class="form-label">Data de Nascimento</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-readonly" value="<?= htmlspecialchars((new DateTime($usuario['DataNasc']))->format('d/m/Y')) ?>" readonly>
                                            <span class="input-group-text">
                                                <i class="bi bi-lock-fill text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cpf" class="form-label">CPF</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-readonly" id="cpf" name="cpf" value="<?= htmlspecialchars(formatarCPF($usuario['CPF'])) ?>" readonly>
                                            <span class="input-group-text">
                                                <i class="bi bi-lock-fill text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                <?php else: // Pessoa Jurídica 
                                ?>
                                    <div class="mb-3">
                                        <label for="cnpj" class="form-label">CNPJ</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-readonly" id="cnpj" name="cnpj" value="<?= htmlspecialchars(formatarCNPJ($usuario['CNPJ'])) ?>" readonly>
                                            <span class="input-group-text">
                                                <i class="bi bi-lock-fill text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <hr>
                                <p class="text-muted small">Para alterar sua senha, acesse a página de "Configurações".</p>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="perfil.php" class="btn btn-secondary me-2">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once('../geral/footer.php');
?>