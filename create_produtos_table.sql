-- Script SQL para criar a tabela de produtos
-- Execute este script no seu banco de dados MySQL

CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabela` varchar(100) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `imagem_url` text NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tabela` (`tabela`),
  KEY `ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir produtos existentes (opcional - você pode fazer isso via interface)
-- Exemplo:
-- INSERT INTO produtos (tabela, nome, imagem_url, ativo) VALUES
-- ('adv_bioxcell', 'Bioxcell', 'https://thumbor.cartpanda.com/nH5ao1dBlpgq_JimR_hYOI47F0Y=/800x0/https://assets.mycartpanda.com/static/products_images/ec/62/60/1746579329.png', 1);

