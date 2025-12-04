<?php
session_start();
// Inclui a lógica de autenticação.
require_once('logica-autenticacao.php');


// Se o usuário já estiver logado, redireciona para o dashboard.
if (autenticado()) {
    header('Location: ../geral/dashboard.php');
    exit();
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>

<main class="blocoPrincipal">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <div class="text-center mb-3">
                            <img src="../IMG/Logo Transparente.png" alt="Logo SWMAI" style="height: 100px;">
                        </div>
                        <h3 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i> Login</h3>
                    </div>
                    <div class="card-body p-4">

                        <?php // Exibe a mensagem de sucesso vinda do cadastro 
                        ?>
                        <?php if (isset($_SESSION['login_success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?= htmlspecialchars($_SESSION['login_success']) ?>
                            </div>
                            <?php unset($_SESSION['login_success']); ?>
                        <?php endif; ?>

                        <?php // Exibe mensagens de erro vindas do processamento de login 
                        ?>
                        <?php if (isset($_SESSION['login_error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['login_error']) ?>
                            </div>
                            <?php unset($_SESSION['login_error']); ?>
                        <?php endif; ?>

                        <form action="processa_login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="senha" name="senha" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p class="mb-0 text-muted">Não tem uma conta? <a href="formulario_cadastro_usuarios.php">Cadastre-se</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once('../geral/footer.php');
?>