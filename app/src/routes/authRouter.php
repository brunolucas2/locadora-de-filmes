<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";
require_once dirname(__DIR__) . "/utils/jwt.php";

function auth(array $usuario): string|false
{
    global $pdo, $authSQL;

    if (
        !isset($usuario["cpf"], $usuario["senha"]) ||
        !is_string($usuario["cpf"]) ||
        !is_string($usuario["senha"]) ||
        $usuario["senha"] === ""
    ) {
        return false;
    }

    $stmt = $pdo->prepare($authSQL["autenticar"]);
    $stmt->execute([
        ":cpf" => $usuario["cpf"]
    ]);

    $usuarioBanco = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioBanco === false) {
        return false;
    }

    if (!password_verify($usuario["senha"], $usuarioBanco["senha_hash"])) {
        return false;
    }

    $dados = buscarDados($usuario["cpf"]);

    if ($dados === false) {
        return false;
    }

    return gerarJWT($dados["id"], $dados["nivel"]);
}

function buscarDados(string $cpf): array|false
{
    global $pdo, $authSQL;

    $stmt = $pdo->prepare($authSQL["buscarDados"]);
    $stmt->execute([
        ":cpf" => $cpf
    ]);

    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados === false) {
        return false;
    }

    return [
        "id" => (int) $dados["usuario_id"],
        "nivel" => $dados["nivel"]
    ];
}