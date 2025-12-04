<?php
session_start();
require_once('../geral/conexao.php');
require_once('../usuarios/logica-autenticacao.php');
require_once('../geral/parametros_ideais.php');

if (!autenticado()) {
    header('Location: ../usuarios/formulario_login.php');
    exit();
}
$id_usuario_logado = id_usuario();

$hortas_usuario_stmt = $conn->prepare("SELECT ID_Horta, NomeHorta, TipoDoPlantio FROM Horta WHERE fk_Usuario = ? ORDER BY NomeHorta ASC");
$hortas_usuario_stmt->execute([$id_usuario_logado]);
$hortas_usuario = $hortas_usuario_stmt->fetchAll(PDO::FETCH_ASSOC);

$id_horta_selecionada = null;
$tipo_plantio_selecionado = 'Geral';
$nome_horta_selecionada = 'Nenhuma Horta';

if (!empty($hortas_usuario)) {
    $id_horta_selecionada = $_GET['id_horta'] ?? $hortas_usuario[0]['ID_Horta'];

    $horta_valida = false;
    foreach ($hortas_usuario as $horta) {
        if ($horta['ID_Horta'] == $id_horta_selecionada) {
            $horta_valida = true;
            $nome_horta_selecionada = $horta['NomeHorta'];
            $tipo_plantio_selecionado = $horta['TipoDoPlantio'];
            break;
        }
    }
    if (!$horta_valida) {
        $_SESSION['dashboard_error'] = "Acesso a horta inválida.";
        header('Location: ../geral/dashboard.php');
        exit();
    }
}

// Lógica para definir as datas iniciais com base nos registros do banco
$data_minima_bd = date('Y-m-d');
$data_maxima_bd = date('Y-m-d');
if ($id_horta_selecionada) {
    $stmt_datas_minmax = $conn->prepare("SELECT MIN(MomentoCaptura) as data_min, MAX(MomentoCaptura) as data_max FROM Registro WHERE fk_Horta = ?");
    $stmt_datas_minmax->execute([$id_horta_selecionada]);
    $datas_db = $stmt_datas_minmax->fetch(PDO::FETCH_ASSOC);
    if ($datas_db && $datas_db['data_min'] && $datas_db['data_max']) {
        $data_minima_bd = date('Y-m-d', strtotime($datas_db['data_min']));
        $data_maxima_bd = date('Y-m-d', strtotime($datas_db['data_max']));
    }
}

$data_inicio = $_GET['data_inicio'] ?? $data_minima_bd;
$data_fim = $_GET['data_fim'] ?? $data_maxima_bd;

$regras_todas = getParametrosIdeais();
$regras_cultura = $regras_todas[$tipo_plantio_selecionado] ?? $regras_todas['Geral'];

$total_alertas = 0;
$registros_tabela = [];
$chart_labels = '[]';
$chart_saude = '[]';
$chart_temp_ar = '[]';
$chart_umid_solo = '[]';
$saude_geral_media = 0;
$total_registros_encontrados = 0;

$limit = $_GET['limit'] ?? 25;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

if ($id_horta_selecionada) {
    $stmt_registros_geral = $conn->prepare("SELECT * FROM Registro WHERE fk_Horta = ? AND MomentoCaptura BETWEEN ? AND ? ORDER BY MomentoCaptura DESC");
    $stmt_registros_geral->execute([$id_horta_selecionada, $data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);
    $registros_geral = $stmt_registros_geral->fetchAll(PDO::FETCH_ASSOC);

    $total_registros_encontrados = count($registros_geral);

    $total_alertas = 0;
    $total_saude_geral = 0;
    foreach ($registros_geral as $registro) {
        $analise_temporaria = analisarRegistro($registro, $regras_cultura);
        if ($analise_temporaria['saude'] < 50) {
            $total_alertas++;
        }
        $total_saude_geral += $analise_temporaria['saude'];
    }
    $saude_geral_media = ($total_registros_encontrados > 0) ? round($total_saude_geral / $total_registros_encontrados) : 0;

    $registros_tabela = array_slice($registros_geral, $offset, $limit);

    $stmt_chart_raw = $conn->prepare("
        SELECT 
            DATE(MomentoCaptura) as dia, 
            AVG(TempAr) as media_temp_ar,
            AVG(UmidSolo) as media_umid_solo,
            GROUP_CONCAT(TempAr) as temps_dia,
            GROUP_CONCAT(UmidAr) as umid_ar_dia,
            GROUP_CONCAT(UmidSolo) as umid_solo_dia,
            GROUP_CONCAT(Luminosidade) as lum_dia
        FROM Registro 
        WHERE fk_Horta = ? AND MomentoCaptura BETWEEN ? AND ?
        GROUP BY dia ORDER BY dia ASC
    ");
    $stmt_chart_raw->execute([$id_horta_selecionada, $data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);
    $dados_grafico_raw = $stmt_chart_raw->fetchAll(PDO::FETCH_ASSOC);

    $medias_saude = [];
    $labels_grafico = [];
    $medias_temp_ar = [];
    $medias_umid_solo = [];

    foreach ($dados_grafico_raw as $dia) {
        $total_saude_dia = 0;
        $contagem_registros = 0;

        $registros_temp_ar = explode(',', $dia['temps_dia']);
        $registros_umid_ar = explode(',', $dia['umid_ar_dia']);
        $registros_umid_solo = explode(',', $dia['umid_solo_dia']);
        $registros_lum = explode(',', $dia['lum_dia']);

        for ($i = 0; $i < count($registros_temp_ar); $i++) {
            $registro_fake = [
                'TempAr' => $registros_temp_ar[$i],
                'UmidAr' => $registros_umid_ar[$i],
                'UmidSolo' => $registros_umid_solo[$i],
                'Luminosidade' => $registros_lum[$i]
            ];
            $analise_temp = analisarRegistro($registro_fake, $regras_cultura);
            $total_saude_dia += $analise_temp['saude'];
            $contagem_registros++;
        }

        $media_saude_dia = ($contagem_registros > 0) ? $total_saude_dia / $contagem_registros : 0;
        $medias_saude[] = $media_saude_dia;
        $labels_grafico[] = $dia['dia'];
        $medias_temp_ar[] = $dia['media_temp_ar'];
        $medias_umid_solo[] = $dia['media_umid_solo'];
    }

    $chart_labels = json_encode($labels_grafico);
    $chart_saude = json_encode($medias_saude);
    $chart_temp_ar = json_encode($medias_temp_ar);
    $chart_umid_solo = json_encode($medias_umid_solo);
    $saude_geral_media = ($total_registros_encontrados > 0) ? round($total_saude_geral / $total_registros_encontrados) : 0;

    // NOVO CÁLCULO DE MÉDIAS PARA OS CARDS
    $media_temp_ar_geral = 0;
    $media_umid_solo_geral = 0;
    if ($total_registros_encontrados > 0) {
        $total_temp_ar = array_sum(array_column($registros_geral, 'TempAr'));
        $total_umid_solo = array_sum(array_column($registros_geral, 'UmidSolo'));

        $media_temp_ar_geral = $total_temp_ar / $total_registros_encontrados;
        $media_umid_solo_geral = $total_umid_solo / $total_registros_encontrados;
    }
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('header.php');
?>

<style>
    .card-icon {
        font-size: 2rem;
        opacity: 0.7;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>

<div class="container-fluid px-4">
    <form method="GET" action="">
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <div class="d-flex flex-column">
                <h1 class="h1 mb-0 text-gray-800">Análises: <span class="text-primary"><?= htmlspecialchars($nome_horta_selecionada) ?></span></h1>
                <h3 class="h3 mb-0 text-gray-800">Tipo de Plantio: <span class="text-primary"><?= htmlspecialchars($tipo_plantio_selecionado) ?></span></h3>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <select name="id_horta" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <?php foreach ($hortas_usuario as $horta): ?>
                        <option value="<?= $horta['ID_Horta'] ?>" <?= ($horta['ID_Horta'] == $id_horta_selecionada) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($horta['NomeHorta']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="data_inicio" class="form-control" style="width: auto;" value="<?= htmlspecialchars($data_inicio) ?>">
                <span>até</span>
                <input type="date" name="data_fim" class="form-control" style="width: auto;" value="<?= htmlspecialchars($data_fim) ?>">
                <button type="submit" class="btn btn-primary btn-icon-split">
                    <span class="icon text-white-50"><i class="bi bi-filter"></i></span>
                    <span class="text">Filtrar</span>
                </button>
                <a href="?id_horta=<?= $id_horta_selecionada ?>&data_inicio=<?= $data_minima_bd ?>&data_fim=<?= $data_maxima_bd ?>" class="btn btn-secondary btn-icon-split">
                    <span class="icon text-white-50"><i class="bi bi-calendar-range"></i></span>
                    <span class="text">Período Total</span>
                </a>
            </div>
        </div>
    </form>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Visualize dados históricos e tendências de suas culturas</li>
    </ol>

    <?php if (empty($hortas_usuario)): ?>
        <div class="alert alert-warning text-center">
            <h4><i class="bi bi-info-circle-fill me-2"></i>Nenhuma Horta Encontrada</h4>
            <p>Você precisa cadastrar pelo menos uma horta para visualizar as análises. <a href="../hortas/formulario_cadastro_horta.php" class="alert-link">Cadastre sua primeira horta agora!</a></p>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Temperatura Média do Ar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($media_temp_ar_geral, 1) ?>°C</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-thermometer-half text-success card-icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Umidade Média do Solo</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($media_umid_solo_geral, 1) ?>%</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-droplet-half text-info card-icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saúde Geral Média</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($saude_geral_media) ?>%</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-heart-pulse-fill text-primary card-icon"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Média Diária de Parâmetros</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 350px;"><canvas id="myAreaChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Relatório Detalhado</h6>
                <div>
                    <span class="small me-2">Registros por página:</span>
                    <a href="?id_horta=<?= $id_horta_selecionada ?>&data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>&limit=25&page=1" class="btn btn-sm btn-<?= $limit == 25 ? 'primary' : 'outline-primary' ?>">25</a>
                    <a href="?id_horta=<?= $id_horta_selecionada ?>&data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>&limit=50&page=1" class="btn btn-sm btn-<?= $limit == 50 ? 'primary' : 'outline-primary' ?>">50</a>
                    <a href="?id_horta=<?= $id_horta_selecionada ?>&data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>&limit=100&page=1" class="btn btn-sm btn-<?= $limit == 100 ? 'primary' : 'outline-primary' ?>">100</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Data e Hora</th>
                                <th>Status Geral</th>
                                <th>Índice de Saúde</th>
                                <th style="width: 40%;">Detalhes dos Sensores</th>
                                <th>Chuva</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registros_tabela)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">Nenhum registro encontrado para esta horta no período selecionado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registros_tabela as $registro): ?>
                                    <?php $analise = analisarRegistro($registro, $regras_cultura); ?>
                                    <tr>
                                        <td class="align-middle"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($registro['MomentoCaptura']))) ?></td>
                                        <td class="align-middle text-center"><?= $analise['status_badge'] ?></td>
                                        <td class="align-middle">
                                            <div class="progress" title="Índice de Saúde: <?= $analise['saude'] ?>%" style="height: 22px; font-size: 0.85rem;">
                                                <div class="progress-bar fw-bold 
                                                <?php
                                                if ($analise['saude'] >= 85) echo 'bg-success';
                                                elseif ($analise['saude'] >= 65) echo 'bg-info';
                                                elseif ($analise['saude'] >= 50) echo 'bg-warning';
                                                else echo 'bg-danger';
                                                ?>" role="progressbar" style="width: <?= $analise['saude'] ?>%;"
                                                    aria-valuenow="<?= $analise['saude'] ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?= $analise['saude'] ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small">
                                            <?php foreach ($analise['detalhes'] as $detalhe): ?>
                                                <div class="d-flex justify-content-between border-bottom py-1">
                                                    <span>
                                                        <i class="bi <?= $detalhe['icone'] ?> <?= $detalhe['status_cor'] ?> me-2"></i>
                                                        <?= $detalhe['nome'] ?>:
                                                        <strong class="fw-bold"><?= $detalhe['valor'] ?></strong>
                                                    </span>
                                                    <span class="<?= $detalhe['status_cor'] ?>"><?= $detalhe['status_texto'] ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?= ($registro['Chuva'] == 1) ? '<i class="bi bi-cloud-rain-fill text-primary fs-4" title="Chovendo"></i>' : '<i class="bi bi-sun-fill text-secondary fs-4" title="Sem Chuva"></i>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="small">
                    Exibindo <?= count($registros_tabela) ?> de <?= $total_registros_encontrados ?> registros.
                </div>
                <nav>
                    <ul class="pagination pagination-sm m-0">
                        <?php
                        $total_pages = ceil($total_registros_encontrados / $limit);
                        for ($i = 1; $i <= $total_pages; $i++):
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?id_horta=<?= $id_horta_selecionada ?>&data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>

    <?php endif; // Fim do if que verifica se o usuário tem hortas 
    ?>
</div>

<?php require_once('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('myAreaChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const labels = <?= $chart_labels; ?>;
            const saudeData = <?= $chart_saude; ?>;
            const tempArData = <?= $chart_temp_ar; ?>;
            const umidSoloData = <?= $chart_umid_solo; ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Saúde Média (%)',
                        data: saudeData,
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y',
                    }, {
                        label: 'Temp. do Ar (°C)',
                        data: tempArData,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y1',
                    }, {
                        label: 'Umid. do Solo (%)',
                        data: umidSoloData,
                        borderColor: '#36b9cc',
                        backgroundColor: 'rgba(54, 185, 204, 0.1)',
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y2',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Saúde (%)'
                            },
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Temperatura (°C)'
                            },
                        },
                        y2: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Umidade (%)'
                            },
                        },
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.label.includes('Saúde')) {
                                        return label + context.parsed.y.toFixed(2) + '%';
                                    } else if (context.dataset.label.includes('Temp.')) {
                                        return label + context.parsed.y.toFixed(2) + '°C';
                                    } else if (context.dataset.label.includes('Umid.')) {
                                        return label + context.parsed.y.toFixed(2) + '%';
                                    }
                                    return label + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>