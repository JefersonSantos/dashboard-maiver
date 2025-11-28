-- Script SQL para criar a tabela de usuários
-- Execute este script no seu banco de dados MySQL

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuário padrão (senha: admin123)
-- IMPORTANTE: Altere a senha após o primeiro login!
INSERT INTO `usuarios` (`username`, `password`, `ativo`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Ou se preferir usar senha em texto simples (menos seguro):
-- INSERT INTO `usuarios` (`username`, `password`, `ativo`) VALUES
-- ('admin', 'admin123', 1);

-- Para criar mais usuários, use:
-- INSERT INTO `usuarios` (`username`, `password`, `ativo`) VALUES
-- ('usuario1', 'senha123', 1),
-- ('usuario2', 'senha456', 1);

