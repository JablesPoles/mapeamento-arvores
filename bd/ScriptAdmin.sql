-- Script para Configurar Apenas a Tabela de Administradores (PostgreSQL)
-- Data: 2024-05-20

-- Etapa 1: Remover a tabela 'administradores' se ela já existir,
-- para garantir uma recriação limpa.
DROP TABLE IF EXISTS administradores CASCADE;

-- Etapa 2: Criar a tabela 'administradores'
CREATE TABLE administradores (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(255),
    data_criacao TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE administradores IS 'Tabela para armazenar usuários administrativos do sistema.';
COMMENT ON COLUMN administradores.senha IS 'Senha armazenada como hash (ex: bcrypt).';
COMMENT ON COLUMN administradores.id IS 'Identificador único do administrador, gerado automaticamente.';
COMMENT ON COLUMN administradores.usuario IS 'Nome de usuário para login, deve ser único.';
COMMENT ON COLUMN administradores.nome_completo IS 'Nome completo do administrador.';
COMMENT ON COLUMN administradores.data_criacao IS 'Data e hora em que o registro do administrador foi criado.';

INSERT INTO administradores (usuario, senha, nome_completo)
VALUES ('admin', '$2y$10$7Qi0/ZNnK5pUvyfuXC5rNOKdHZkgwceWdBj8Bj9/GR3YpqNw0OMTS', 'Administrador Padrão');

SELECT 'Tabela "administradores" recriada e usuário "admin" (senha "admin123") inserido com sucesso.' AS status;
