<?php
$pageTitle = 'Meu Painel';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/NoticiaDAO.php';
require_once 'dao/UsuarioDAO.php';

exigirLogin();

$noticiaDAO = new NoticiaDAO($conn);
$usuarioDAO = new UsuarioDAO($conn);

$usuario = $usuarioDAO->buscarPorId(usuarioLogadoId());
$minhasNoticias = $noticiaDAO->listarPorAutor(usuarioLogadoId());
$totalNoticias = count($minhasNoticias);
$totalViews = array_sum(array_column($minhasNoticias, 'visualizacoes'));

$msg = $_GET['msg'] ?? '';

include 'includes/header.php';
?>

<!-- Mensagens -->
<?php if ($msg === 'noticia_excluida'): ?>
    <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Noticia excluida com sucesso!</div>
<?php endif; ?>
<?php if ($msg === 'conta_atualizada'): ?>
    <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Conta atualizada com sucesso!</div>
<?php endif; ?>

<div class="dashboard-header">
    <h2><i class="fa-solid fa-gauge" style="color:var(--vermelho)"></i> Ola, <?php echo htmlspecialchars(usuarioLogadoNome()); ?>!</h2>
    <div style="display:flex; gap:10px;">
        <a href="nova_noticia.php" class="btn btn-laranja btn-sm"><i class="fa-solid fa-plus"></i> Nova Noticia</a>
        <a href="editar_usuario.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-user-gear"></i> Minha Conta</a>
    </div>
</div>

<!-- Stats -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--vermelho);">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalNoticias; ?></h3>
            <p>Noticias Publicadas</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--laranja);">
            <i class="fa-solid fa-eye"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalViews; ?></h3>
            <p>Visualizacoes Totais</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #27AE60;">
            <i class="fa-solid fa-calendar"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo date('d/m/Y', strtotime($usuario['criado_em'])); ?></h3>
            <p>Membro desde</p>
        </div>
    </div>
    <?php if (ehAdmin()): ?>
    <div class="stat-card">
        <div class="stat-icon" style="background: #8B5CF6;">
            <i class="fa-solid fa-shield"></i>
        </div>
        <div class="stat-info">
            <h3>Admin</h3>
            <p><a href="usuarios.php" style="color:var(--vermelho);">Gerenciar usuarios</a></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Minhas Noticias -->
<div class="section-header">
    <h2><i class="fa-solid fa-pen-nib"></i> Minhas Noticias</h2>
</div>

<?php if (count($minhasNoticias) > 0): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Titulo</th>
                    <th>Categoria</th>
                    <th>Views</th>
                    <th>Data</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minhasNoticias as $n): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars(mb_substr($n['titulo'], 0, 50)); ?></strong>
                            <?php if ($n['destaque']): ?>
                                <i class="fa-solid fa-star" style="color:var(--amarelo-forte);"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($n['categoria_nome'])): ?>
                                <span style="background:<?php echo $n['categoria_cor'] ?? '#ccc'; ?>; color:white; padding:3px 8px; border-radius:12px; font-size:0.75rem;">
                                    <?php echo htmlspecialchars($n['categoria_nome']); ?>
                                </span>
                            <?php else: ?>
                                <span style="color:var(--cinza);">-</span>
                            <?php endif; ?>
                        </td>
                        <td><i class="fa-solid fa-eye" style="color:var(--cinza);"></i> <?php echo $n['visualizacoes']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($n['data'])); ?></td>
                        <td>
                            <div class="actions">
                                <a href="noticia.php?id=<?php echo $n['id']; ?>" class="action-view" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                <a href="editar_noticia.php?id=<?php echo $n['id']; ?>" class="action-edit" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <a href="excluir_noticia.php?id=<?php echo $n['id']; ?>" class="action-delete confirm-delete" title="Excluir"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-pen-fancy"></i>
        <h3>Nenhuma noticia publicada</h3>
        <p>Comece a compartilhar suas historias gastronomicas!</p>
        <a href="nova_noticia.php" class="btn btn-laranja"><i class="fa-solid fa-plus"></i> Publicar Primeira Noticia</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
