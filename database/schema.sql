
CREATE DATABASE IF NOT EXISTS locadora_de_filmes
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE locadora_de_filmes;

CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    cpf VARCHAR(255) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    nivel ENUM('administrador', 'comum') NOT NULL DEFAULT 'comum',
    data_de_cadastro DATE DEFAULT (CURRENT_DATE),

    PRIMARY KEY (usuario_id),
    UNIQUE (email)
);

CREATE TABLE clientes (
    cliente_id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(255) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    data_de_cadastro DATE DEFAULT (CURRENT_DATE),

    PRIMARY KEY (cliente_id),
    UNIQUE (telefone)
);

CREATE TABLE filmes (
    filme_id INT AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    genero VARCHAR(50) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    ano_de_lancamento YEAR NOT NULL,
    data_de_cadastro DATE DEFAULT (CURRENT_DATE),

    PRIMARY KEY (filme_id)
);

CREATE TABLE emprestimos (
    emprestimo_id INT AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    filme_id INT NOT NULL,
    usuario_id INT NOT NULL,
    status_emprestimo ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    data_da_devolucao DATE NOT NULL,
    data_do_emprestimo DATE DEFAULT (CURRENT_DATE),

    PRIMARY KEY (emprestimo_id),

    FOREIGN KEY (cliente_id)
        REFERENCES clientes (cliente_id),

    FOREIGN KEY (filme_id)
        REFERENCES filmes (filme_id),

    FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id)
);
