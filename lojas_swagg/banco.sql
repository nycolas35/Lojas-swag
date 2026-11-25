DROP DATABASE IF EXISTS lojas_swag;
CREATE DATABASE lojas_swag CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lojas_swag;

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NOT NULL,
  nome VARCHAR(100) NOT NULL,
  descricao VARCHAR(255),
  tamanho VARCHAR(20),
  preco DECIMAL(10,2) NOT NULL,
  estoque INT NOT NULL DEFAULT 0,
  imagem VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(120) UNIQUE,
  telefone VARCHAR(20)
);

CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE itens_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

TRUNCATE TABLE produtos;

INSERT INTO produtos (categoria_id, nome, descricao, tamanho, preco, estoque, imagem) VALUES
(1, 'Jaqueta Tech Fleece', 'Aquecimento leve, design bicolor', 'P ao GG', 499.90, 15, 'img/tech-fleece.jpg'),
(1, 'Shorts Esportivo Cinza', 'Tecido leve e respirável para treino', 'P ao GG', 129.90, 25, 'img/shortdrifit.jpg'),
(1, 'Kit 10 Cuecas Boxer', 'Algodão macio com elástico reforçado', 'P ao GG', 149.90, 35, 'img/kitcuecas.jpg'),
(1, 'Camiseta Oversized Black', 'Algodão pesado, caimento solto', 'P ao GG', 89.90, 30, 'img/overside.jpg'),
(1, 'Calça Cargo Preta', 'Seis bolsos, tecido sarja', '38 ao 46', 179.90, 20, 'img/cargo.jpg'),
(2, 'Tênis Air Max Cyan', 'Entressola leve, detalhe azul ciano', '38 ao 45', 359.90, 10, 'img/tn.jpg'),
(2, 'Tênis Street Black', 'Solado alto, cabedal sintético', '38 ao 44', 329.90, 15, 'img/tenisblack.jpg'),
(2, 'Bota Chelsea Couro', 'Couro legítimo, elástico lateral', '39 ao 44', 389.90, 8, 'img/bota.jpg');
