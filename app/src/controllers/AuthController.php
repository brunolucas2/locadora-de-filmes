<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";
require_once dirname(__DIR__) . "/utils/jwt.php";

function auth(array $usuario): string|false
{
    global $pdo, $authSQL;

    if (
        !isset($usuario["email"], $usuario["senha"]) ||
        !is_string($usuario["email"]) ||
        !is_string($usuario["senha"]) ||
        $usuario["email"] === "" ||
        $usuario["senha"] === ""
    ) {
        return false;
    }

    $stmt = $pdo->prepare($authSQL["autenticar"]);
    $stmt->execute([
        ":email" => $usuario["email"]
    ]);

    $usuarioBanco = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioBanco === false) {
        return false;
    }

    if (!password_verify($usuario["senha"], $usuarioBanco["senha_hash"])) {
        return false;
    }

    $dados = buscarDados($usuario["email"]);

    if ($dados === false) {
        return false;
    }

    return gerarJWT($dados["id"], $dados["nivel"]);
}

function buscarDados(string $email): array|false
{
    global $pdo, $authSQL;

    $stmt = $pdo->prepare($authSQL["buscarDados"]);
    $stmt->execute([
        ":email" => $email
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