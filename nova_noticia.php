<?php
$pageTitle = 'Nova Noticia';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/NoticiaDAO.php';
require_once 'dao/CategoriaDAO.php';

exigirLogin();

$noticiaDAO = new NoticiaDAO($conn);
$categoriaDAO = new CategoriaDAO($conn);
$categorias = $categoriaDAO->listarTodas();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $resumo = trim($_POST['resumo'] ?? '');
    $noticia = trim($_POST['noticia'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $imagem = null;

    if (empty($titulo) || empty($noticia)) {
        $erro = 'Titulo e conteudo sao obrigatorios.';
    } else {
        // Upload de imagem
        if (!empty($_FILES['imagem']['name'])) {
            $extensoes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $extensoes)) {
                $erro = 'Formato de imagem invalido. Use: jpg, png, gif ou webp.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem muito grande. Maximo: 5MB.';
            } else {
                $nomeArquivo = uniqid('noticia_') . '.' . $ext;
                $destino = 'uploads/' . $nomeArquivo;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                    $imagem = $nomeArquivo;
                } else {
                    $erro = 'Erro ao fazer upload da imagem.';
                }
            }
        }

        if (empty($erro)) {
            try {
                $id = $noticiaDAO->criar($titulo, $resumo, $noticia, $imagem, $categoria_id, usuarioLogadoId(), $destaque);
                header('Location: noticia.php?id=' . $id);
                exit;
            } catch (Exception $e) {
                $erro = 'Erro ao publicar noticia. Tente novamente.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <h2><i class="fa-solid fa-pen-nib"></i> Nova Noticia</h2>
        <p class="form-subtitle">Compartilhe algo delicioso com o mundo</p>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fa-solid fa-heading"></i> Titulo</label>
                <input type="text" name="titulo" placeholder="Titulo da sua noticia" value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left"></i> Resumo (opcional)</label>
                <input type="text" name="resumo" placeholder="Um breve resumo da noticia" maxlength="300" value="<?php echo htmlspecialchars($_POST['resumo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-tag"></i> Categoria</label>
                <select name="categoria_id">
                    <option value="0">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (($_POST['categoria_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-newspaper"></i> Conteudo</label>
                <textarea name="noticia" placeholder="Escreva sua noticia aqui..." required><?php echo htmlspecialchars($_POST['noticia'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i> Imagem (opcional)</label>
                <input type="file" name="imagem" accept="image/*">
                <div id="imagePreview"></div>
            </div>

            <?php if (ehAdmin()): ?>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="destaque" value="1">
                    <span><i class="fa-solid fa-star"></i> Marcar como destaque</span>
                </label>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-laranja">
                <i class="fa-solid fa-paper-plane"></i> Publicar Noticia
            </button>
        </form>

        <div class="form-links">
            <p><a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Voltar ao painel</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
