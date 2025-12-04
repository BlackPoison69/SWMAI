<?php

// Neste arquivo, definimos as condições ideais para cada tipo de cultura,
// e as funções de análise para calcular o score e o status.

function getParametrosIdeais()
{
    return [
        'Geral' => [
            'UmidSolo'      => ['ideal' => 35, 'alerta_min' => 15, 'critico_min' => 5, 'alerta_max' => 70, 'critico_max' => 90, 'peso' => 4],
            'TempAr'        => ['ideal' => 26, 'alerta_min' => 16, 'critico_min' => 8, 'alerta_max' => 32, 'critico_max' => 38, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 750, 'alerta_min' => 500, 'critico_min' => 300, 'alerta_max' => 1100, 'critico_max' => 1400, 'peso' => 2],
            'UmidAr'        => ['ideal' => 35, 'alerta_min' => 15, 'critico_min' => 10, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 1],
        ],
        'Abobrinha' => [
            'UmidSolo'      => ['ideal' => 60, 'alerta_min' => 50, 'critico_min' => 40, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 3],
            'TempAr'        => ['ideal' => 25, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 32, 'critico_max' => 35, 'peso' => 4],
            'Luminosidade'  => ['ideal' => 1150, 'alerta_min' => 900, 'critico_min' => 700, 'alerta_max' => 1500, 'critico_max' => 1700, 'peso' => 4],
            'UmidAr'        => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 2],
        ],
        'Agrião' => [
            'UmidSolo'      => ['ideal' => 90, 'alerta_min' => 80, 'critico_min' => 70, 'alerta_max' => 95, 'critico_max' => 100, 'peso' => 4],
            'TempAr'        => ['ideal' => 20, 'alerta_min' => 15, 'critico_min' => 12, 'alerta_max' => 25, 'critico_max' => 28, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 750, 'alerta_min' => 600, 'critico_min' => 450, 'alerta_max' => 900, 'critico_max' => 1100, 'peso' => 3],
            'UmidAr'        => ['ideal' => 75, 'alerta_min' => 65, 'critico_min' => 55, 'alerta_max' => 85, 'critico_max' => 95, 'peso' => 2],
        ],
        'Alface' => [
            'UmidSolo'      => ['ideal' => 80, 'alerta_min' => 70, 'critico_min' => 60, 'alerta_max' => 85, 'critico_max' => 95, 'peso' => 4],
            'TempAr'        => ['ideal' => 22, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 28, 'critico_max' => 30, 'peso' => 4],
            'Luminosidade'  => ['ideal' => 725, 'alerta_min' => 550, 'critico_min' => 400, 'alerta_max' => 900, 'critico_max' => 1100, 'peso' => 3],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 85, 'critico_max' => 95, 'peso' => 2],
        ],
        'Almeirão' => [
            'UmidSolo'      => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 4],
            'TempAr'        => ['ideal' => 21, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 28, 'critico_max' => 32, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 750, 'alerta_min' => 600, 'critico_min' => 450, 'alerta_max' => 900, 'critico_max' => 1100, 'peso' => 3],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 2],
        ],
        'Batata-doce' => [
            'UmidSolo'      => ['ideal' => 55, 'alerta_min' => 45, 'critico_min' => 35, 'alerta_max' => 65, 'critico_max' => 75, 'peso' => 4],
            'TempAr'        => ['ideal' => 25, 'alerta_min' => 20, 'critico_min' => 18, 'alerta_max' => 30, 'critico_max' => 35, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 1150, 'alerta_min' => 900, 'critico_min' => 700, 'alerta_max' => 1500, 'critico_max' => 1700, 'peso' => 3],
            'UmidAr'        => ['ideal' => 60, 'alerta_min' => 50, 'critico_min' => 40, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 2],
        ],
        'Brócolis' => [
            'UmidSolo'      => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 3],
            'TempAr'        => ['ideal' => 20, 'alerta_min' => 15, 'critico_min' => 12, 'alerta_max' => 28, 'critico_max' => 32, 'peso' => 4],
            'Luminosidade'  => ['ideal' => 1000, 'alerta_min' => 800, 'critico_min' => 600, 'alerta_max' => 1300, 'critico_max' => 1500, 'peso' => 3],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 2],
        ],
        'Cebolinha' => [
            'UmidSolo'      => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 4],
            'TempAr'        => ['ideal' => 20, 'alerta_min' => 15, 'critico_min' => 12, 'alerta_max' => 28, 'critico_max' => 32, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 650, 'alerta_min' => 500, 'critico_min' => 350, 'alerta_max' => 800, 'critico_max' => 1000, 'peso' => 3],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 2],
        ],
        'Cenoura' => [
            'UmidSolo'      => ['ideal' => 40, 'alerta_min' => 35, 'critico_min' => 20, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 4],
            'TempAr'        => ['ideal' => 20, 'alerta_min' => 10, 'critico_min' => 6, 'alerta_max' => 30, 'critico_max' => 35, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 500, 'alerta_min' => 300, 'critico_min' => 100, 'alerta_max' => 1000, 'critico_max' => 1200, 'peso' => 2],
            'UmidAr'        => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 1],
        ],
        'Coentro' => [
            'UmidSolo'      => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 3],
            'TempAr'        => ['ideal' => 22, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 27, 'critico_max' => 30, 'peso' => 2],
            'Luminosidade'  => ['ideal' => 750, 'alerta_min' => 600, 'critico_min' => 450, 'alerta_max' => 900, 'critico_max' => 1100, 'peso' => 3],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 1],
        ],
        'Couve' => [
            'UmidSolo'      => ['ideal' => 75, 'alerta_min' => 65, 'critico_min' => 55, 'alerta_max' => 85, 'critico_max' => 95, 'peso' => 4],
            'TempAr'        => ['ideal' => 21, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 28, 'critico_max' => 32, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 1150, 'alerta_min' => 900, 'critico_min' => 700, 'alerta_max' => 1400, 'critico_max' => 1600, 'peso' => 3],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 2],
        ],
        'Mandioca' => [
            'UmidSolo'      => ['ideal' => 50, 'alerta_min' => 40, 'critico_min' => 30, 'alerta_max' => 60, 'critico_max' => 70, 'peso' => 2],
            'TempAr'        => ['ideal' => 28, 'alerta_min' => 25, 'critico_min' => 20, 'alerta_max' => 32, 'critico_max' => 35, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 1250, 'alerta_min' => 1000, 'critico_min' => 800, 'alerta_max' => 1500, 'critico_max' => 1800, 'peso' => 4],
            'UmidAr'        => ['ideal' => 60, 'alerta_min' => 50, 'critico_min' => 40, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 1],
        ],
        'Morango' => [
            'UmidSolo'      => ['ideal' => 60, 'alerta_min' => 50, 'critico_min' => 40, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 4],
            'TempAr'        => ['ideal' => 20, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 25, 'critico_max' => 28, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 1000, 'alerta_min' => 800, 'critico_min' => 600, 'alerta_max' => 1200, 'critico_max' => 1400, 'peso' => 3],
            'UmidAr'        => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 2],
        ],
        'Pepino' => [
            'UmidSolo'      => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 4],
            'TempAr'        => ['ideal' => 28, 'alerta_min' => 20, 'critico_min' => 18, 'alerta_max' => 35, 'critico_max' => 38, 'peso' => 4],
            'Luminosidade'  => ['ideal' => 1150, 'alerta_min' => 900, 'critico_min' => 700, 'alerta_max' => 1400, 'critico_max' => 1600, 'peso' => 4],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 2],
        ],
        'Pimentão' => [
            'UmidSolo'      => ['ideal' => 60, 'alerta_min' => 50, 'critico_min' => 40, 'alerta_max' => 70, 'critico_max' => 80, 'peso' => 3],
            'TempAr'        => ['ideal' => 25, 'alerta_min' => 20, 'critico_min' => 18, 'alerta_max' => 30, 'critico_max' => 35, 'peso' => 4],
            'Luminosidade'  => ['ideal' => 1000, 'alerta_min' => 800, 'critico_min' => 600, 'alerta_max' => 1300, 'critico_max' => 1500, 'peso' => 4],
            'UmidAr'        => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 2],
        ],
        'Rabanete' => [
            'UmidSolo'      => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 3],
            'TempAr'        => ['ideal' => 15, 'alerta_min' => 10, 'critico_min' => 8, 'alerta_max' => 22, 'critico_max' => 25, 'peso' => 4],
            'Luminosidade'  => ['ideal' => 900, 'alerta_min' => 700, 'critico_min' => 500, 'alerta_max' => 1100, 'critico_max' => 1300, 'peso' => 3],
            'UmidAr'        => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 2],
        ],
        'Salsa' => [
            'UmidSolo'      => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 3],
            'TempAr'        => ['ideal' => 20, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 25, 'critico_max' => 28, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 650, 'alerta_min' => 500, 'critico_min' => 350, 'alerta_max' => 800, 'critico_max' => 1000, 'peso' => 2],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 1],
        ],
        'Tomate' => [
            'UmidSolo'      => ['ideal' => 65, 'alerta_min' => 55, 'critico_min' => 45, 'alerta_max' => 75, 'critico_max' => 85, 'peso' => 4],
            'TempAr'        => ['ideal' => 24, 'alerta_min' => 18, 'critico_min' => 15, 'alerta_max' => 29, 'critico_max' => 33, 'peso' => 3],
            'Luminosidade'  => ['ideal' => 1000, 'alerta_min' => 800, 'critico_min' => 600, 'alerta_max' => 1300, 'critico_max' => 1500, 'peso' => 2],
            'UmidAr'        => ['ideal' => 70, 'alerta_min' => 60, 'critico_min' => 50, 'alerta_max' => 80, 'critico_max' => 90, 'peso' => 1],
        ],
    ];
}


// Função que calcula um score de 0-100 para um único parâmetro
function calcularScoreParametro($valor, $ideal, $alerta_min, $critico_min, $alerta_max, $critico_max)
{
    // Se o valor estiver exatamente na faixa ideal
    if ($valor == $ideal) {
        return 100;
    }

    // Se o valor está entre a faixa ideal e a faixa de alerta
    if (($valor >= $alerta_min && $valor < $ideal) || ($valor > $ideal && $valor <= $alerta_max)) {
        $distancia_do_ideal = abs($valor - $ideal);
        $alcance_alerta = max(abs($ideal - $alerta_min), abs($ideal - $alerta_max));
        if ($alcance_alerta == 0) return 100;
        $score = 100 - ($distancia_do_ideal / $alcance_alerta) * 50;
        return round($score);
    }

    // Se o valor está na faixa crítica
    if (($valor >= $critico_min && $valor < $alerta_min) || ($valor > $alerta_max && $valor <= $critico_max)) {
        $distancia_do_alerta = abs($valor - (($valor < $alerta_min) ? $alerta_min : $alerta_max));
        $alcance_critico = abs($alerta_min - $critico_min) + abs($alerta_max - $critico_max); // A soma dos dois lados
        if ($alcance_critico == 0) return 0;
        $score = 50 - ($distancia_do_alerta / $alcance_critico) * 50;
        return round(max(0, $score));
    }

    // Se o valor está completamente fora da faixa crítica
    return 0;
}

// --- NOVA FUNÇÃO DE ANÁLISE ---
// Esta função recebe um registro do banco e as regras da cultura,
// e retorna um array com textos, cores e ícones prontos para exibição.
function analisarRegistro($registro, $regras_cultura)
{
    $analise = ['detalhes' => [], 'problemas' => []];
    $saude_pontos = 0;
    $peso_total_saude = 0;

    $parametros_saude = [
        'TempAr' => 'Temperatura do Ar',
        'UmidAr' => 'Umidade do Ar',
        'UmidSolo' => 'Umidade do Solo'
    ];

    $parametros_outros = [
        'Luminosidade' => 'Luminosidade'
    ];

    foreach ($parametros_saude as $coluna => $nome_param) {
        if (!isset($registro[$coluna]) || !isset($regras_cultura[$coluna])) continue;

        $valor = $registro[$coluna];
        $regras = $regras_cultura[$coluna];
        $peso = $regras['peso'];
        $peso_total_saude += $peso;

        $score = calcularScoreParametro($valor, $regras['ideal'], $regras['alerta_min'], $regras['critico_min'], $regras['alerta_max'], $regras['critico_max']);
        $saude_pontos += $score * $peso;

        $detalhe = [
            'nome' => $nome_param,
            'valor' => $valor,
            'status_texto' => 'Ideal',
            'status_cor' => 'text-success',
            'icone' => 'bi-check-circle-fill'
        ];

        if ($valor < $regras['critico_min'] || $valor > $regras['critico_max']) {
            $detalhe['status_texto'] = ($valor < $regras['critico_min']) ? 'Muito Baixo' : 'Muito Alto';
            $detalhe['status_cor'] = 'text-danger fw-bold';
            $detalhe['icone'] = 'bi-x-octagon-fill';
            $analise['problemas'][] = $nome_param . ' em nível crítico (' . $valor . ')';
        } elseif ($valor < $regras['alerta_min'] || $valor > $regras['alerta_max']) {
            $detalhe['status_texto'] = ($valor < $regras['alerta_min']) ? 'Baixo' : 'Alto';
            $detalhe['status_cor'] = 'text-warning';
            $detalhe['icone'] = 'bi-exclamation-triangle-fill';
            $analise['problemas'][] = $nome_param . ' em alerta (' . $valor . ')';
        }
        $analise['detalhes'][] = $detalhe;
    }

    foreach ($parametros_outros as $coluna => $nome_param) {
        if (!isset($registro[$coluna]) || !isset($regras_cultura[$coluna])) continue;

        $valor = $registro[$coluna];
        $regras = $regras_cultura[$coluna];

        $detalhe = [
            'nome' => $nome_param,
            'valor' => $valor,
            'status_texto' => 'Ideal',
            'status_cor' => 'text-success',
            'icone' => 'bi-check-circle-fill'
        ];

        if ($valor < $regras['critico_min'] || $valor > $regras['critico_max']) {
            $detalhe['status_texto'] = ($valor < $regras['critico_min']) ? 'Muito Baixo' : 'Muito Alto';
            $detalhe['status_cor'] = 'text-danger fw-bold';
            $detalhe['icone'] = 'bi-x-octagon-fill';
            $analise['problemas'][] = $nome_param . ' em nível crítico (' . $valor . ')';
        } elseif ($valor < $regras['alerta_min'] || $valor > $regras['alerta_max']) {
            $detalhe['status_texto'] = ($valor < $regras['alerta_min']) ? 'Baixo' : 'Alto';
            $detalhe['status_cor'] = 'text-warning';
            $detalhe['icone'] = 'bi-exclamation-triangle-fill';
            $analise['problemas'][] = $nome_param . ' em alerta (' . $valor . ')';
        }
        $analise['detalhes'][] = $detalhe;
    }

    $saude_planta = ($peso_total_saude > 0) ? round(($saude_pontos / $peso_total_saude), 2) : 100;

    if (empty($analise['problemas'])) {
        $analise['diagnostico_resumo'] = "Todos os parâmetros estão dentro da faixa ideal.";
    } else {
        $analise['diagnostico_resumo'] = implode(' ', $analise['problemas']);
    }

    if ($saude_planta >= 85) {
        $analise['status_badge'] = '<span class="badge bg-success">Excelente</span>';
    } elseif ($saude_planta >= 65) {
        $analise['status_badge'] = '<span class="badge bg-info">Bom</span>';
    } elseif ($saude_planta >= 50) {
        $analise['status_badge'] = '<span class="badge bg-warning text-dark">Atenção</span>';
    } else {
        $analise['status_badge'] = '<span class="badge bg-danger">Crítico</span>';
    }

    $analise['saude'] = $saude_planta;

    return $analise;
}
