<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../dao/CategoriaDAO.php';

$categoriaDAO = new CategoriaDAO($conn);
$categoriasMenu = $categoriaDAO->listarTodas();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Portal Gastronomia' : 'Portal Gastronomia'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span><i class="fa-solid fa-fire-burner"></i> <?php echo date('d/m/Y'); ?></span>
            <span class="separator">|</span>
            <span>Seu portal de gastronomia</span>
        </div>
        <div class="top-bar-right">
            <?php if (estaLogado()): ?>
                <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Painel</a>
                <a href="nova_noticia.php"><i class="fa-solid fa-pen-nib"></i> Publicar</a>
                <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Entrar</a>
                <a href="cadastro.php"><i class="fa-solid fa-user-plus"></i> Cadastrar</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Header -->
<header class="main-header">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-utensils"></i>
            <div>
                <h1>Portal <span>Gastronomia</span></h1>
                <p class="logo-tagline">Sabor em cada noticia</p>
            </div>
        </a>
        <div class="header-search">
            <form action="buscar.php" method="GET">
                <input type="text" name="q" placeholder="Buscar noticias..." required>
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>

<!-- Navigation -->
<nav class="main-nav" id="mainNav">
    <div class="container">
        <ul>
            <li><a href="index.php" class="nav-home"><i class="fa-solid fa-house"></i> Inicio</a></li>
            <?php foreach ($categoriasMenu as $cat): ?>
                <li>
                    <a href="categoria.php?slug=<?php echo $cat['slug']; ?>">
                        <i class="fa-solid <?php echo $cat['icone']; ?>"></i>
                        <?php echo htmlspecialchars($cat['nome']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<main class="main-content">
    <div class="container">
