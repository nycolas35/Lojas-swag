-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/11/2026 às 13:01
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lojas_swag`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(1, 'Roupas'),
(2, 'Sapatos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`) VALUES
(1, 'HENRIQUE BARBIERI RAMOS', 'henrique.barbieri.ramos@escola.pr.gov.br', '42988183018'),
(2, 'Cliente Teste', 'teste@email.com', '(41) 99999-9999');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 27, 1, 149.90),
(2, 1, 29, 1, 179.90),
(3, 2, 25, 1, 499.90),
(4, 2, 26, 1, 129.90),
(5, 3, 25, 1, 499.90),
(6, 3, 28, 1, 89.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` char(2) NOT NULL,
  `valor_impostos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_frete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `forma_pagamento` varchar(50) NOT NULL DEFAULT 'Pix',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_pedido` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `cep`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `valor_impostos`, `valor_frete`, `forma_pagamento`, `total`, `criado_em`, `data_pedido`) VALUES
(1, 1, '79045-402', 'Rua Marcos', '31', '', 'Bairro Industrial', 'Campo Grande', 'MS', 39.58, 0.00, 'Pix', 352.89, '2026-11-13 15:20:31', '2026-11-13 12:29:54'),
(2, 1, '79045-402', 'Rua Marcos', '31', '', 'Bairro Industrial', 'Campo Grande', 'MS', 75.58, 0.00, 'Pix', 673.89, '2026-11-13 15:21:32', '2026-11-13 12:29:54'),
(3, 1, '79045-402', 'Rua Marcos', '31', '', 'Bairro Industrial', 'Campo Grande', 'MS', 70.78, 0.00, 'Pix', 631.09, '2026-11-13 15:22:20', '2026-11-13 12:29:54'),
(4, 2, '80010-000', 'Rua XV de Novembro', '100', NULL, 'Centro', 'Curitiba', 'PR', 24.00, 0.00, 'Pix', 214.00, '2026-11-13 15:30:29', '2026-11-13 12:30:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tamanho` varchar(20) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `imagem` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `categoria_id`, `nome`, `descricao`, `tamanho`, `preco`, `estoque`, `imagem`, `criado_em`) VALUES
(25, 1, 'Jaqueta Tech Fleece', 'Aquecimento leve, design bicolor', 'P ao GG', 499.90, 13, 'img/tech.jpg', '2026-11-13 14:57:31'),
(26, 1, 'Shorts Esportivo Cinza', 'Tecido leve e respirável para treino', 'P ao GG', 129.90, 24, 'img/shortdrifit.jpg', '2026-11-13 14:57:31'),
(27, 1, 'Kit 10 Cuecas Boxer', 'Algodão macio com elástico reforçado', 'P ao GG', 149.90, 34, 'img/kitcueca.jpg', '2026-11-13 14:57:31'),
(28, 1, 'Camiseta Oversized Black', 'Algodão pesado, caimento solto', 'P ao GG', 89.90, 29, 'img/overside.jpg', '2026-11-13 14:57:31'),
(29, 1, 'Calça Cargo Preta', 'Seis bolsos, tecido sarja', '38 ao 46', 179.90, 19, 'img/cargo.jpg', '2026-11-13 14:57:31'),
(30, 2, 'Tênis Air Max Cyan', 'Entressola leve, detalhe azul ciano', '38 ao 45', 359.90, 10, 'img/tn.jpg', '2026-11-13 14:57:31'),
(31, 2, 'Tênis Street Black', 'Solado alto, cabedal sintético', '38 ao 44', 329.90, 15, 'img/tenisblack.jpg', '2026-11-13 14:57:31'),
(32, 2, 'Bota Chelsea Couro', 'Couro legítimo, elástico lateral', '39 ao 44', 389.90, 8, 'img/bota.jpg', '2026-11-13 14:57:31');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
