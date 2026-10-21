<?php

require_once dirname(__DIR__, 3) . "/vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function obterChaveJWT(): string
{
    static $chave = null;

    if ($chave !== null) {
        return $chave;
    }

    $caminho = dirname(__DIR__, 2) . "/.env";

    if (!is_readable($caminho)) {
        throw new RuntimeException("Arquivo .env não encontrado ou sem permissão de leitura.");
    }

    $env = parse_ini_file($caminho, false, INI_SCANNER_RAW);

    if ($env === false) {
        throw new RuntimeException("Não foi possível ler o arquivo .env.");
    }

    $segredo = $env["JWT_SECRET"] ?? "";

    if (strlen($segredo) < 32) {
        throw new RuntimeException("JWT_SECRET deve conter pelo menos 32 bytes.");
    }

    $chave = $segredo;

    return $chave;
}

function gerarJWT(int $id, string $nivel): string
{
    if (
        $id <= 0 ||
        !in_array($nivel, ["administrador", "comum"], true)
    ) {
        throw new InvalidArgumentException("Dados do usuário inválidos.");
    }

    $agora = time();

    $payload = [
        "sub" => (string) $id,
        "nivel" => $nivel,
        "iat" => $agora,
        "exp" => $agora + 7200
    ];

    return JWT::encode($payload, obterChaveJWT(), "HS256");
}

function validarJWT(string $token): object|false
{
    $chave = obterChaveJWT();

    try {
        $dados = JWT::decode($token, new Key($chave, "HS256"));

        if (
            !isset($dados->sub, $dados->nivel, $dados->exp) ||
            !is_string($dados->sub) ||
            !ctype_digit($dados->sub) ||
            (int) $dados->sub <= 0 ||
            !is_int($dados->exp) ||
            !in_array($dados->nivel, ["administrador", "comum"], true)
        ) {
            return false;
        }

        return $dados;
    } catch (\UnexpectedValueException $e) {
        return false;
    }
}