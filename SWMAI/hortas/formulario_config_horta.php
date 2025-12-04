<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
verificarAcesso(1);

require_once('../geral/conexao.php');
require_once('../geral/parametros_ideais.php'); // Certifique-se de que este arquivo existe e está correto

// 1. PROTEÇÃO E VALIDAÇÃO INICIAL
if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login.";
    header('Location: ../usuarios/formulario_login.php');
    exit();
}

$id_horta = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$id_usuario_logado = id_usuario();
$horta = null;
$sensores = [];
$erro = null;

if (!$id_horta) {
    // Caminho do redirect corrigido
    header('Location: ../hortas/minhas_culturas.php');
    exit();
}

// 2. BUSCA DE DADOS DA HORTA E SENSORES VINCULADOS
if ($conn) {
    try {
        // ===== CONSULTA SQL CORRIGIDA PARA MYSQL =====
        $sql_horta = 'SELECT * FROM Horta WHERE ID_Horta = ? AND fk_Usuario = ?';
        $stmt_horta = $conn->prepare($sql_horta);
        $stmt_horta->execute([$id_horta, $id_usuario_logado]);
        $horta = $stmt_horta->fetch(PDO::FETCH_ASSOC);

        if (!$horta) {
            $_SESSION['cultura_error'] = "Horta não encontrada ou acesso não permitido.";
            // Caminho do redirect corrigido
            header('Location: ../geral/minhas_culturas.php');
            exit();
        }

        // Pega a lista de culturas do arquivo de parâmetros para preencher o <select>
        $culturas_disponiveis = getParametrosIdeais();
        $tipoAtual = $horta['TipoDoPlantio'];
        $isTipoOutro = !array_key_exists($tipoAtual, $culturas_disponiveis);

        // ===== CONSULTA SQL CORRIGIDA PARA MYSQL =====
        $sql_sensores = 'SELECT DISTINCT ID_Sensor FROM Registro WHERE fk_Horta = ? ORDER BY ID_Sensor ASC';
        $stmt_sensores = $conn->prepare($sql_sensores);
        $stmt_sensores->execute([$id_horta]);
        $sensores = $stmt_sensores->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $erro = "Erro ao carregar os dados da horta: " . $e->getMessage();
    }
} else {
    $erro = "Falha na conexão com o banco de dados.";
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>

<main class="blocoPrincipal">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                <?php else: ?>
                    <h1 class="mb-4">Configurações da Horta: <span class="text-primary"><?= htmlspecialchars($horta['NomeHorta']) ?></span></h1>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Editar Informações</h5>
                        </div>
                        <div class="card-body">
                            <form action="processa_editar_horta.php" method="POST">
                                <input type="hidden" name="id_horta" value="<?= htmlspecialchars($horta['ID_Horta']) ?>">
                                <div class="mb-3">
                                    <label for="nomeHorta" class="form-label">Nome da Horta</label>
                                    <input type="text" class="form-control" id="nomeHorta" name="nomeHorta" value="<?= htmlspecialchars($horta['NomeHorta']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="localizacao" class="form-label">Localização</label>
                                    <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?= htmlspecialchars($horta['Localizacao']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tipoPlantio" class="form-label">Principal Cultura / Tipo de Plantio</label>
                                    <select class="form-select" id="tipoPlantio" name="tipoPlantio" required>
                                        <option value="" disabled>Selecione um tipo...</option>
                                        <?php foreach ($culturas_disponiveis as $cultura => $regras): ?>
                                            <option value="<?= htmlspecialchars($cultura) ?>" <?php if ($tipoAtual == $cultura) echo 'selected'; ?>>
                                                <?= htmlspecialchars($cultura) ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="Outro" <?php if ($isTipoOutro) echo 'selected'; ?>>Outro (Especificar)</option>
                                    </select>
                                </div>
                                <div class="mb-4" id="outroTipoPlantioContainer" style="<?= $isTipoOutro ? 'display: block;' : 'display: none;' ?>">
                                    <label for="tipoPlantioOutro" class="form-label">Especifique o tipo</label>
                                    <input type="text" class="form-control" id="tipoPlantioOutro" name="tipoPlantioOutro"
                                        value="<?= $isTipoOutro ? htmlspecialchars($tipoAtual) : '' ?>"
                                        placeholder="Ex: Manjericão Roxo"
                                        <?= $isTipoOutro ? 'required' : '' ?>>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="../hortas/minhas_culturas.php" class="btn btn-secondary me-2">Voltar</a>
                                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-cpu-fill me-2"></i>Sensores Vinculados</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($sensores) > 0): ?>
                                <ul class="list-group">
                                    <?php foreach ($sensores as $sensor): ?>
                                        <li class="list-group-item">Sensor ID: <strong><?= htmlspecialchars($sensor['ID_Sensor']) ?></strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">Nenhum sensor enviou dados para esta horta ainda.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-exclamation-octagon-fill me-2"></i>Zona de Perigo</h5>
                        </div>
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Excluir esta Horta</strong>
                                <p class="mb-0 text-muted small">Ação irreversível. Todos os registros de sensores associados serão perdidos.</p>
                            </div>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarExclusaoModal">Excluir</button>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php if ($horta): ?>
    <div class="modal fade" id="confirmarExclusaoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a horta "<strong><?= htmlspecialchars($horta['NomeHorta']) ?></strong>"?</p>
                    <p class="text-danger">Todos os dados de sensores vinculados a ela serão perdidos.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="processa_excluir_horta.php?id=<?= htmlspecialchars($horta['ID_Horta']) ?>" class="btn btn-danger">Sim, Excluir</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoPlantioSelect = document.getElementById('tipoPlantio');
        const outroContainer = document.getElementById('outroTipoPlantioContainer');
        const outroInput = document.getElementById('tipoPlantioOutro');

        function toggleOutroField() {
            if (tipoPlantioSelect.value === 'Outro') {
                outroContainer.style.display = 'block';
                outroInput.setAttribute('required', 'required');
            } else {
                outroContainer.style.display = 'none';
                outroInput.removeAttribute('required');
                outroInput.value = '';
            }
        }
        tipoPlantioSelect.addEventListener('change', toggleOutroField);
        toggleOutroField(); // Executa ao carregar para garantir o estado inicial correto
    });
</script>

<?php
require_once('../geral/footer.php');
?>