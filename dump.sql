-- ============================================
-- Portal Gastronomia - Banco de Dados
-- Nome do banco: portal_gastronomia
-- ============================================

CREATE DATABASE IF NOT EXISTS portal_gastronomia
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE portal_gastronomia;

-- ============================================
-- Tabela de Usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    tipo ENUM('usuario', 'admin') DEFAULT 'usuario',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabela de Categorias (TABELA EXTRA)
-- Funcionalidade exclusiva: filtragem por categoria
-- ============================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL UNIQUE,
    slug VARCHAR(80) NOT NULL UNIQUE,
    icone VARCHAR(50) DEFAULT 'fa-utensils',
    cor VARCHAR(7) DEFAULT '#E74C3C',
    descricao TEXT DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabela de Noticias
-- ============================================
CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    resumo VARCHAR(300) DEFAULT NULL,
    noticia TEXT NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    categoria_id INT DEFAULT NULL,
    autor INT NOT NULL,
    destaque TINYINT(1) DEFAULT 0,
    visualizacoes INT DEFAULT 0,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (autor) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- Dados iniciais - Categorias
-- ============================================
INSERT INTO categorias (nome, slug, icone, cor, descricao) VALUES
('Receitas', 'receitas', 'fa-bowl-food', '#E74C3C', 'As melhores receitas do mundo gastronomico'),
('Restaurantes', 'restaurantes', 'fa-store', '#F39C12', 'Novidades e reviews de restaurantes'),
('Confeitaria', 'confeitaria', 'fa-cake-candles', '#E91E63', 'O doce mundo da confeitaria'),
('Bebidas', 'bebidas', 'fa-wine-glass', '#9B59B6', 'Vinhos, coqueteis e muito mais'),
('Saude e Nutricao', 'saude-nutricao', 'fa-apple-whole', '#27AE60', 'Alimentacao saudavel e dicas nutricionais'),
('Cultura Gastronomica', 'cultura-gastronomica', 'fa-earth-americas', '#3498DB', 'Gastronomia pelo mundo'),
('Dicas e Tecnicas', 'dicas-tecnicas', 'fa-fire-burner', '#F97316', 'Truques e tecnicas culinarias'),
('Eventos', 'eventos', 'fa-calendar-days', '#8B5CF6', 'Feiras, festivais e eventos gastronomicos');

-- ============================================
-- Usuario admin padrao (senha: admin123)
-- ============================================
INSERT INTO usuarios (nome, email, senha, tipo, bio) VALUES
('Chef Admin', 'admin@portalgatronomia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrador do Portal Gastronomia');
