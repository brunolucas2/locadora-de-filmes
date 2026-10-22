<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

function verificarAutorizacao(bool $admin = false): void
{
    if (
        !isset($_SESSION["usuario"]["id"]) ||
        !isset($_SESSION["usuario"]["nivel"])
    ) {
        http_response_code(401);
        exit("Usuário não autenticado.");
    }

    $nivel = $_SESSION["usuario"]["nivel"];

    if (!in_array($nivel, ["administrador", "comum"], true)) {
        http_response_code(403);
        exit("Acesso negado.");
    }

    if ($admin && $nivel !== "administrador") {
        http_response_code(403);
        exit("Apenas administradores podem acessar.");
    }
}
