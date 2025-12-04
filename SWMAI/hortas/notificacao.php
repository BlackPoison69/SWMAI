<?php
session_start();
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('../geral/header.php');

// 1. Pega os parâmetros da URL
$tipo = $_GET['tipo'] ?? 'info';
$mensagem = $_GET['mensagem'] ?? 'Nenhuma notificação foi especificada.';
$redirect_url = $_GET['redirecionar'] ?? '../geral/dashboard.php';
$titulo = $_GET['titulo'] ?? 'Notificação do Sistema';

// 2. Define o estilo do alerta com base no tipo
switch ($tipo) {
    case 'success':
        $cor = 'alert-success';
        $icone = 'bi-check-circle-fill';
        break;
    case 'danger':
        $cor = 'alert-danger';
        $icone = 'bi-x-octagon-fill';
        break;
    case 'warning':
        $cor = 'alert-warning';
        $icone = 'bi-exclamation-triangle-fill';
        break;
    default:
        $cor = 'alert-info';
        $icone = 'bi-info-circle-fill';
        break;
}

// 3. Usa o CSS para centralizar a caixa de alerta
?>

<style>
    .notification-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        text-align: center;
    }

    .notification-box {
        max-width: 600px;
        width: 100%;
    }
</style>

<div class="container notification-container">
    <div class="notification-box">
        <div class="alert <?= htmlspecialchars($cor) ?> d-flex align-items-center" role="alert">
            <i class="bi <?= htmlspecialchars($icone) ?> flex-shrink-0 me-2 fs-4"></i>
            <div>
                <h4 class="alert-heading"><?= htmlspecialchars($titulo) ?></h4>
                <p><?= htmlspecialchars($mensagem) ?></p>
                <hr>
                <p class="mb-0">Você será redirecionado em <span id="countdown">5</span> segundos.</p>
                <a href="<?= htmlspecialchars($redirect_url) ?>" class="btn btn-outline-secondary mt-2">Voltar agora</a>
            </div>
        </div>
    </div>
</div>

<script>
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');

    // Atualiza o contador a cada segundo
    const interval = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(interval);
            // Redireciona quando o contador chega a zero
            window.location.href = "<?= htmlspecialchars($redirect_url) ?>";
        }
    }, 1000);
</script>

<?php
require_once('../geral/footer.php');
?>