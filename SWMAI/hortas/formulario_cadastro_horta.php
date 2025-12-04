<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
require_once('../geral/parametros_ideais.php'); // Certifique-se de que este arquivo existe e está correto

// Redireciona se o usuário não estiver logado.
if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login para cadastrar uma horta.";
    header('Location: ../usuarios/formulario_login.php');
    exit();
}
// verifica se o usuário tem permissão para cadastrar uma horta (nível 1)
verificarAcesso(1);

// Pega a lista de culturas do arquivo de parâmetros para preencher o <select>
$culturas_disponiveis = getParametrosIdeais();
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>

<main class="blocoPrincipal">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h3 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Cadastrar Nova Horta</h3>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_SESSION['horta_error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['horta_error']) ?>
                            </div>
                            <?php unset($_SESSION['horta_error']); ?>
                        <?php endif; ?>

                        <form action="../hortas/processa_inserir_horta.php" method="POST">

                            <div class="mb-3">
                                <label for="nomeHorta" class="form-label">Nome da Horta</label>
                                <input type="text" class="form-control" id="nomeHorta" name="nomeHorta" placeholder="Ex: Estufa de Tomates da Fazenda" required>
                            </div>

                            <div class="mb-3">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao" placeholder="Ex: Setor Norte, Lote 12" required>
                            </div>

                            <div class="mb-3">
                                <label for="tipoPlantio" class="form-label">Principal Cultura / Tipo de Plantio</label>
                                <select class="form-select" id="tipoPlantio" name="tipoPlantio" required>
                                    <option value="" disabled selected>Selecione um tipo...</option>
                                    <?php foreach ($culturas_disponiveis as $cultura => $regras): ?>
                                        <option value="<?= htmlspecialchars($cultura) ?>">
                                            <?= htmlspecialchars($cultura) ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="Outro">Outro (Especificar)</option>
                                </select>
                            </div>

                            <div class="mb-4" id="outroTipoPlantioContainer" style="display: none;">
                                <label for="tipoPlantioOutro" class="form-label">Especifique o tipo</label>
                                <input type="text" class="form-control" id="tipoPlantioOutro" name="tipoPlantioOutro" placeholder="Ex: Manjericão Roxo">
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="../hortas/minhas_culturas.php" class="btn btn-secondary me-2">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Cadastrar Horta</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.getElementById('tipoPlantio').addEventListener('change', function() {
        var outroContainer = document.getElementById('outroTipoPlantioContainer');
        var outroInput = document.getElementById('tipoPlantioOutro');

        if (this.value === 'Outro') {
            outroContainer.style.display = 'block';
            outroInput.required = true;
        } else {
            outroContainer.style.display = 'none';
            outroInput.required = false;
            outroInput.value = '';
        }
    });
</script>

<?php
require_once('../geral/footer.php');
?>