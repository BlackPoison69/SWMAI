<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');

// SEÇÃO 1: Redireciona o usuário se ele já estiver logado.
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
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <div class="text-center mb-3">
                            <img src="../IMG/Logo Transparente.png" alt="Logo SWMAI" style="height: 100px;">
                        </div>
                        <h3 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Criar Nova Conta</h3>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_SESSION['form_error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['form_error']) ?>
                            </div>
                            <?php unset($_SESSION['form_error']); ?>
                        <?php endif; ?>

                        <form action="processa_cadastro.php" method="POST" id="formCadastro">

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(XX) XXXXX-XXXX" required maxlength="11">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="senha" class="form-label">Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" class="form-control" id="senha" name="senha" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirmaSenha" class="form-label">Confirmar Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" class="form-control" id="confirmaSenha" name="confirmaSenha" required onblur="verificaSenhas();" aria-describedby="senhaFeedback">
                                    </div>
                                    <div id="senhaFeedback" class="invalid-feedback">
                                        As senhas informadas não são idênticas.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tipoPessoa" class="form-label">Tipo de Conta</label>
                                <select class="form-select" id="tipoPessoa" name="tipoPessoa">
                                    <option value="fisica" selected>Pessoa Física</option>
                                    <option value="juridica">Pessoa Jurídica</option>
                                </select>
                            </div>

                            <div id="camposPessoaFisica">
                                <div class="mb-3">
                                    <label for="dataNasc" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="dataNasc" name="dataNasc">
                                </div>
                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="11">
                                </div>
                            </div>

                            <div id="camposPessoaJuridica" style="display: none;">
                                <div class="mb-3">
                                    <label for="cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="00.000.000/0001-00" maxlength="14">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Cadastrar</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0 text-muted">Já tem uma conta? <a href="formulario_login.php">Faça o login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formCadastro');
        const tipoPessoaSelect = document.getElementById('tipoPessoa');
        const camposFisicaDiv = document.getElementById('camposPessoaFisica');
        const camposJuridicaDiv = document.getElementById('camposPessoaJuridica');
        const cpfInput = document.getElementById('cpf');
        const dataNascInput = document.getElementById('dataNasc');
        const cnpjInput = document.getElementById('cnpj');
        const senhaInput = document.getElementById('senha');
        const confirmaSenhaInput = document.getElementById('confirmaSenha');

        function toggleRequiredFields() {
            if (tipoPessoaSelect.value === 'fisica') {
                camposFisicaDiv.style.display = 'block';
                camposJuridicaDiv.style.display = 'none';
                cpfInput.setAttribute('required', '');
                dataNascInput.setAttribute('required', '');
                cnpjInput.removeAttribute('required');
            } else {
                camposFisicaDiv.style.display = 'none';
                camposJuridicaDiv.style.display = 'block';
                cnpjInput.setAttribute('required', '');
                cpfInput.removeAttribute('required');
                dataNascInput.removeAttribute('required');
            }
        }

        // Run on page load and whenever the account type changes
        toggleRequiredFields();
        tipoPessoaSelect.addEventListener('change', toggleRequiredFields);

        // Function to validate password match
        function verificaSenhas() {
            if (confirmaSenhaInput.value === '') {
                confirmaSenhaInput.classList.remove('is-invalid');
                return true;
            }
            if (senhaInput.value !== confirmaSenhaInput.value) {
                confirmaSenhaInput.classList.add('is-invalid');
                return false;
            } else {
                confirmaSenhaInput.classList.remove('is-invalid');
                return true;
            }
        }

        // Add a listener to confirm password field
        confirmaSenhaInput.addEventListener('blur', verificaSenhas);
    });
</script>

<?php
require_once('../geral/footer.php');
?>