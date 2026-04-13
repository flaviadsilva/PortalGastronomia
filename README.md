# PortalGastronomia
# 🍝 Sabor & Arte — Portal de Gastronomia

Portal de receitas e notícias gastronômicas desenvolvido em **PHP + MySQL**, com cadastro de usuários, publicação de receitas, categorias culinárias e busca.

## ✨ Funcionalidades

- Cadastro e login de usuários (autenticação por sessão)
- Publicação, edição e exclusão de receitas (CRUD)
- Upload de fotos dos pratos
- Categorias (Massas, Carnes, Sobremesas, Vegetariano, Bebidas, etc.)
- Receitas em destaque e contador de visualizações
- Sistema de busca por ingrediente ou nome
- Página de perfil e gerenciamento de "Minhas Receitas"
- Avaliações e comentários (futuro)

## 🛠️ Tecnologias

- PHP 8+ (PDO)
- MySQL / MariaDB
- HTML, CSS e JavaScript (vanilla)
- Servidor local: XAMPP / Apache

## 📂 Estrutura sugerida

```
Gastronomia-php/
├── config/        # Conexão com o banco e autenticação
├── includes/      # header.php e footer.php
├── pages/         # Perfil, formulário de receita, gerenciamento
├── assets/        # CSS, JS, imagens
├── uploads/       # Fotos enviadas pelos usuários
├── database.sql   # Estrutura do banco
└── seed_receitas.sql
```

## 🗄️ Banco de dados (exemplo)

```sql
CREATE TABLE receitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL,
    ingredientes TEXT NOT NULL,
    modo_preparo TEXT NOT NULL,
    tempo_preparo INT,            -- em minutos
    porcoes INT,
    dificuldade ENUM('fácil','médio','difícil') DEFAULT 'fácil',
    imagem VARCHAR(255),
    categoria_id INT,
    autor_id INT NOT NULL,
    destaque TINYINT(1) DEFAULT 0,
    visualizacoes INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 🚀 Como rodar localmente

1. Instale o [XAMPP](https://www.apachefriends.org/) e inicie **Apache** e **MySQL**.
2. Clone o projeto em `C:/xampp/htdocs/`:
   ```bash
   git clone <repo> Gastronomia-php
   ```
3. Importe `database.sql` no phpMyAdmin (e o seed se quiser receitas de exemplo).
4. Configure `config/database.php`:
   ```php
   $host = 'localhost';
   $dbname = 'portal_gastronomia';
   $username = 'root';
   $password = '';
   ```
5. Acesse: <http://localhost/Gastronomia-php>

## 🍽️ Categorias iniciais

Massas · Carnes · Aves · Peixes · Vegetariano · Vegano · Sobremesas · Bebidas · Lanches · Saladas · Pães · Comida Internacional

## 📄 Licença

Projeto acadêmico — uso livre para fins educacionais.