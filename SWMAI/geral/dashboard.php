<?php
session_start();
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('header.php');
require_once('../usuarios/logica-autenticacao.php');
require_once('conexao.php');
require_once('parametros_ideais.php');

if (!autenticado()) {
    header('Location: ../usuarios/formulario_login.php');
    exit();
}

$id_usuario_logado = id_usuario();
$dados_sensor = null;
$total_alertas = 0;
$saude_planta_recente = 0;
$nome_horta = "Nenhuma Horta Encontrada";
$tipo_plantio = "N/A";
$analise_temp = null;

$hortas_usuario = [];
$id_horta_selecionada = null;

if ($conn) {
    try {
        $stmt_hortas = $conn->prepare("SELECT ID_Horta, NomeHorta, TipoDoPlantio FROM Horta WHERE fk_Usuario = ? ORDER BY NomeHorta ASC");
        $stmt_hortas->execute([$id_usuario_logado]);
        $hortas_usuario = $stmt_hortas->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($hortas_usuario)) {
            $id_horta_selecionada = $_GET['id_horta'] ?? $hortas_usuario[0]['ID_Horta'];

            $horta_valida = false;
            foreach ($hortas_usuario as $horta) {
                if ($horta['ID_Horta'] == $id_horta_selecionada) {
                    $horta_valida = true;
                    $nome_horta = $horta['NomeHorta'];
                    $tipo_plantio = $horta['TipoDoPlantio'];
                    break;
                }
            }

            if (!$horta_valida) {
                header('Location: dashboard.php?id_horta=' . $hortas_usuario[0]['ID_Horta']);
                exit();
            }

            $regras_cultura = getParametrosIdeais()[$tipo_plantio] ?? getParametrosIdeais()['Geral'];

            $stmt_registro = $conn->prepare("SELECT * FROM Registro WHERE fk_Horta = ? ORDER BY MomentoCaptura DESC LIMIT 1");
            $stmt_registro->execute([$id_horta_selecionada]);
            $dados_sensor = $stmt_registro->fetch(PDO::FETCH_ASSOC);

            if ($dados_sensor) {
                $analise_temp = analisarRegistro($dados_sensor, $regras_cultura);
                $total_alertas = count($analise_temp['problemas']);
                $saude_planta_recente = $analise_temp['saude'];
            }
        }
    } catch (Exception $e) {
        error_log("Erro no dashboard: " . $e->getMessage());
    }
}

function getCorStatus($valor, $regras)
{
    if ($valor >= $regras['alerta_min'] && $valor <= $regras['alerta_max']) {
        return 'success';
    } elseif (($valor >= $regras['critico_min'] && $valor < $regras['alerta_min']) || ($valor > $regras['alerta_max'] && $valor <= $regras['critico_max'])) {
        return 'warning';
    } else {
        return 'danger';
    }
}

function getCorSaude($saude)
{
    if ($saude >= 85) return 'success';
    if ($saude >= 65) return 'info';
    if ($saude >= 50) return 'warning';
    return 'danger';
}

$cores_cards = [];
if ($dados_sensor) {
    $regras_cultura = getParametrosIdeais()[$tipo_plantio] ?? getParametrosIdeais()['Geral'];

    $cores_cards['saude'] = getCorSaude($saude_planta_recente);
    $cores_cards['temp_ar'] = getCorStatus($dados_sensor['TempAr'], $regras_cultura['TempAr']);
    $cores_cards['umid_solo'] = getCorStatus($dados_sensor['UmidSolo'], $regras_cultura['UmidSolo']);
    $cores_cards['umid_ar'] = getCorStatus($dados_sensor['UmidAr'], $regras_cultura['UmidAr']);
}

?>

<div class="container-fluid px-4">
    <form method="GET" action="">
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <div class="d-flex flex-column">
                <h1 class="h1 mb-0 text-gray-800">Dashboard: <span class="text-primary"><?= htmlspecialchars($nome_horta) ?></span></h1>
                <h3 class="h3 mb-0 text-gray-800">Tipo de Plantio: <span class="text-primary"><?= htmlspecialchars($tipo_plantio) ?></span></h3>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (!empty($hortas_usuario)): ?>
                    <select name="id_horta" class="form-select" style="width: auto;" onchange="this.form.submit()">
                        <?php foreach ($hortas_usuario as $horta): ?>
                            <option value="<?= $horta['ID_Horta'] ?>" <?= ($horta['ID_Horta'] == $id_horta_selecionada) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($horta['NomeHorta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <a href="analises.php?id_horta=<?= htmlspecialchars($id_horta_selecionada) ?>" class="btn btn-success btn-icon-split">
                    <span class="icon text-white-50"><i class="bi bi-graph-up-arrow"></i></span>
                    <span class="text">Ver Análises Detalhadas</span>
                </a>
            </div>
        </div>
    </form>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Visão geral do seu plantio com base nas últimas informações</li>
    </ol>

    <?php if (empty($hortas_usuario)): ?>
        <div class="alert alert-warning text-center">
            <h4><i class="bi bi-info-circle-fill me-2"></i>Nenhuma Horta Encontrada</h4>
            <p>Você precisa cadastrar pelo menos uma horta para visualizar as análises. <a href="../hortas/formulario_cadastro_horta.php" class="alert-link">Cadastre sua primeira horta agora!</a></p>
        </div>
    <?php elseif (!$dados_sensor): ?>
        <div class="alert alert-warning text-center">
            <h4><i class="bi bi-info-circle-fill me-2"></i>Nenhum dado encontrado para esta horta</h4>
            <p>Não há registros de sensores para a horta "<?= htmlspecialchars($nome_horta) ?>". Certifique-se de que o seu dispositivo está funcionando e enviando dados.</p>
        </div>
    <?php endif; ?>

    <?php if ($dados_sensor): ?>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-<?= $cores_cards['saude'] ?> shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-<?= $cores_cards['saude'] ?> text-uppercase mb-1">
                                    Saúde Geral</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 text-<?= $cores_cards['saude'] ?>">
                                    <?= htmlspecialchars(number_format($saude_planta_recente, 1) . '%') ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-heart-pulse-fill text-<?= $cores_cards['saude'] ?>" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-<?= $cores_cards['temp_ar'] ?> shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-<?= $cores_cards['temp_ar'] ?> text-uppercase mb-1">
                                    Temperatura do Ar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 text-<?= $cores_cards['temp_ar'] ?>">
                                    <?= htmlspecialchars(number_format($dados_sensor['TempAr'], 1) . '°C') ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-thermometer-half text-<?= $cores_cards['temp_ar'] ?>" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-<?= $cores_cards['umid_solo'] ?> shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-<?= $cores_cards['umid_solo'] ?> text-uppercase mb-1">
                                    Umidade do Solo</div>
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800 text-<?= $cores_cards['umid_solo'] ?>">
                                    <?= htmlspecialchars(number_format($dados_sensor['UmidSolo'], 1) . '%') ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-moisture text-<?= $cores_cards['umid_solo'] ?>" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-<?= $cores_cards['umid_ar'] ?> shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-<?= $cores_cards['umid_ar'] ?> text-uppercase mb-1">
                                    Umidade do Ar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 text-<?= $cores_cards['umid_ar'] ?>">
                                    <?= htmlspecialchars(number_format($dados_sensor['UmidAr'], 1) . '%') ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-droplet-half text-<?= $cores_cards['umid_ar'] ?>" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Atividades Recentes</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Último registro recebido:
                                <span class="badge bg-secondary rounded-pill"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($dados_sensor['MomentoCaptura']))) ?></span>
                            </li>
                            <?php
                            $analise_recente = analisarRegistro($dados_sensor, getParametrosIdeais()[$tipo_plantio] ?? getParametrosIdeais()['Geral']);
                            $detalhes_analise = $analise_recente['detalhes'];
                            ?>
                            <?php foreach ($detalhes_analise as $detalhe): ?>
                                <?php
                                // Lógica para determinar a unidade de medida
                                $unidade = '';
                                if (strpos($detalhe['nome'], 'Umidade') !== false) {
                                    $unidade = '%';
                                } elseif (strpos($detalhe['nome'], 'Temperatura') !== false) {
                                    $unidade = '°C';
                                }
                                ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi <?= htmlspecialchars($detalhe['icone']) ?> <?= htmlspecialchars($detalhe['status_cor']) ?> me-2"></i>
                                        <?= htmlspecialchars($detalhe['nome']) ?>:
                                        <strong class="fw-bold"><?= htmlspecialchars($detalhe['valor']) ?><?= $unidade ?></strong>
                                    </span>
                                    <span class="<?= htmlspecialchars($detalhe['status_cor']) ?>"><?= htmlspecialchars($detalhe['status_texto']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
require_once('footer.php');
?>