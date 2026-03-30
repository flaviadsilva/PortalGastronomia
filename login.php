<?php
$pageTitle = 'Login';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/UsuarioDAO.php';

if (estaLogado()) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        $usuarioDAO = new UsuarioDAO($conn);
        $usuario = $usuarioDAO->login($email, $senha);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            $_SESSION['usuario_foto'] = $usuario['foto'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="fa-solid fa-utensils"></i>
            <h2>Bem-vindo de volta!</h2>
            <p>Entre na sua conta para publicar noticias</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="seu@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Senha</label>
                <input type="password" name="senha" placeholder="Sua senha" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
        </form>

        <div class="form-links">
            <p>Nao tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
