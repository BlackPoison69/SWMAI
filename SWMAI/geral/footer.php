</main>

<style>
    .footer-social-icon {
        font-size: 1.5rem;
        transition: color 0.2s ease-in-out, transform 0.2s ease-in-out;
    }

    .footer-social-icon:hover {
        color: #ffffff !important;
        /* !important para sobrescrever a classe .text-white-50 */
        transform: scale(1.1);
    }

    .footer-dev-photo {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }

    .footer-nav a {
        text-decoration: none;
        transition: color 0.2s ease-in-out;
    }

    .footer-nav a:hover {
        color: #ffffff !important;
        text-decoration: underline;
    }
</style>

<footer class="footer mt-auto py-3 bg-dark text-white">
    <div class="container">
        <div class="row py-4 g-4">

            <div class="col-lg-4 col-md-12 text-center text-lg-start">
                <a href="index.php" class="d-inline-flex align-items-center mb-3 text-white text-decoration-none">
                    <i class="bi bi-leaf-fill me-2" style="font-size: 2rem; color: green;"></i>
                    <span class="fs-4 vt323-regular">SWMAI</span>
                </a>
                <p class="mb-3 text-white-50">Seu parceiro em agricultura inteligente.</p>
                <ul class="list-unstyled d-flex justify-content-center justify-content-lg-start">
                    <li class="me-3">
                        <a class="text-white-50" href="#" title="Twitter"><i class="bi bi-twitter footer-social-icon"></i></a>
                    </li>
                    <li class="me-3">
                        <a class="text-white-50" href="#" title="Instagram"><i class="bi bi-instagram footer-social-icon"></i></a>
                    </li>
                    <li>
                        <a class="text-white-50" href="#" title="GitHub"><i class="bi bi-github footer-social-icon"></i></a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6 text-center text-lg-start">
                <h5 class="mb-3">Feito por:</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="d-inline-flex align-items-center mb-2">
                        <img src="../IMG/Gustavo.png" class="rounded-circle me-2 footer-dev-photo" alt="Foto de Gustavo">
                        <span>Gustavo Veronezi de Carvalho</span>
                    </li>
                    <li class="d-inline-flex align-items-center">
                        <img src="../IMG/Julio.png" class="rounded-circle me-2 footer-dev-photo" alt="Foto de Julio">
                        <span>Julio Cesar Teiche Fraioli</span>
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6 text-center text-lg-start">
                <h5 class="mb-3">Navegação</h5>
                <ul class="list-unstyled footer-nav">
                    <li><a href="index.php" class="text-white-50">Início</a></li>
                    <li><a href="dashboard.php" class="text-white-50">Dashboard</a></li>
                    <li><a href="minhas_culturas.php" class="text-white-50">Minhas Culturas</a></li>
                    <li><a href="analises.php" class="text-white-50">Análises</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-3 text-white-50">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="text-center text-md-start mb-2 mb-md-0">&copy; <?php echo date("Y"); ?> SWMAI. Todos os direitos reservados.</p>
            <a href="#" class="text-white-50 text-decoration-none">Voltar ao Topo <i class="bi bi-arrow-up-circle-fill"></i></a>
        </div>
    </div>
</footer>

<script>
    // Procura pelo alerta na página
    const alertElement = document.getElementById('auto-fade-alert');

    // Se o alerta existir...
    if (alertElement) {
        // ...espera 4 segundos (4000 milissegundos) e então o fecha suavemente.
        setTimeout(() => {
            new bootstrap.Alert(alertElement).close();
        }, 4000);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../dist/dashboard.js"></script>

</body>

</html>