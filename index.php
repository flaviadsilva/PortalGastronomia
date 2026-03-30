<?php
$pageTitle = 'Inicio';
require_once 'config/database.php';
require_once 'dao/NoticiaDAO.php';
require_once 'dao/CategoriaDAO.php';

$noticiaDAO = new NoticiaDAO($conn);
$categoriaDAO = new CategoriaDAO($conn);

$destaques = $noticiaDAO->listarDestaques(3);
$noticias = $noticiaDAO->listarTodas(12);
$maisLidas = $noticiaDAO->maisLidas(5);
$categorias = $categoriaDAO->listarComContagem();

include 'includes/header.php';
?>

<!-- HERO / DESTAQUES -->
<?php if (count($destaques) > 0): ?>
<section class="hero-section">
    <div class="hero-grid">
        <!-- Destaque principal -->
        <a href="noticia.php?id=<?php echo $destaques[0]['id']; ?>" class="hero-main">
            <?php if (!empty($destaques[0]['imagem'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($destaques[0]['imagem']); ?>" alt="<?php echo htmlspecialchars($destaques[0]['titulo']); ?>">
            <?php else: ?>
                <div class="img-placeholder"><i class="fa-solid fa-utensils"></i></div>
            <?php endif; ?>
            <div class="hero-overlay">
                <?php if (!empty($destaques[0]['categoria_nome'])): ?>
                    <span class="badge" style="background:<?php echo $destaques[0]['categoria_cor'] ?? 'var(--vermelho)'; ?>"><?php echo htmlspecialchars($destaques[0]['categoria_nome']); ?></span>
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($destaques[0]['titulo']); ?></h2>
                <p><?php echo htmlspecialchars($destaques[0]['resumo'] ?? substr(strip_tags($destaques[0]['noticia']), 0, 150)); ?></p>
                <div class="meta">
                    <span><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($destaques[0]['autor_nome']); ?></span>
                    <span><i class="fa-solid fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($destaques[0]['data'])); ?></span>
                </div>
            </div>
        </a>

        <!-- Destaques laterais -->
        <?php if (count($destaques) > 1): ?>
        <div class="hero-side">
            <?php for ($i = 1; $i < min(3, count($destaques)); $i++): ?>
            <a href="noticia.php?id=<?php echo $destaques[$i]['id']; ?>" class="hero-side-item">
                <?php if (!empty($destaques[$i]['imagem'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($destaques[$i]['imagem']); ?>" alt="<?php echo htmlspecialchars($destaques[$i]['titulo']); ?>">
                <?php else: ?>
                    <div class="img-placeholder img-placeholder-sm"><i class="fa-solid fa-utensils"></i></div>
                <?php endif; ?>
                <div class="hero-overlay">
                    <?php if (!empty($destaques[$i]['categoria_nome'])): ?>
                        <span class="badge" style="background:<?php echo $destaques[$i]['categoria_cor'] ?? 'var(--vermelho)'; ?>"><?php echo htmlspecialchars($destaques[$i]['categoria_nome']); ?></span>
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($destaques[$i]['titulo']); ?></h2>
                </div>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- CONTEUDO PRINCIPAL + SIDEBAR -->
<div class="content-layout">
    <div class="content-main">
        <div class="section-header">
            <h2><i class="fa-solid fa-newspaper"></i> Ultimas Noticias</h2>
        </div>

        <?php if (count($noticias) > 0): ?>
            <div class="news-grid">
                <?php foreach ($noticias as $noticia): ?>
                    <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="news-card">
                        <div class="news-card-img">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <?php else: ?>
                                <div class="img-placeholder img-placeholder-sm"><i class="fa-solid fa-utensils"></i></div>
                            <?php endif; ?>
                            <?php if (!empty($noticia['categoria_nome'])): ?>
                                <span class="news-card-badge" style="background:<?php echo $noticia['categoria_cor'] ?? 'var(--vermelho)'; ?>">
                                    <?php echo htmlspecialchars($noticia['categoria_nome']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="news-card-body">
                            <h3><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($noticia['resumo'] ?? substr(strip_tags($noticia['noticia']), 0, 120)); ?>...</p>
                            <div class="news-card-meta">
                                <span class="author"><i class="fa-solid fa-user-pen"></i> <?php echo htmlspecialchars($noticia['autor_nome']); ?></span>
                                <span><i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($noticia['data'])); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-bowl-food"></i>
                <h3>Nenhuma noticia ainda</h3>
                <p>Seja o primeiro a publicar uma noticia gastronomica!</p>
                <a href="nova_noticia.php" class="btn btn-laranja"><i class="fa-solid fa-pen"></i> Publicar Noticia</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <!-- Categorias -->
        <div class="sidebar-widget">
            <h3><i class="fa-solid fa-tags"></i> Categorias</h3>
            <ul class="cat-list">
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="categoria.php?slug=<?php echo $cat['slug']; ?>">
                            <span class="cat-icon">
                                <i class="fa-solid <?php echo $cat['icone']; ?>" style="color:<?php echo $cat['cor']; ?>"></i>
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </span>
                            <span class="cat-count"><?php echo $cat['total_noticias']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Mais Lidas -->
        <?php if (count($maisLidas) > 0): ?>
        <div class="sidebar-widget">
            <h3><i class="fa-solid fa-fire"></i> Mais Lidas</h3>
            <?php foreach ($maisLidas as $idx => $ml): ?>
                <a href="noticia.php?id=<?php echo $ml['id']; ?>" class="news-list-item">
                    <span class="list-number"><?php echo $idx + 1; ?></span>
                    <div>
                        <h4><?php echo htmlspecialchars($ml['titulo']); ?></h4>
                        <span class="list-meta">
                            <i class="fa-solid fa-eye"></i> <?php echo $ml['visualizacoes']; ?> visualizacoes
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Newsletter fake -->
        <div class="sidebar-widget" style="background: linear-gradient(135deg, var(--vermelho), var(--laranja)); color: white;">
            <h3 style="color: white; border-color: rgba(255,255,255,0.3);"><i class="fa-solid fa-envelope"></i> Newsletter</h3>
            <p style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 12px;">Receba as melhores noticias gastronomicas no seu email!</p>
            <div style="display:flex; gap:5px;">
                <input type="email" placeholder="Seu email..." style="flex:1; padding:10px; border:none; border-radius:8px; font-size:0.85rem;">
                <button class="btn btn-sm" style="background:var(--preto);color:white;white-space:nowrap;">Assinar</button>
            </div>
        </div>
    </aside>
</div>

<?php include 'includes/footer.php'; ?>
