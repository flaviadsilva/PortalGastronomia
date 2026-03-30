<?php
$pageTitle = 'Editar Conta';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/UsuarioDAO.php';

exigirLogin();

$usuarioDAO = new UsuarioDAO($conn);
$usuario = $usuarioDAO->buscarPorId(usuarioLogadoId());

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'perfil';

    if ($acao === 'perfil') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $foto = null;

        if (empty($nome) || empty($email)) {
            $erro = 'Nome e email sao obrigatorios.';
        } else {
            // Verificar se email ja existe para outro usuario
            $existente = $usuarioDAO->buscarPorEmail($email);
            if ($existente && $existente['id'] != usuarioLogadoId()) {
                $erro = 'Este email ja esta em uso por outro usuario.';
            } else {
                if (!empty($_FILES['foto']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $nomeArquivo = uniqid('avatar_') . '.' . $ext;
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $nomeArquivo)) {
                            $foto = $nomeArquivo;
                            // Remover foto antiga
                            if (!empty($usuario['foto']) && file_exists('uploads/' . $usuario['foto'])) {
                                unlink('uploads/' . $usuario['foto']);
                            }
                        }
                    }
                }

                $usuarioDAO->atualizar(usuarioLogadoId(), $nome, $email, $bio, $foto);
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_email'] = $email;
                if ($foto) $_SESSION['usuario_foto'] = $foto;

                $usuario = $usuarioDAO->buscarPorId(usuarioLogadoId());
                $sucesso = 'Perfil atualizado com sucesso!';
            }
        }
    } elseif ($acao === 'senha') {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        if (empty($senhaAtual) || empty($novaSenha)) {
            $erro = 'Preencha todos os campos de senha.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } elseif ($novaSenha !== $confirmar) {
            $erro = 'As senhas nao conferem.';
        } elseif (!password_verify($senhaAtual, $usuario['senha'])) {
            $erro = 'Senha atual incorreta.';
        } else {
            $usuarioDAO->atualizarSenha(usuarioLogadoId(), $novaSenha);
            $sucesso = 'Senha alterada com sucesso!';
        }
    }
}

include 'includes/header.php';
?>

<div class="form-page">
    <?php if ($erro): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro; ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <?php echo $sucesso; ?></div>
    <?php endif; ?>

    <!-- Perfil -->
    <div class="form-card" style="margin-bottom:25px;">
        <h2><i class="fa-solid fa-user-gear"></i> Meu Perfil</h2>
        <p class="form-subtitle">Atualize suas informacoes</p>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="perfil">

            <div style="text-align:center; margin-bottom:20px;">
                <div class="profile-avatar" style="margin:0 auto;">
                    <?php if (!empty($usuario['foto'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($usuario['foto']); ?>" alt="">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Nome</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-pen"></i> Bio</label>
                <textarea name="bio" placeholder="Conte um pouco sobre voce..." maxlength="500"><?php echo htmlspecialchars($usuario['bio'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-camera"></i> Foto de Perfil</label>
                <input type="file" name="foto" accept="image/*">
            </div>

            <button type="submit" class="btn btn-laranja">
                <i class="fa-solid fa-save"></i> Salvar Perfil
            </button>
        </form>
    </div>

    <!-- Alterar Senha -->
    <div class="form-card" style="margin-bottom:25px;">
        <h2><i class="fa-solid fa-lock"></i> Alterar Senha</h2>
        <p class="form-subtitle">Mantenha sua conta segura</p>

        <form method="POST" action="">
            <input type="hidden" name="acao" value="senha">

            <div class="form-group">
                <label><i class="fa-solid fa-key"></i> Senha Atual</label>
                <input type="password" name="senha_atual" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Nova Senha</label>
                <input type="password" name="nova_senha" placeholder="Minimo 6 caracteres" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Confirmar Nova Senha</label>
                <input type="password" name="confirmar_senha" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-key"></i> Alterar Senha
            </button>
        </form>
    </div>

    <!-- Excluir Conta -->
    <div class="form-card" style="border: 2px solid var(--vermelho-pastel);">
        <h2 style="color:var(--vermelho);"><i class="fa-solid fa-triangle-exclamation"></i> Zona Perigosa</h2>
        <p class="form-subtitle">Excluir sua conta permanentemente</p>
        <p style="font-size:0.85rem; color:var(--cinza); margin-bottom:15px;">
            Esta acao ira excluir sua conta e todas as suas noticias. Esta acao nao pode ser desfeita.
        </p>
        <a href="excluir_usuario.php" class="btn btn-danger confirm-delete">
            <i class="fa-solid fa-trash"></i> Excluir Minha Conta
        </a>
    </div>

    <div class="form-links" style="margin-top:20px;">
        <p><a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Voltar ao painel</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
