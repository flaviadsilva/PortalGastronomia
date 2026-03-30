<?php
$termo = trim($_GET['q'] ?? '');
$pageTitle = 'Buscar: ' . $termo;

require_once 'config/database.php';
require_once 'dao/NoticiaDAO.php';

$noticiaDAO = new NoticiaDAO($conn);
$resultados = [];

if (!empty($termo)) {
    $resultados = $noticiaDAO->buscar($termo);
}

include 'includes/header.php';
?>

<div class="section-header">
    <h2>
        <i class="fa-solid fa-magnifying-glass"></i>
        Resultados para: "<?php echo htmlspecialchars($termo); ?>"
    </h2>
    <span style="color:var(--cinza); font-size:0.9rem;"><?php echo count($resultados); ?> resultado(s)</span>
</div>

<?php if (count($resultados) > 0): ?>
    <div class="news-grid">
        <?php foreach ($resultados as $noticia): ?>
            <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="news-card">
                <div class="news-card-img">
                    <?php if (!empty($noticia['imagem'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="">
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
        <i class="fa-solid fa-magnifying-glass"></i>
        <h3>Nenhum resultado encontrado</h3>
        <p>Tente buscar por outros termos relacionados a gastronomia.</p>
        <a href="index.php" class="btn btn-laranja"><i class="fa-solid fa-house"></i> Voltar ao Inicio</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
