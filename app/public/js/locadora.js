const config = {
    clientes: { arquivo: "clientesRouter.php", rota: "cliente", id: "cliente_id" },
    filmes: { arquivo: "filmesRouter.php", rota: "filme", id: "filme_id" },
    emprestimos: { arquivo: "emprestimosRouter.php", rota: "emprestimo", id: "emprestimo_id" }
};

const mensagem = document.getElementById("mensagem");

function mostrarMensagem(texto) {
    mensagem.textContent = texto;
}

function urlRota(tipo, acao) {
    return `../../src/routes/${config[tipo].arquivo}?rota=${config[tipo].rota}/${acao}`;
}

function abrirPagina(tipo) {
    document.querySelectorAll(".pagina").forEach(pagina => {
        pagina.hidden = pagina.id !== tipo;
    });
    document.querySelectorAll(".aba").forEach(botao => {
        botao.classList.toggle("ativa", botao.dataset.pagina === tipo);
    });
    mostrarMensagem("");
    listar(tipo);
}

async function listar(tipo) {
    const destino = document.getElementById(`lista-${tipo}`);
    destino.textContent = "Carregando...";

    try {
        const nomeAcao = "buscar" + tipo[0].toUpperCase() + tipo.slice(1);
        const resposta = await fetch(urlRota(tipo, nomeAcao), { credentials: "same-origin" });
        const texto = await resposta.text();
        if (!resposta.ok) throw new Error(texto || `Erro HTTP ${resposta.status}`);
        const dados = JSON.parse(texto);
        const itens = dados[tipo] || [];

        destino.replaceChildren();
        if (itens.length === 0) {
            destino.textContent = "Nenhum registro encontrado.";
            return;
        }

        // A tabela mostra somente os campos necessários, sem incluir senhas ou hashes.
        const campos = tipo === "clientes"
            ? ["cliente_id", "nome", "cpf", "telefone"]
            : tipo === "filmes"
                ? ["filme_id", "titulo", "genero", "categoria", "ano_de_lancamento"]
                : ["emprestimo_id", "cliente_id", "filme_id", "usuario_id", "status_emprestimo", "data_da_devolucao"];

        const tabela = document.createElement("table");
        const cabecalho = document.createElement("tr");
        campos.forEach(campo => {
            const th = document.createElement("th");
            th.textContent = campo.replaceAll("_", " ");
            cabecalho.append(th);
        });
        tabela.append(cabecalho);

        itens.forEach(item => {
            const linha = document.createElement("tr");
            campos.forEach(campo => {
                const td = document.createElement("td");
                td.textContent = item[campo] ?? "";
                linha.append(td);
            });
            tabela.append(linha);
        });
        destino.append(tabela);
    } catch (erro) {
        destino.textContent = "Não foi possível carregar os registros.";
        mostrarMensagem(erro.message);
    }
}

document.querySelectorAll(".aba").forEach(botao => {
    botao.addEventListener("click", () => abrirPagina(botao.dataset.pagina));
});

document.querySelectorAll("[data-listar]").forEach(botao => {
    botao.addEventListener("click", () => listar(botao.dataset.listar));
});

document.querySelectorAll("form[data-tipo]").forEach(formulario => {
    formulario.addEventListener("submit", async evento => {
        evento.preventDefault();

        const tipo = formulario.dataset.tipo;
        const acao = formulario.dataset.acao;
        if (acao === "deletar" && !confirm("Deseja deletar este registro?")) return;

        const dados = new URLSearchParams(new FormData(formulario));
        try {
            const resposta = await fetch(urlRota(tipo, acao), {
                method: "POST",
                credentials: "same-origin",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: dados.toString()
            });
            const texto = await resposta.text();
            let retorno;
            try { retorno = JSON.parse(texto); } catch { retorno = texto; }
            if (!resposta.ok) throw new Error(typeof retorno === "string" ? retorno : `Erro HTTP ${resposta.status}`);
            mostrarMensagem(typeof retorno === "string" ? retorno : "Operação concluída.");
            if (acao !== "atualizar" || resposta.ok) formulario.reset();
            await listar(tipo);
        } catch (erro) {
            mostrarMensagem(erro.message);
        }
    });
});

abrirPagina("clientes");
