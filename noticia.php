<?php
require_once 'config/database.php';
require_once 'dao/NoticiaDAO.php';
require_once 'dao/CategoriaDAO.php';

$noticiaDAO = new NoticiaDAO($conn);

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$noticia = $noticiaDAO->buscarPorId($id);
if (!$noticia) {
    header('Location: index.php');
    exit;
}

$noticiaDAO->incrementarVisualizacao($id);
$pageTitle = $noticia['titulo'];

$maisLidas = $noticiaDAO->maisLidas(5);

include 'includes/header.php';
?>

<div class="content-layout">
    <article class="content-main">
        <div class="article-header">
            <div class="breadcrumb">
                <a href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <?php if (!empty($noticia['categoria_nome'])): ?>
                    <a href="categoria.php?slug=<?php echo $noticia['categoria_slug']; ?>"><?php echo htmlspecialchars($noticia['categoria_nome']); ?></a>
                    <i class="fa-solid fa-chevron-right"></i>
                <?php endif; ?>
                <span><?php echo htmlspecialchars(mb_substr($noticia['titulo'], 0, 40)); ?>...</span>
            </div>

            <?php if (!empty($noticia['categoria_nome'])): ?>
                <a href="categoria.php?slug=<?php echo $noticia['categoria_slug']; ?>" class="badge" style="background:<?php echo $noticia['categoria_cor'] ?? 'var(--vermelho)'; ?>; color:white; display:inline-block; padding:5px 14px; border-radius:20px; font-size:0.8rem; font-weight:600; margin-bottom:12px;">
                    <i class="fa-solid <?php echo $noticia['categoria_icone']; ?>"></i>
                    <?php echo htmlspecialchars($noticia['categoria_nome']); ?>
                </a>
            <?php endif; ?>

            <h1><?php echo htmlspecialchars($noticia['titulo']); ?></h1>

            <?php if (!empty($noticia['resumo'])): ?>
                <p class="article-resumo"><?php echo htmlspecialchars($noticia['resumo']); ?></p>
            <?php endif; ?>

            <div class="article-meta">
                <div class="author-info">
                    <div class="author-avatar">
                        <?php if (!empty($noticia['autor_foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($noticia['autor_foto']); ?>" alt="">
                        <?php else: ?>
                            <?php echo strtoupper(mb_substr($noticia['autor_nome'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($noticia['autor_nome']); ?></strong>
                    </div>
                </div>
                <span><i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y \a\s H:i', strtotime($noticia['data'])); ?></span>
                <span><i class="fa-solid fa-eye"></i> <?php echo $noticia['visualizacoes'] + 1; ?> visualizacoes</span>
            </div>
        </div>

        <?php if (!empty($noticia['imagem'])): ?>
            <div class="article-img">
                <img src="uploads/<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
            </div>
        <?php endif; ?>

        <div class="article-body">
            <?php echo nl2br(htmlspecialchars($noticia['noticia'])); ?>
        </div>
    </article>

    <aside class="sidebar">
        <?php if (count($maisLidas) > 0): ?>
        <div class="sidebar-widget">
            <h3><i class="fa-solid fa-fire"></i> Mais Lidas</h3>
            <?php foreach ($maisLidas as $idx => $ml): ?>
                <a href="noticia.php?id=<?php echo $ml['id']; ?>" class="news-list-item">
                    <span class="list-number"><?php echo $idx + 1; ?></span>
                    <div>
                        <h4><?php echo htmlspecialchars($ml['titulo']); ?></h4>
                        <span class="list-meta">
                            <i class="fa-solid fa-eye"></i> <?php echo $ml['visualizacoes']; ?> views
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </aside>
</div>

<?php include 'includes/footer.php'; ?>
