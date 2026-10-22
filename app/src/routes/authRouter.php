<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

require_once dirname(__DIR__) . "/controllers/authController.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Allow: POST");
    http_response_code(405);
    exit("Método não permitido.");
}

$email = $_POST["email"] ?? "";
$senha = $_POST["senha"] ?? "";

if (!is_string($email) || !is_string($senha)) {
    http_response_code(400);
    exit("Dados inválidos.");
}

$email = trim($email);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit("Informe um e-mail válido.");
}

if ($senha === "") {
    http_response_code(400);
    exit("Informe a senha.");
}

if ($senha === "") {
    http_response_code(400);
    exit("Informe a senha.");
}

try {
    $jwt = auth([
        "email" => $email,
        "senha" => $senha
    ]);

    if ($jwt === false) {
        http_response_code(401);
        exit("email ou senha inválidos.");
    }

    // Disponível porque o controller já inclui jwt.php.
    $dados = validarJWT($jwt);

    if ($dados === false) {
        http_response_code(500);
        exit("Erro ao validar autorização.");
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Erro no servidor.");
} catch (RuntimeException | InvalidArgumentException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Erro ao gerar autorização.");
}

session_regenerate_id(true);

header("Content-Type: text/html; charset=UTF-8");

setcookie("access_token", $jwt, [
    "expires" => $dados->exp,
    "path" => "/",
    "secure" => false,
    "httponly" => true,
    "samesite" => "Strict"
]);

$_SESSION["usuario"] = [
    "id" => (int) $dados->sub,
    "nivel" => $dados->nivel
];

header("Location: ../../index.php");
exit;