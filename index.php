
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

if (
    isset($_SESSION["usuario"]["id"], $_SESSION["usuario"]["nivel"], $_SESSION["usuario"]["exp"]) &&
    time() < $_SESSION["usuario"]["exp"]
) {
    if ($_SESSION["usuario"]["nivel"] == "administrador") {
        header("Location: app/public/pages/usuarios.php");
        exit;
    } else {
        header("Location: app/public/pages/locadora.php");
        exit;
    }
}

$_SESSION = [];
session_destroy();

header("Location: app/public/login.php");
exit;
