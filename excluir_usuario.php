<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'dao/UsuarioDAO.php';

exigirLogin();

$usuarioDAO = new UsuarioDAO($conn);
$usuario = $usuarioDAO->buscarPorId(usuarioLogadoId());

// Remover foto se existir
if (!empty($usuario['foto']) && file_exists('uploads/' . $usuario['foto'])) {
    unlink('uploads/' . $usuario['foto']);
}

$usuarioDAO->excluir(usuarioLogadoId());
session_destroy();
header('Location: index.php');
exit;
