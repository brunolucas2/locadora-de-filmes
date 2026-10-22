
<?php

require_once dirname(__DIR__) . '\utils\verificarSessao.php';

verificarSessao(true);

$router = "../../src/routes/usuariosRouter.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Locadora</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/usuarios.css">
</head>
<body>

<header>
    <h1 class="titlesupe">Gerenciamento de Usuários</h1>

    <style>
        .titlesupe {
            color: whitesmoke;
        }
    </style>
</header>

<nav>
    <a href="dashboard.php">Início</a>
    <a href="clientes.php">Clientes</a>
    <a href="filmes.php">Filmes</a>
    <a href="emprestimos.php">Empréstimos</a>
    <a href="usuarios.php">Usuários</a>
</nav>

<main>
    <h2>Cadastrar usuário</h2>

    <form action="<?= $router ?>?rota=usuario/cadastrar" method="POST">
        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>CPF</label>
        <input type="text" name="cpf" maxlength="11" required>

        <label>Senha</label>
        <input type="password" name="senha" required>

        <label>Nível</label>
        <select name="nivel">
            <option value="comum">Comum</option>
            <option value="administrador">Administrador</option>
        </select>

        <button type="submit">Cadastrar</button>
    </form>

    <hr>

    <h2>Buscar usuários</h2>

    <form action="<?= $router ?>?rota=usuario/buscarUsuario" method="GET">
        <input type="hidden" name="rota" value="usuario/buscarUsuario">

        <label>ID do usuário</label>
        <input type="number" name="id" min="1" required>

        <button type="submit">Buscar</button>
    </form>

    <a href="<?= $router ?>?rota=usuario/buscarUsuarios">
        Listar todos os usuários
    </a>
    

    <hr>

    <h2>Atualizar usuário</h2>

    <form action="<?= $router ?>?rota=usuario/atualizar" method="POST">
        <label>ID do usuário</label>
        <input type="number" name="id" min="1" required>

        <label>Novo nome</label>
        <input type="text" name="nome">

        <label>Novo CPF</label>
        <input type="text" name="cpf" maxlength="11">

        <label>Nova senha</label>
        <input type="password" name="senha">

        <button type="submit">Atualizar</button>
    </form>

    <hr>

    <h2>Excluir usuário</h2>

    <form action="<?= $router ?>?rota=usuario/deletar"
          method="POST"
          onsubmit="return confirm('Deseja excluir este usuário?')">

        <label>ID do usuário</label>
        <input type="number" name="id" min="1" required>

        <button type="submit">Excluir</button>
    </form>

    <hr>

    <form action="../src/utils/logout.php" method="POST">
        <button type="submit">Sair</button>
    </form>
</main>

</body>
</html>
