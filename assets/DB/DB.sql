-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/04/2025 às 00:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `mercado`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `ID` int(11) NOT NULL,
  `CPF` varchar(14) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `fidelidade` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--



-- ===============DEBUG==========================
INSERT INTO `clientes` (`ID`, `CPF`, `nome`, `fidelidade`) VALUES
(1, '24001140802', 'Nome Teste Veryfy', 0);
-- ===============DEBUG==========================





-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `ID` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `desconto_fidelidade` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`ID`, `codigo`, `valor`, `nome`, `desconto_fidelidade`) VALUES
(1, '9002490275013', 8.90, 'RedBull Maracujá & Melão', 1),
(2, '7892840808013', 19.95, 'Gatorade Frutas Cítricas', 0),
(3, '7897947612907', 12.99, 'XÔ inseto', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`ID`, `nome`, `cargo`, `senha`) VALUES
(1, 'João Silva', 'CAIXA', '$2y$10$sdEV8g6Eiece/RXJrywrXuSQR.WjZvYTiQBZWCPZTFHqGdu2outCK'),
(2, 'Maria Oliveira', 'CAIXA', '$2y$10$uVtXYI1/UTVcLOQUrR4bLOl1Kd/cLXCJRgSi9DvLDy3TvplTsU/QW'),
(3, 'Pedro Santos', 'CAIXA', '$2y$10$8YX0qe.L9hHQB45uQVgEiu08yKhL3HyIW5sQsHWWvKgcoLuwhGsbu'),
(4, 'Camila Martins', 'GESTOR', '$2y$10$0e37ldeQrzCR9LIPGouJieYrAiaXdWdcnmzRaCxItkGUd/9B0zFo2'),
(5, 'Jucelino Ku de Cheque', 'GESTOR', '$2y$10$wo5ueBIf2UU9QgJkkNuMnuXlM0mpok5GCFlWGsEl7fXRPXPfAqaeS'),
(6, 'Croco', 'ADM', '$2y$10$3lk9xf/lLmrBVVpEioxPnOO5aJ0SmgayIjGf7LIsEvWEClaQ2VCdO');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `CPF` (`CPF`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
