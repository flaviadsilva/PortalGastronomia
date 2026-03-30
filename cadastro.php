<?php
$pageTitle = 'Cadastro';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/UsuarioDAO.php';

if (estaLogado()) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos obrigatorios.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas nao conferem.';
    } else {
        $usuarioDAO = new UsuarioDAO($conn);

        if ($usuarioDAO->buscarPorEmail($email)) {
            $erro = 'Este email ja esta cadastrado.';
        } else {
            try {
                $id = $usuarioDAO->cadastrar($nome, $email, $senha);
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_email'] = $email;
                $_SESSION['usuario_tipo'] = 'usuario';
                $_SESSION['usuario_foto'] = null;
                header('Location: dashboard.php');
                exit;
            } catch (Exception $e) {
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="fa-solid fa-user-plus"></i>
            <h2>Criar Conta</h2>
            <p>Junte-se ao Portal Gastronomia</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Nome completo</label>
                <input type="text" name="nome" placeholder="Seu nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="seu@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Senha</label>
                <input type="password" name="senha" placeholder="Minimo 6 caracteres" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Confirmar Senha</label>
                <input type="password" name="confirmar_senha" placeholder="Repita a senha" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Cadastrar
            </button>
        </form>

        <div class="form-links">
            <p>Ja tem uma conta? <a href="login.php">Faca login</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
