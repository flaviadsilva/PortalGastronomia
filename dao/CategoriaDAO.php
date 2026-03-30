<?php
class CategoriaDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listarTodas() {
        $stmt = $this->conn->query("SELECT * FROM categorias ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function buscarPorSlug($slug) {
        $stmt = $this->conn->prepare("SELECT * FROM categorias WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function criar($nome, $slug, $icone, $cor, $descricao) {
        $stmt = $this->conn->prepare("INSERT INTO categorias (nome, slug, icone, cor, descricao) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $slug, $icone, $cor, $descricao]);
        return $this->conn->lastInsertId();
    }

    public function atualizar($id, $nome, $slug, $icone, $cor, $descricao) {
        $stmt = $this->conn->prepare("UPDATE categorias SET nome = ?, slug = ?, icone = ?, cor = ?, descricao = ? WHERE id = ?");
        $stmt->execute([$nome, $slug, $icone, $cor, $descricao, $id]);
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function contarNoticias($categoria_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM noticias WHERE categoria_id = ?");
        $stmt->execute([$categoria_id]);
        return $stmt->fetch()['total'];
    }

    public function listarComContagem() {
        $stmt = $this->conn->query(
            "SELECT c.*, COUNT(n.id) as total_noticias
             FROM categorias c
             LEFT JOIN noticias n ON c.id = n.categoria_id
             GROUP BY c.id
             ORDER BY c.nome ASC"
        );
        return $stmt->fetchAll();
    }
}
