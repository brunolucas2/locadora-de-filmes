<?php
require_once dirname(__DIR__) . "/utils/verificarSessao.php";
verificarSessao();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locadora de Filmes</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/locadora.css">
    <script src="../js/locadora.js" defer></script>
</head>

<body>
    <header>
        <h1>Locadora de Filmes</h1>
    </header>

    <nav>
        <button type="button" class="aba ativa" data-pagina="clientes">Clientes</button>
        <button type="button" class="aba" data-pagina="filmes">Filmes</button>
        <button type="button" class="aba" data-pagina="emprestimos">Empréstimos</button>
        <?php if (($_SESSION["usuario"]["nivel"] ?? "") === "administrador"): ?>
            <a href="usuarios.php">Usuários</a>
        <?php endif; ?>
        <form action="../../src/utils/logout.php" method="POST" class="logout">
            <button type="submit">Sair</button>
        </form>
    </nav>

    <main>
        <p id="mensagem" role="status" aria-live="polite"></p>

        <section id="clientes" class="pagina">
            <h2>Clientes</h2>
            <h3>Cadastrar cliente</h3>
            <form data-tipo="clientes" data-acao="cadastrar">
                <label>Nome <input name="nome" required></label>
                <label>CPF <input name="cpf" maxlength="11" minlength="11" pattern="[0-9]{11}" required></label>
                <label>Telefone <input name="telefone" required></label>
                <button type="submit">Cadastrar</button>
            </form>
            <h3>Clientes cadastrados</h3>
            <button type="button" data-listar="clientes">Atualizar lista</button>
            <div class="tabela" id="lista-clientes"></div>
            <h3>Atualizar cliente</h3>
            <form data-tipo="clientes" data-acao="atualizar">
                <label>ID <input name="id" type="number" min="1" required></label>
                <label>Novo nome <input name="nome"></label>
                <label>Novo CPF <input name="cpf" maxlength="11" pattern="[0-9]{11}"></label>
                <label>Novo telefone <input name="telefone"></label>
                <button type="submit">Atualizar</button>
            </form>
            <h3>Deletar cliente</h3>
            <form data-tipo="clientes" data-acao="deletar">
                <label>ID <input name="id" type="number" min="1" required></label>
                <button type="submit">Deletar</button>
            </form>
        </section>

        <section id="filmes" class="pagina" hidden>
            <h2>Filmes</h2>
            <h3>Cadastrar filme</h3>
            <form data-tipo="filmes" data-acao="cadastrar">
                <label>Título <input name="titulo" required></label>
                <label>Gênero <input name="genero" required></label>
                <label>Categoria <input name="categoria" required></label>
                <label>Ano de lançamento <input name="ano_de_lancamento" type="number" min="1901" max="2155" required></label>
                <button type="submit">Cadastrar</button>
            </form>
            <h3>Filmes cadastrados</h3>
            <button type="button" data-listar="filmes">Atualizar lista</button>
            <div class="tabela" id="lista-filmes"></div>
            <h3>Atualizar filme</h3>
            <form data-tipo="filmes" data-acao="atualizar">
                <label>ID <input name="id" type="number" min="1" required></label>
                <label>Novo título <input name="titulo"></label>
                <label>Novo gênero <input name="genero"></label>
                <label>Nova categoria <input name="categoria"></label>
                <label>Novo ano <input name="ano_de_lancamento" type="number" min="1901" max="2155"></label>
                <button type="submit">Atualizar</button>
            </form>
            <h3>Deletar filme</h3>
            <form data-tipo="filmes" data-acao="deletar">
                <label>ID <input name="id" type="number" min="1" required></label>
                <button type="submit">Deletar</button>
            </form>
        </section>

        <section id="emprestimos" class="pagina" hidden>
            <h2>Empréstimos</h2>
            <h3>Cadastrar empréstimo</h3>
            <form data-tipo="emprestimos" data-acao="cadastrar">
                <label>ID do cliente <input name="cliente_id" type="number" min="1" required></label>
                <label>ID do filme <input name="filme_id" type="number" min="1" required></label>
                <label>ID do usuário <input name="usuario_id" type="number" min="1" required></label>
                <label>Status <select name="status_emprestimo">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select></label>
                <label>Data da devolução <input name="data_da_devolucao" type="date" required></label>
                <button type="submit">Cadastrar</button>
            </form>
            <h3>Empréstimos cadastrados</h3>
            <button type="button" data-listar="emprestimos">Atualizar lista</button>
            <div class="tabela" id="lista-emprestimos"></div>
            <h3>Atualizar empréstimo</h3>
            <form data-tipo="emprestimos" data-acao="atualizar">
                <label>ID <input name="id" type="number" min="1" required></label>
                <label>Novo ID do cliente <input name="cliente_id" type="number" min="1"></label>
                <label>Novo ID do filme <input name="filme_id" type="number" min="1"></label>
                <label>Novo ID do usuário <input name="usuario_id" type="number" min="1"></label>
                <label>Novo status <select name="status_emprestimo">
                        <option value="">Manter atual</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select></label>
                <label>Nova data da devolução <input name="data_da_devolucao" type="date"></label>
                <button type="submit">Atualizar</button>
            </form>
            <h3>Deletar empréstimo</h3>
            <form data-tipo="emprestimos" data-acao="deletar">
                <label>ID <input name="id" type="number" min="1" required></label>
                <button type="submit">Deletar</button>
            </form>
        </section>
    </main>
</body>

</html>