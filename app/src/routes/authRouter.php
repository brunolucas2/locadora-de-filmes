<?php

require_once dirname(__DIR__) . "/database/conn.php";
require_once dirname(__DIR__) . "/database/querys.php";
require_once dirname(__DIR__, 2) . "/vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function chaveJWT(): string
{
    $chave = getenv("JWT_SECRET");

    if ($chave === false || strlen($chave) < 32) {
        throw new RuntimeException("Configure JWT_SECRET com pelo menos 32 bytes.");
    }

    return $chave;
}

function auth(array $usuario): string|false
{
    global $pdo, $authSQL;

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

    return gerarToken($dados);
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

function gerarToken(array $usuario): string
{
    $agora = time();

    $payload = [
        "sub" => (string) $usuario["id"],
        "nivel" => $usuario["nivel"],
        "iat" => $agora,
        "exp" => $agora + (5 * 60 * 60)
    ];

    return JWT::encode($payload, chaveJWT(), "HS256");
}

function validarToken(string $token): array|false
{
    $chave = chaveJWT();

    try {
        $dados = JWT::decode($token, new Key($chave, "HS256"));

        if (
            !isset($dados->sub, $dados->nivel, $dados->exp) ||
            !ctype_digit((string) $dados->sub) ||
            !in_array($dados->nivel, ["administrador", "comum"], true)
        ) {
            return false;
        }

        return [
            "id" => (int) $dados->sub,
            "nivel" => $dados->nivel
        ];
    } catch (\UnexpectedValueException $e) {
        return false;
    }
}