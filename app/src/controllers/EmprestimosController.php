<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";

function cadastrarEmprestimo(array $emprestimo): void
{
    global $pdo, $emprestimosSQL;

    $stmt = $pdo->prepare($emprestimosSQL["cadastrarEmprestimo"]);
    $stmt->execute([
        ":cliente_id" => $emprestimo["cliente_id"],
        ":filme_id" => $emprestimo["filme_id"],
        ":usuario_id" => $emprestimo["usuario_id"],
        ":status_emprestimo" => $emprestimo["status_emprestimo"] ?? "ativo",
        ":data_da_devolucao" => $emprestimo["data_da_devolucao"]
    ]);
}

function buscarEmprestimos(): array
{
    global $pdo, $emprestimosSQL;

    $stmt = $pdo->prepare($emprestimosSQL["buscarEmprestimos"]);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscarEmprestimo(int $id): array|false
{
    global $pdo, $emprestimosSQL;

    $stmt = $pdo->prepare($emprestimosSQL["buscarEmprestimo"]);
    $stmt->execute([":id" => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function atualizarEmprestimo(array $emprestimo): bool
{
    global $pdo, $emprestimosSQL;

    $stmt = $pdo->prepare($emprestimosSQL["atualizarEmprestimo"]);

    return $stmt->execute([
        ":cliente_id" => $emprestimo["cliente_id"] ?? "",
        ":filme_id" => $emprestimo["filme_id"] ?? "",
        ":usuario_id" => $emprestimo["usuario_id"] ?? "",
        ":status_emprestimo" => $emprestimo["status_emprestimo"] ?? "",
        ":data_da_devolucao" => $emprestimo["data_da_devolucao"] ?? "",
        ":id" => $emprestimo["id"]
    ]);
}

function deletarEmprestimo(int $id): bool
{
    global $pdo, $emprestimosSQL;

    $stmt = $pdo->prepare($emprestimosSQL["deletarEmprestimo"]);
    $stmt->execute([":id" => $id]);

    return $stmt->rowCount() > 0;
}