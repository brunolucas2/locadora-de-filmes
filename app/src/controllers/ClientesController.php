<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";

function cadastrarCliente (array $cliente): void
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["cadastrarCliente"]);
    $stmt->execute([
        ":nome" => $cliente["nome"],
        ":cpf" => $cliente["cpf"],
        ":telefone" => $cliente["telefone"]
    ]);
}

function buscarClientes (): array
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["buscarCLientes"]);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function buscarCliente (int $id): array|false
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["buscarCLiente"]);
    $stmt->execute([":id" => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function atualizarCliente (array $cliente): bool
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["atualizarCliente"]);

    return $stmt-> execute([
        ":nome" => $cliente["nome"] ?? "",
        ":cpf" => $cliente["cpf"] ?? "",
        ":telefone" => $cliente["telefone"] ?? "",
        ":id" => $cliente["id"]
    ]);
}

function deletarCLiente (int $id): bool
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["deletarCliente"]);

    $stmt->execute(["id" => $id]);

    return $stmt->rowCount() > 0;
}