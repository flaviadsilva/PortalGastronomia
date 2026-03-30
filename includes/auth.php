<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

function exigirLogin() {
    if (!estaLogado()) {
        header('Location: login.php');
        exit;
    }
}

function ehAdmin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
}

function usuarioLogadoId() {
    return $_SESSION['usuario_id'] ?? null;
}

function usuarioLogadoNome() {
    return $_SESSION['usuario_nome'] ?? '';
}

function usuarioLogadoFoto() {
    return $_SESSION['usuario_foto'] ?? null;
}
