<?php
$pageTitle = 'Gerenciar Usuarios';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/UsuarioDAO.php';

exigirLogin();

if (!ehAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$usuarioDAO = new UsuarioDAO($conn);
$usuarios = $usuarioDAO->listarTodos();

include 'includes/header.php';
?>

<div class="dashboard-header">
    <h2><i class="fa-solid fa-users" style="color:var(--vermelho)"></i> Gerenciar Usuarios</h2>
    <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Cadastro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>#<?php echo $u['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($u['nome']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <?php if ($u['tipo'] === 'admin'): ?>
                            <span style="background:var(--vermelho); color:white; padding:3px 10px; border-radius:12px; font-size:0.75rem;">Admin</span>
                        <?php else: ?>
                            <span style="background:var(--cinza-claro); padding:3px 10px; border-radius:12px; font-size:0.75rem;">Usuario</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($u['criado_em'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
