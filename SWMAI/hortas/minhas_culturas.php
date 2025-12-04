<?php
session_start();
require_once('../usuarios/logica-autenticacao.php');
require_once('../geral/conexao.php');
require_once('../geral/parametros_ideais.php'); // Inclui o arquivo que contém a função analisarRegistro()

if (!autenticado()) {
    $_SESSION['login_error'] = "Acesso negado. Por favor, faça o login para ver suas culturas.";
    header('Location: ../usuarios/formulario_login.php');
    exit();
}

$hortas = [];
$erro = null;

if ($conn) {
    try {
        $id_usuario_logado = id_usuario();

        $sql = 'SELECT * FROM Horta WHERE fk_Usuario = ? ORDER BY NomeHorta ASC';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario_logado]);
        $hortas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $erro = "Erro ao buscar suas culturas: " . $e->getMessage();
    }
} else {
    $erro = "Não foi possível conectar ao banco de dados.";
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Minhas Culturas</h1>
        <a href="../hortas/formulario_cadastro_horta.php" class="btn btn-primary mt-4">
            <i class="bi bi-plus-circle-fill me-2"></i>Adicionar Nova Cultura
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Gerencie suas plantações e culturas aqui</li>
    </ol>

    <?php if (isset($_SESSION['cultura_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['cultura_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['cultura_success']); ?>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if (count($hortas) > 0): ?>
            <?php foreach ($hortas as $horta): ?>
                <?php
                // --- LÓGICA PARA CALCULAR A SAÚDE MÉDIA DE CADA HORTA ---
                $regras_cultura = getParametrosIdeais()[$horta['TipoDoPlantio']] ?? getParametrosIdeais()['Geral'];

                $stmt_registros = $conn->prepare("SELECT * FROM Registro WHERE fk_Horta = ? AND MomentoCaptura >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY MomentoCaptura DESC");
                $stmt_registros->execute([$horta['ID_Horta']]);
                $registros_horta = $stmt_registros->fetchAll(PDO::FETCH_ASSOC);

                $saude_geral_media = 0;
                $total_saude = 0;

                if (!empty($registros_horta)) {
                    foreach ($registros_horta as $registro) {
                        $analise_temp = analisarRegistro($registro, $regras_cultura);
                        $total_saude += $analise_temp['saude'];
                    }
                    $saude_geral_media = round($total_saude / count($registros_horta));
                }

                $status_texto = "Não monitorado";
                $status_cor = "secondary";
                if ($saude_geral_media > 0) {
                    if ($saude_geral_media >= 85) {
                        $status_texto = 'Excelente';
                        $status_cor = 'success';
                    } elseif ($saude_geral_media >= 65) {
                        $status_texto = 'Bom';
                        $status_cor = 'info';
                    } elseif ($saude_geral_media >= 50) {
                        $status_texto = 'Atenção';
                        $status_cor = 'warning text-dark';
                    } else {
                        $status_texto = 'Crítico';
                        $status_cor = 'danger';
                    }
                }
                ?>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold text-primary"><?= htmlspecialchars($horta['NomeHorta']) ?></h5>
                            <p class="card-text">
                                <strong>Localização:</strong> <?= htmlspecialchars($horta['Localizacao']) ?><br>
                                <strong>Cultura Principal:</strong> <?= htmlspecialchars($horta['TipoDoPlantio']) ?>
                            </p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Status:
                                <span class="badge bg-<?= $status_cor ?>"><?= $status_texto ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Última Irrigação:
                                <span class="badge bg-secondary">Não monitorado</span>
                            </li>
                        </ul>
                        <div class="card-body text-center">
                            <a href="../geral/analises.php?id_horta=<?= $horta['ID_Horta'] ?>" class="btn btn-info me-2"><i class="bi bi-eye-fill me-1"></i> Ver Detalhes</a>
                            <a href="../hortas/formulario_config_horta.php?id=<?= $horta['ID_Horta'] ?>" class="btn btn-outline-secondary"><i class="bi bi-gear-fill me-1"></i> Gerenciar</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <h5 class="card-title">Nenhuma cultura encontrada</h5>
                        <p class="card-text">Você ainda não cadastrou nenhuma horta ou plantação. Clique no botão abaixo para começar!</p>
                        <a href="../hortas/formulario_cadastro_horta.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle-fill me-2"></i>Adicionar Nova Cultura
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once('../geral/footer.php'); ?>