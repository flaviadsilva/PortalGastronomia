<?php
class UsuarioDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $email, $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $hash]);
        return $this->conn->lastInsertId();
    }

    public function login($email, $senha) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function buscarPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function atualizar($id, $nome, $email, $bio, $foto = null) {
        if ($foto) {
            $stmt = $this->conn->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ?, foto = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $bio, $foto, $id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $bio, $id]);
        }
    }

    public function atualizarSenha($id, $novaSenha) {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function listarTodos() {
        $stmt = $this->conn->query("SELECT id, nome, email, foto, tipo, criado_em FROM usuarios ORDER BY criado_em DESC");
        return $stmt->fetchAll();
    }

    public function contarNoticias($usuario_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM noticias WHERE autor = ?");
        $stmt->execute([$usuario_id]);
        return $stmt->fetch()['total'];
    }
}
