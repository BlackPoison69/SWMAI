<?php
// É importante que a sessão seja iniciada para o sistema de login funcionar.
// E que as funções de autenticação sejam incluídas.
require_once('../usuarios/logica-autenticacao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="stylesheet" href="../dist/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../IMG/Logo-Transparente.ico" type="image/x-icon">
    <title>SWMAI</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="shortcut icon" href="../IMG/white-Photoroom__2_-removebg-preview-_1_.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../dist/dashboard.css">
    <style>
        .username-truncate {
            display: inline-block;
            /* Necessário para aplicar max-width */
            max-width: 150px;
            /* Largura máxima que o nome pode ocupar. Ajuste se precisar. */
            white-space: nowrap;
            /* Impede que o texto quebre a linha */
            overflow: hidden;
            /* Esconde o texto que ultrapassar a largura máxima */
            text-overflow: ellipsis;
            /* Adiciona o "..." no final */
        }
    </style>
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a id="logo-swmai" class="navbar-brand  logoSuperior" href="../geral/index.php" style="color: green;  min-width: 140px;   text-align: center;">
            <i class="fas fa-seedling"></i>
            </i> SWMAI
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavItems" aria-controls="navbarNavItems" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavItems">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                if (autenticado()) {
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($pagina_atual == 'dashboard.php') {
                                                echo 'active';
                                            } ?>" href="../geral/dashboard.php">
                            <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($pagina_atual == 'minhas_culturas.php') {
                                                echo 'active';
                                            } ?>" href="../hortas/minhas_culturas.php">
                            <i class="bi bi-flower1 me-1"></i> Minhas Culturas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($pagina_atual == 'analises.php') {
                                                echo 'active';
                                            } ?>" href="../geral/analises.php">
                            <i class="bi bi-graph-up-arrow me-1"></i> Análises
                        </a>
                    </li>
                <?php
                }
                if (admin()) {
                ?>

                    <li class="nav-item">
                        <a class="nav-link <?php if ($pagina_atual == 'listagem_usuarios.php') {
                                                echo 'active';
                                            } ?>" href="../usuarios/listagem_usuarios.php">
                            <i class="bi bi-people-fill me-1"></i> Listar Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($pagina_atual == 'listagem_hortas.php') {
                                                echo 'active';
                                            } ?>" href="../hortas/listagem_hortas.php">
                            <i class="bi bi-card-list me-1"></i> Listar Hortas
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
            <div class="text-end">
                <?php if (!autenticado()) { ?>
                    <a href="../usuarios/formulario_cadastro_usuarios.php" class="btn btn-outline-info me-2">
                        <i class="bi bi-person-plus-fill me-1"></i>
                        Cadastrar
                    </a>
                    <a href="../usuarios/formulario_login.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Entrar
                    </a>
                <?php } else { ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center 
        <?php
                    // Adiciona a classe de cor diretamente no link
                    if (desenvolvedor()) {
                        echo 'text-danger';
                    } elseif (admin()) {
                        echo 'text-danger';
                    }
        ?>
    " id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <?php // A lógica interna agora só adiciona o peso da fonte e o ícone 
                            ?>
                            <?php if (desenvolvedor()) { ?>
                                <span class="fw-bold username-truncate" style="font-size: large;" title="<?= htmlspecialchars(nome_usuario()); ?>">
                                    👑 <?= htmlspecialchars(nome_usuario()); ?>
                                </span>
                            <?php } elseif (admin()) { ?>
                                <span class="fw-bold username-truncate" style="font-size: large;" title="<?= htmlspecialchars(nome_usuario()); ?>">
                                    #<?= htmlspecialchars(nome_usuario()); ?>
                                </span>
                            <?php } else { ?>
                                <span class="username-truncate" style="color: #e0e0e0; font-size: large" title="<?= htmlspecialchars(nome_usuario()); ?>">
                                    <?= htmlspecialchars(nome_usuario()); ?>
                                </span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                            <li><a class="dropdown-item" href="../usuarios/perfil.php"><i class="bi bi-person-fill me-2"></i>Meu Perfil</a></li>
                            <li><a class="dropdown-item" href="../usuarios/configuracoes.php"><i class="bi bi-gear-fill me-2"></i>Configurações</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="../usuarios/sair.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<body>
    <style>
        /* Importação de Fonte e Definições de Variáveis */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        :root {
            --primary-color: #2a9d8f;
            --secondary-color: #264653;
            --background-color: #f4f7f6;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
            --text-color: #333;
            --text-light: #666;
            --border-color: #e0e0e0;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            --sidebar-width: 240px;
            --sidebar-width-collapsed: 80px;
            --header-height: 60px;
        }

        /* Reset Básico e Estilos Globais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        /* Estrutura do Grid Principal */
        .grid-container {
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            grid-template-rows: var(--header-height) 1fr auto;
            grid-template-areas:
                "header header"
                "sidebar main"
                "sidebar footer";
            height: 100vh;
            transition: grid-template-columns 0.3s ease;
        }

        /* Estilos do Cabeçalho */
        .header {
            grid-area: header;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            z-index: 1000;
        }

        .header__left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            margin-right: 15px;
            color: var(--secondary-color);
        }

        .header__logo {
            display: flex;
            align-items: center;
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .header__logo i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        .header__search {
            display: flex;
            align-items: center;
            background-color: var(--background-color);
            padding: 8px 15px;
            border-radius: 20px;
            width: 40%;
        }

        .header__search i {
            color: var(--text-light);
        }

        .header__search input {
            border: none;
            background: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
            font-size: 14px;
        }

        .header__right {
            display: flex;
            align-items: center;
        }

        .header__button {
            background: none;
            border: none;
            font-size: 20px;
            margin-right: 20px;
            cursor: pointer;
            color: var(--text-light);
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile__avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-profile__info {
            display: flex;
            flex-direction: column;
        }

        .user-profile__name {
            font-weight: 500;
            font-size: 14px;
        }

        .user-profile__role {
            font-size: 12px;
            color: var(--text-light);
        }

        /* Estilos da Navegação Lateral (Sidebar) */
        .sidebar {
            grid-area: sidebar;
            background-color: var(--sidebar-bg);
            padding-top: 20px;
            overflow-y: auto;
            transition: width 0.3s ease;
            border-right: 1px solid var(--border-color);
        }

        .sidebar__nav ul li a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: var(--text-light);
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar__nav ul li a:hover,
        .sidebar__nav ul li a.active {
            background-color: #e8f5e9;
            color: var(--primary-color);
        }

        .sidebar__nav ul li a.active {
            border-left: 4px solid var(--primary-color);
            padding-left: 21px;
        }

        .sidebar__nav ul li a i {
            font-size: 18px;
            width: 30px;
            text-align: center;
            margin-right: 15px;
        }

        .sidebar__separator {
            height: 1px;
            background-color: var(--border-color);
            margin: 15px 25px;
        }

        /* Submenus */
        .submenu {
            padding-left: 30px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .has-submenu.open>.submenu {
            max-height: 500px;
            /* Ajuste conforme necessário */
        }

        .submenu-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .has-submenu.open.submenu-arrow {
            transform: rotate(180deg);
        }

        .submenu a {
            padding: 10px 25px 10px 0 !important;
        }

        .submenu a i {
            font-size: 14px !important;
            width: 20px !important;
            margin-right: 10px !important;
        }

        /* Estado Recolhido da Sidebar */
        .sidebar-collapsed.grid-container {
            grid-template-columns: var(--sidebar-width-collapsed) 1fr;
        }

        .sidebar-collapsed.sidebar__text,
        .sidebar-collapsed.submenu-arrow {
            display: none;
        }

        .sidebar-collapsed.sidebar__nav ul li a {
            justify-content: center;
            padding: 15px 10px;
        }

        .sidebar-collapsed.sidebar__nav ul li a i {
            margin-right: 0;
        }

        .sidebar-collapsed.submenu {
            display: none;
            /* Esconde submenus no modo recolhido */
        }

        /* Conteúdo Principal */
        .main-content {
            grid-area: main;
            padding: 30px;
            overflow-y: auto;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        /* Rodapé */
        .footer {
            grid-area: footer;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: var(--text-light);
            border-top: 1px solid var(--border-color);
        }

        /* Navegação Inferior (Mobile) */
        .mobile-tab-bar {
            display: none;
            /* Escondido por padrão */
        }

        /* --- Media Queries para Responsividade --- */

        /* Tablets (até 1024px) */
        @media (max-width: 1024px) {
            .grid-container {
                grid-template-columns: var(--sidebar-width-collapsed) 1fr;
            }

            .sidebar__text,
            .submenu-arrow {
                display: none;
            }

            .sidebar__nav ul li a {
                justify-content: center;
                padding: 15px 10px;
            }

            .sidebar__nav ul li a i {
                margin-right: 0;
            }

            .submenu {
                display: none;
            }

            .header__search {
                width: 50%;
            }

            .user-profile__info {
                display: none;
            }
        }

        /* Mobile (até 768px) */
        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "header"
                    "main"
                    "footer";
            }

            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                width: var(--sidebar-width);
                height: 100%;
                z-index: 1001;
                transition: left 0.3s ease;
            }

            .sidebar.open {
                left: 0;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            }

            .sidebar__text,
            .submenu-arrow {
                display: inline-block;
            }

            .sidebar__nav ul li a {
                justify-content: flex-start;
            }

            .sidebar__nav ul li a i {
                margin-right: 15px;
            }

            .header__logo span {
                display: none;
            }

            .header__search {
                display: none;
            }

            .main-content {
                padding: 20px;
                padding-bottom: 80px;
                /* Espaço para a tab bar */
            }

            .mobile-tab-bar {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 60px;
                background-color: #fff;
                box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
                justify-content: space-around;
                align-items: center;
                z-index: 999;
            }

            .tab-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                font-size: 12px;
                color: var(--text-light);
            }

            .tab-item i {
                font-size: 20px;
                margin-bottom: 4px;
            }

            .tab-item.active {
                color: var(--primary-color);
            }



        }

        .blocoPrincipal {
            min-height: 70vh;
        }
    </style>
    <main class="blocoPrincipal">