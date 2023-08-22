# crudphpvue
Simples CRUD usando PHP de BackEnd e Vue no Front

SQL utilizado foi:

CREATE TABLE `pessoas` (
  `codigo` int(11) NOT NULL,
  `nome` varchar(20) NOT NULL,
  `endereco` varchar(50) NOT NULL,
  `contatos` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`codigo`);
  
ALTER TABLE `pessoas`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;  

--
-- Estrutura para tabela `pessoas_contatos`
--

CREATE TABLE `pessoas_contatos` (
  `codigopessoa` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `telefone` varchar(20) NOT NULL
)

ALTER TABLE `pessoas_contatos`
  ADD KEY `pk_contatos_pessoas` (`codigopessoa`);

ALTER TABLE `pessoas_contatos`
  ADD CONSTRAINT `pk_contatos_pessoas` FOREIGN KEY (`codigopessoa`) REFERENCES `pessoas` (`codigo`);
COMMIT;
