<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

function verificarSessao(bool $admin = false): void
{
    $usuario = $_SESSION["usuario"] ?? [];

    if (
        !isset($usuario["id"], $usuario["nivel"], $usuario["exp"]) ||
        time() >= $usuario["exp"]
    ) {
        $_SESSION = [];
        session_destroy();

        header("Location: ../login.php");
        exit;
    }

    if ($admin && $usuario["nivel"] !== "administrador") {
        http_response_code(403);
        exit("Acesso permitido apenas para administradores.");
    }
}
