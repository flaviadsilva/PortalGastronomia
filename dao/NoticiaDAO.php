<?php
class NoticiaDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function gerarSlug($titulo) {
        $slug = mb_strtolower($titulo, 'UTF-8');
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug);
        $slug = preg_replace('/[éèêë]/u', 'e', $slug);
        $slug = preg_replace('/[íìîï]/u', 'i', $slug);
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug);
        $slug = preg_replace('/[úùûü]/u', 'u', $slug);
        $slug = preg_replace('/[ç]/u', 'c', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $base = $slug;
        $count = 1;
        while (true) {
            $stmt = $this->conn->prepare("SELECT id FROM noticias WHERE slug = ?");
            $stmt->execute([$slug]);
            if (!$stmt->fetch()) break;
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }

    public function criar($titulo, $resumo, $noticia, $imagem, $categoria_id, $autor, $destaque = 0) {
        $slug = $this->gerarSlug($titulo);
        $stmt = $this->conn->prepare(
            "INSERT INTO noticias (titulo, slug, resumo, noticia, imagem, categoria_id, autor, destaque)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$titulo, $slug, $resumo, $noticia, $imagem, $categoria_id ?: null, $autor, $destaque]);
        return $this->conn->lastInsertId();
    }

    public function atualizar($id, $titulo, $resumo, $noticia, $imagem, $categoria_id, $destaque = 0) {
        if ($imagem) {
            $stmt = $this->conn->prepare(
                "UPDATE noticias SET titulo = ?, resumo = ?, noticia = ?, imagem = ?, categoria_id = ?, destaque = ? WHERE id = ?"
            );
            $stmt->execute([$titulo, $resumo, $noticia, $imagem, $categoria_id ?: null, $destaque, $id]);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE noticias SET titulo = ?, resumo = ?, noticia = ?, categoria_id = ?, destaque = ? WHERE id = ?"
            );
            $stmt->execute([$titulo, $resumo, $noticia, $categoria_id ?: null, $destaque, $id]);
        }
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.nome as autor_nome, u.foto as autor_foto, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor, c.icone as categoria_icone
             FROM noticias n
             JOIN usuarios u ON n.autor = u.id
             LEFT JOIN categorias c ON n.categoria_id = c.id
             WHERE n.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function incrementarVisualizacao($id) {
        $stmt = $this->conn->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function listarTodas($limite = 20, $offset = 0) {
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.nome as autor_nome, u.foto as autor_foto, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor, c.icone as categoria_icone
             FROM noticias n
             JOIN usuarios u ON n.autor = u.id
             LEFT JOIN categorias c ON n.categoria_id = c.id
             ORDER BY n.data DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$limite, $offset]);
        return $stmt->fetchAll();
    }

    public function listarDestaques($limite = 5) {
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.nome as autor_nome, u.foto as autor_foto, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor
             FROM noticias n
             JOIN usuarios u ON n.autor = u.id
             LEFT JOIN categorias c ON n.categoria_id = c.id
             WHERE n.destaque = 1
             ORDER BY n.data DESC
             LIMIT ?"
        );
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    public function listarPorCategoria($categoria_id, $limite = 20, $offset = 0) {
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.nome as autor_nome, u.foto as autor_foto, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor, c.icone as categoria_icone
             FROM noticias n
             JOIN usuarios u ON n.autor = u.id
             LEFT JOIN categorias c ON n.categoria_id = c.id
             WHERE n.categoria_id = ?
             ORDER BY n.data DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoria_id, $limite, $offset]);
        return $stmt->fetchAll();
    }

    public function buscar($termo, $limite = 20) {
        $termo = "%$termo%";
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.nome as autor_nome, u.foto as autor_foto, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor, c.icone as categoria_icone
             FROM noticias n
             JOIN usuarios u ON n.autor = u.id
             LEFT JOIN categorias c ON n.categoria_id = c.id
             WHERE n.titulo LIKE ? OR n.noticia LIKE ? OR n.resumo LIKE ?
             ORDER BY n.data DESC
             LIMIT ?"
        );
        $stmt->execute([$termo, $termo, $termo, $limite]);
        return $stmt->fetchAll();
    }

    public function listarPorAutor($autor_id) {
        $stmt = $this->conn->prepare(
            "SELECT n.*, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor, c.icone as categoria_icone
             FROM noticias n
             LEFT JOIN categorias c ON n.categoria_id = c.id
             WHERE n.autor = ?
             ORDER BY n.data DESC"
        );
        $stmt->execute([$autor_id]);
        return $stmt->fetchAll();
    }

    public function contarTodas() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM noticias");
        return $stmt->fetch()['total'];
    }

    public function contarPorCategoria($categoria_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM noticias WHERE categoria_id = ?");
        $stmt->execute([$categoria_id]);
        return $stmt->fetch()['total'];
    }

    public function maisLidas($limite = 5) {
        $stmt = $this->conn->prepare(
            "SELECT n.*, u.nome as autor_nome, c.nome as categoria_nome, c.slug as categoria_slug, c.cor as categoria_cor
             FROM noticias n
             JOIN usuarios u ON n.autor = u.id
             LEFT JOIN categorias c ON n.categoria_id = c.id
             ORDER BY n.visualizacoes DESC
             LIMIT ?"
        );
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
}
