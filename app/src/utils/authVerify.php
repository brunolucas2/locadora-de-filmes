
<?php

require_once __DIR__ . "/jwt.php";

function verificarAutorizacao(bool $admin = false): void
{
    $token = $_COOKIE["access_token"] ?? "";

    if ($token === "") {
        http_response_code(401);
        exit("Usuário não autenticado.");
    }

    $dados = validarJWT($token);

    if ($dados === false) {
        http_response_code(401);
        exit("Token inválido ou expirado.");
    }

    if ($admin && $dados->nivel !== "administrador") {
        http_response_code(403);
        exit("Acesso permitido apenas para administradores.");
    }
}
