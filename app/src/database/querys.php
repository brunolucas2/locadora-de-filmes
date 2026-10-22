<?php

$authSQL = [
    "autenticar" => "
        SELECT senha_hash FROM usuarios
        WHERE email = :email;
    ",
    "buscarDados" => "
        SELECT usuario_id, nivel FROM usuarios
        WHERE email = :email;
    "
];

$clientesSQL = [
    "cadastrarCliente" => "
        INSERT INTO clientes (
            nome,
            cpf,
            telefone
        ) VALUES (
            :nome,
            :cpf,
            :telefone
        );
    ",
    "buscarClientes" => "
        SELECT * FROM clientes;
    ",
    "buscarCliente" => "
        SELECT * FROM clientes
        WHERE cliente_id = :id;
    ",
    "atualizarCliente" => "
        UPDATE clientes
        SET
            nome = COALESCE(NULLIF(:nome, ''), nome),
            cpf = COALESCE(NULLIF(:cpf, ''), cpf),
            telefone = COALESCE(NULLIF(:telefone, ''), telefone)
        WHERE cliente_id = :id;
    ",
    "deletarCliente" => "
        DELETE FROM clientes
        WHERE cliente_id = :id;
    "
];

$usuariosSQL = [
    "cadastrarUsuario" => "
        INSERT INTO usuarios (
            nome,
            cpf,
            senha_hash
        ) VALUES (
            :nome,
            :cpf,
            :senha_hash
        );
    ",
    "buscarUsuarios" => "
        SELECT * FROM usuarios;
    ",
    "buscarUsuario" => "
        SELECT * FROM usuarios
        WHERE usuario_id = :id;
    ",
    "atualizarUsuario" => "
        UPDATE usuarios
        SET
            nome = COALESCE(NULLIF(:nome, ''), nome),
            cpf = COALESCE(NULLIF(:cpf, ''), cpf),
            senha_hash = COALESCE(NULLIF(:senha_hash, ''), senha_hash)
        WHERE usuario_id = :id;
    ",
    "deletarUsuario" => "
        DELETE FROM usuarios
        WHERE usuario_id = :id;
    "
];

$filmesSQL = [
    "cadastrarFilme" => "
        INSERT INTO filmes (
            titulo,
            genero,
            categoria,
            ano_de_lancamento
        ) VALUES (
            :titulo,
            :genero,
            :categoria,
            :ano_de_lancamento
        );
    ",
    "buscarFilmes" => "
        SELECT * FROM filmes;
    ",
    "buscarFilme" => "
        SELECT * FROM filmes
        WHERE filme_id = :id;
    ",
    "atualizarFilme" => "
        UPDATE filmes
        SET
            titulo = COALESCE(NULLIF(:titulo, ''), titulo),
            genero = COALESCE(NULLIF(:genero, ''), genero),
            categoria = COALESCE(NULLIF(:categoria, ''), categoria),
            ano_de_lancamento = COALESCE(
                NULLIF(:ano_de_lancamento, ''), ano_de_lancamento
            )
        WHERE filme_id = :id;
    ",
    "deletarFilme" => "
        DELETE FROM filmes
        WHERE filme_id = :id;
    "
];

$emprestimosSQL = [
    "cadastrarEmprestimo" => "
        INSERT INTO emprestimos (
            cliente_id,
            filme_id,
            usuario_id,
            status_emprestimo,
            data_da_devolucao
        ) VALUES (
            :cliente_id,
            :filme_id,
            :usuario_id,
            :status_emprestimo,
            NULLIF(:data_da_devolucao, '')
        );
    ",
    "buscarEmprestimos" => "
        SELECT * FROM emprestimos;
    ",
    "buscarEmprestimo" => "
        SELECT * FROM emprestimos
        WHERE emprestimo_id = :id;
    ",
    "atualizarEmprestimo" => "
        UPDATE emprestimos
        SET
            cliente_id = COALESCE(NULLIF(:cliente_id, ''), cliente_id),
            filme_id = COALESCE(NULLIF(:filme_id, ''), filme_id),
            usuario_id = COALESCE(NULLIF(:usuario_id, ''), usuario_id),
            status_emprestimo = COALESCE(
                NULLIF(:status_emprestimo, ''), status_emprestimo
            ),
            data_da_devolucao = COALESCE(
                NULLIF(:data_da_devolucao, ''), data_da_devolucao
            )
        WHERE emprestimo_id = :id;
    ",
    "deletarEmprestimo" => "
        DELETE FROM emprestimos
        WHERE emprestimo_id = :id;
    "
];