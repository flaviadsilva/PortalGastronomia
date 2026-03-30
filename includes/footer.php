    </div>
</main>

<!-- Footer -->
<footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3><i class="fa-solid fa-utensils"></i> Portal Gastronomia</h3>
                <p>Seu portal de noticias sobre o mundo da gastronomia. Receitas, restaurantes, tecnicas e muito mais.</p>
                <div class="footer-social">
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Categorias</h4>
                <ul>
                    <?php
                    require_once __DIR__ . '/../config/database.php';
                    require_once __DIR__ . '/../dao/CategoriaDAO.php';
                    $footerCatDAO = new CategoriaDAO($conn);
                    $footerCats = $footerCatDAO->listarTodas();
                    foreach ($footerCats as $fc): ?>
                        <li><a href="categoria.php?slug=<?php echo $fc['slug']; ?>"><?php echo htmlspecialchars($fc['nome']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Portal</h4>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="cadastro.php">Cadastre-se</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Portal Gastronomia - Projeto Academico. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<script src="assets/script.js"></script>
</body>
</html>
