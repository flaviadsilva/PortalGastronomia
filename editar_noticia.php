<?php
$pageTitle = 'Editar Noticia';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/NoticiaDAO.php';
require_once 'dao/CategoriaDAO.php';

exigirLogin();

$noticiaDAO = new NoticiaDAO($conn);
$categoriaDAO = new CategoriaDAO($conn);

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$noticia = $noticiaDAO->buscarPorId($id);
if (!$noticia) {
    header('Location: dashboard.php');
    exit;
}

// Somente o autor ou admin pode editar
if ($noticia['autor'] != usuarioLogadoId() && !ehAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$categorias = $categoriaDAO->listarTodas();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $resumo = trim($_POST['resumo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $imagem = null;

    if (empty($titulo) || empty($conteudo)) {
        $erro = 'Titulo e conteudo sao obrigatorios.';
    } else {
        if (!empty($_FILES['imagem']['name'])) {
            $extensoes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $extensoes)) {
                $erro = 'Formato de imagem invalido.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem muito grande. Maximo: 5MB.';
            } else {
                $nomeArquivo = uniqid('noticia_') . '.' . $ext;
                $destino = 'uploads/' . $nomeArquivo;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                    $imagem = $nomeArquivo;
                    // Remover imagem antiga
                    if (!empty($noticia['imagem']) && file_exists('uploads/' . $noticia['imagem'])) {
                        unlink('uploads/' . $noticia['imagem']);
                    }
                }
            }
        }

        if (empty($erro)) {
            try {
                $noticiaDAO->atualizar($id, $titulo, $resumo, $conteudo, $imagem, $categoria_id, $destaque);
                header('Location: noticia.php?id=' . $id);
                exit;
            } catch (Exception $e) {
                $erro = 'Erro ao atualizar noticia.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <h2><i class="fa-solid fa-pen-to-square"></i> Editar Noticia</h2>
        <p class="form-subtitle">Atualize sua publicacao</p>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fa-solid fa-heading"></i> Titulo</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left"></i> Resumo</label>
                <input type="text" name="resumo" maxlength="300" value="<?php echo htmlspecialchars($noticia['resumo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-tag"></i> Categoria</label>
                <select name="categoria_id">
                    <option value="0">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($noticia['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-newspaper"></i> Conteudo</label>
                <textarea name="noticia" required><?php echo htmlspecialchars($noticia['noticia']); ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i> Imagem</label>
                <?php if (!empty($noticia['imagem'])): ?>
                    <p style="font-size:0.8rem; color:var(--cinza); margin-bottom:8px;">
                        Imagem atual: <?php echo htmlspecialchars($noticia['imagem']); ?>
                    </p>
                <?php endif; ?>
                <input type="file" name="imagem" accept="image/*">
                <div id="imagePreview"></div>
            </div>

            <?php if (ehAdmin()): ?>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="destaque" value="1" <?php echo $noticia['destaque'] ? 'checked' : ''; ?>>
                    <span><i class="fa-solid fa-star"></i> Marcar como destaque</span>
                </label>
            </div>
            <?php endif; ?>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-laranja" style="flex:1;">
                    <i class="fa-solid fa-save"></i> Salvar
                </button>
                <a href="dashboard.php" class="btn btn-secondary" style="flex:0.5;">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
