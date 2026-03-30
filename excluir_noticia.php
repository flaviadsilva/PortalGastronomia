<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/NoticiaDAO.php';

exigirLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$noticiaDAO = new NoticiaDAO($conn);
$noticia = $noticiaDAO->buscarPorId($id);

if (!$noticia) {
    header('Location: dashboard.php');
    exit;
}

// Somente o autor ou admin pode excluir
if ($noticia['autor'] != usuarioLogadoId() && !ehAdmin()) {
    header('Location: dashboard.php');
    exit;
}

// Remover imagem se existir
if (!empty($noticia['imagem']) && file_exists('uploads/' . $noticia['imagem'])) {
    unlink('uploads/' . $noticia['imagem']);
}

$noticiaDAO->excluir($id);
header('Location: dashboard.php?msg=noticia_excluida');
exit;
