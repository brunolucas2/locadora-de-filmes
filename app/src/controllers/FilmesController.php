<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";

function cadastrarFilme(array $filme): void
{
    global $pdo, $filmesSQL;

    $stmt = $pdo->prepare($filmesSQL["cadastrarFilme"]);
    $stmt->execute([
        ":titulo" => $filme["titulo"],
        ":genero" => $filme["genero"],
        ":categoria" => $filme["categoria"],
        ":ano_de_lancamento" => $filme["ano_de_lancamento"]
    ]);
}

function buscarFilmes(): array
{
    global $pdo, $filmesSQL;

    $stmt = $pdo->prepare($filmesSQL["buscarFilmes"]);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscarFilme(int $id): array|false
{
    global $pdo, $filmesSQL;

    $stmt = $pdo->prepare($filmesSQL["buscarFilme"]);
    $stmt->execute([":id" => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function atualizarFilme(array $filme): bool
{
    global $pdo, $filmesSQL;

    $stmt = $pdo->prepare($filmesSQL["atualizarFilme"]);

    return $stmt->execute([
        ":titulo" => $filme["titulo"] ?? "",
        ":genero" => $filme["genero"] ?? "",
        ":categoria" => $filme["categoria"] ?? "",
        ":ano_de_lancamento" => $filme["ano_de_lancamento"] ?? "",
        ":id" => $filme["id"]
    ]);
}

function deletarFilme(int $id): bool
{
    global $pdo, $filmesSQL;

    $stmt = $pdo->prepare($filmesSQL["deletarFilme"]);
    $stmt->execute([":id" => $id]);

    return $stmt->rowCount() > 0;
}