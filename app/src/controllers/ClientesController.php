<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";
require_once dirname(__DIR__) . "/utils/criptografarCPF.php";

function cadastrarCliente (array $cliente): void
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["cadastrarCliente"]);
    $stmt->execute([
        ":nome" => $cliente["nome"],
        ":cpf" => criptografarCPF($cliente["cpf"]),
        ":telefone" => $cliente["telefone"]
    ]);
}

function buscarClientes(): array
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["buscarClientes"]);
    $stmt->execute();

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($clientes as &$cliente) {
        $cpf = descriptografarCPF($cliente["cpf"]);

        $cliente["cpf"] = $cpf !== false ? $cpf : "CPF indisponível";
    }

    unset($cliente);

    return $clientes;
}

function buscarCliente(int $id): array|false
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["buscarCliente"]);
    $stmt->execute([":id" => $id]);

    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente === false) {
        return false;
    }

    $cpf = descriptografarCPF($cliente["cpf"]);
    $cliente["cpf"] = $cpf !== false ? $cpf : "CPF indisponível";

    return $cliente;
}

function atualizarCliente (array $cliente): bool
{
    global $pdo, $clientesSQL;

    $stmt = $pdo->prepare($clientesSQL["atualizarCliente"]);

    return $stmt-> execute([
        ":nome" => $cliente["nome"] ?? "",
        ":cpf" => criptografarCPF($cliente["cpf"]) ?? "",
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