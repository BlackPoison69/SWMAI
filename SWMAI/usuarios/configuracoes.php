<?php
session_start();
// Inclui os arquivos de autenticação e conexão
require_once('logica-autenticacao.php');
verificarAcesso(1);

// 1. PROTEÇÃO DA PÁGINA: Redireciona se o usuário não estiver logado
if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login.";
    header('Location: formulario_login.php');
    exit();
}

// Incluído aqui para que a variável $pagina_atual seja definida antes do header
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>

<main class="blocoPrincipal">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4">Configurações da Conta</h1>

                <?php if (isset($_SESSION['config_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['config_success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['config_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['config_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['config_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['config_error']); ?>
                <?php endif; ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-key-fill me-2"></i>Alterar Senha</h5>
                    </div>
                    <div class="card-body">
                        <form action="processa_alterar_senha.php" method="POST" id="formAlterarSenha">
                            <div class="mb-3">
                                <label for="senhaAtual" class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" id="senhaAtual" name="senhaAtual" required>
                            </div>
                            <div class="mb-3">
                                <label for="novaSenha" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="novaSenha" name="novaSenha" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmaNovaSenha" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirmaNovaSenha" name="confirmaNovaSenha" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-bell-fill me-2"></i>Preferências de Notificação</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="notificacaoEmail" checked>
                            <label class="form-check-label" for="notificacaoEmail">Receber notificações por e-mail sobre alertas críticos</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="resumoDiario">
                            <label class="form-check-label" for="resumoDiario">Receber um resumo diário da atividade da horta</label>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-exclamation-octagon-fill me-2"></i>Zona de Perigo</h5>
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Excluir esta conta</strong>
                            <p class="mb-0 text-muted small">Após a exclusão, todos os seus dados serão perdidos permanentemente.</p>
                        </div>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarExclusaoModal">
                            Excluir Conta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="confirmarExclusaoModal" tabindex="-1" aria-labelledby="confirmarExclusaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarExclusaoLabel">Confirmar Exclusão de Conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Você tem certeza absoluta que deseja excluir sua conta? Esta ação é irreversível e todos os seus dados, incluindo informações de culturas e registros de sensores, serão apagados para sempre.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="excluir.php" class="btn btn-danger">Sim, Excluir Minha Conta</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para validar a confirmação da nova senha
    document.getElementById('formAlterarSenha').addEventListener('submit', function(event) {
        var novaSenha = document.getElementById('novaSenha').value;
        var confirmaNovaSenha = document.getElementById('confirmaNovaSenha').value;
        if (novaSenha !== confirmaNovaSenha) {
            alert('A nova senha e a confirmação não coincidem. Por favor, verifique.');
            event.preventDefault(); // Impede o envio do formulário
        }
    });
</script>

<?php
require_once('../geral/footer.php');
?>