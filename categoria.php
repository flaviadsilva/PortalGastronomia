<?php
require_once 'config/database.php';
require_once 'dao/NoticiaDAO.php';
require_once 'dao/CategoriaDAO.php';

$categoriaDAO = new CategoriaDAO($conn);
$noticiaDAO = new NoticiaDAO($conn);

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: index.php');
    exit;
}

$categoria = $categoriaDAO->buscarPorSlug($slug);
if (!$categoria) {
    header('Location: index.php');
    exit;
}

$pageTitle = $categoria['nome'];

// Paginacao
$porPagina = 12;
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$offset = ($pagina - 1) * $porPagina;
$totalNoticias = $noticiaDAO->contarPorCategoria($categoria['id']);
$totalPaginas = ceil($totalNoticias / $porPagina);

$noticias = $noticiaDAO->listarPorCategoria($categoria['id'], $porPagina, $offset);

include 'includes/header.php';
?>

<!-- Categoria Header -->
<div class="category-header">
    <div class="cat-icon-big" style="background:<?php echo $categoria['cor']; ?>">
        <i class="fa-solid <?php echo $categoria['icone']; ?>"></i>
    </div>
    <div>
        <h1><?php echo htmlspecialchars($categoria['nome']); ?></h1>
        <p><?php echo htmlspecialchars($categoria['descricao'] ?? ''); ?> &mdash; <?php echo $totalNoticias; ?> noticia(s)</p>
    </div>
</div>

<?php if (count($noticias) > 0): ?>
    <div class="news-grid">
        <?php foreach ($noticias as $noticia): ?>
            <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="news-card">
                <div class="news-card-img">
                    <?php if (!empty($noticia['imagem'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="">
                    <?php else: ?>
                        <div class="img-placeholder img-placeholder-sm"><i class="fa-solid fa-utensils"></i></div>
                    <?php endif; ?>
                    <span class="news-card-badge" style="background:<?php echo $categoria['cor']; ?>">
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </span>
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

    <!-- Paginacao -->
    <?php if ($totalPaginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina > 1): ?>
                <a href="?slug=<?php echo $slug; ?>&pagina=<?php echo $pagina - 1; ?>"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i == $pagina): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?slug=<?php echo $slug; ?>&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagina < $totalPaginas): ?>
                <a href="?slug=<?php echo $slug; ?>&pagina=<?php echo $pagina + 1; ?>"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="fa-solid <?php echo $categoria['icone']; ?>"></i>
        <h3>Nenhuma noticia nesta categoria</h3>
        <p>Seja o primeiro a publicar em <?php echo htmlspecialchars($categoria['nome']); ?>!</p>
        <a href="nova_noticia.php" class="btn btn-laranja"><i class="fa-solid fa-pen"></i> Publicar Noticia</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
