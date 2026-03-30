<?php
require 'config/database.php';
require 'dao/NoticiaDAO.php';

$noticiaDAO = new NoticiaDAO($conn);
$noticias = $noticiaDAO->listarTodas();
?>

<?php include 'includes/header.php'; ?>

<h1>🍔 Portal Gastronomia</h1>

<div class="container">

    <?php if (count($noticias) > 0): ?>
        
        <?php foreach ($noticias as $noticia): ?>
            
            <div class="card">

                <?php if (!empty($noticia['imagem'])): ?>
                    <img src="uploads/<?php echo $noticia['imagem']; ?>" alt="Imagem da notícia">
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($noticia['titulo']); ?></h2>

                <p>
                    <?php 
                    echo substr(htmlspecialchars($noticia['noticia']), 0, 150) . '...'; 
                    ?>
                </p>

                <small>
                    👨‍🍳 Autor: <?php echo htmlspecialchars($noticia['autor_nome']); ?> |
                    📅 <?php echo date('d/m/Y H:i', strtotime($noticia['data'])); ?>
                </small>

                <br><br>

                <a href="noticia.php?id=<?php echo $noticia['id']; ?>">
                    Ver receita completa 🍽️
                </a>

            </div>

        <?php endforeach; ?>

    <?php else: ?>
        <p>Nenhuma receita cadastrada ainda 😢</p>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>