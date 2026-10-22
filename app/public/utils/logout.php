<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Método não permitido.");
}

$_SESSION = [];
session_destroy();

setcookie(session_name(), "", [
    "expires" => time() - 7300,
    "path" => "/",
    "httponly" => true
]);

setcookie("access_token", "", [
    "expires" => time() - 7300,
    "path" => "/",
    "secure" => false,
    "httponly" => true,
    "samesite" => "Strict"
]);

header("Location: ../../public/login.php");
exit;
