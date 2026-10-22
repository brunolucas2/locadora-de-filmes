
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

require_once dirname(__DIR__) . "/utils/verificarAutorizacao.php";

verificarAutorizacao(true);

require_once dirname(__DIR__) . "/controllers/UsuariosController.php";
require_once dirname(__DIR__) . "/utils/validarPost.php";

$usuariosPage = "../../../public/usuarios.php";
$rota = $_GET["rota"] ?? "";

$metodosPermitidos = [
    "usuario/buscarUsuarios" => "GET",
    "usuario/buscarUsuario" => "GET",
    "usuario/cadastrar" => "POST",
    "usuario/atualizar" => "POST",
    "usuario/deletar" => "POST"
];

if (!is_string($rota) || !isset($metodosPermitidos[$rota])) {
    http_response_code(404);
    exit("Rota não encontrada.");
}

if ($_SERVER["REQUEST_METHOD"] !== $metodosPermitidos[$rota]) {
    http_response_code(405);
    exit("Método não permitido.");
}

switch ($rota) {
    case "usuario/buscarUsuarios":
        rota_buscarUsuarios();
        break;

    case "usuario/buscarUsuario":
        rota_buscarUsuario();
        break;

    case "usuario/cadastrar":
        rota_cadastrarUsuario();
        break;

    case "usuario/atualizar":
        rota_atualizarUsuario();
        break;

    case "usuario/deletar":
        rota_deletarUsuario();
        break;
}

function rota_cadastrarUsuario(): void
{
    global $usuariosPage;

    $campos = [
        "nome",
        "cpf",
        "email",
        "senha"
    ];

    validador($campos, $_POST, $usuariosPage);

    try {
        cadastrarUsuario($_POST);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit("Erro ao cadastrar usuario.");
    }

    #header("Location: $usuariosPage");

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode("Usuario Cadastrado com sucesso", JSON_UNESCAPED_UNICODE);
    exit;
}

function rota_buscarUsuarios(): void
{
    header("Content-Type: application/json; charset=utf-8");

    try {
        $usuarios = buscarUsuarios();

        echo json_encode([
            "usuarios" => $usuarios,
            "mensagem" => empty($usuarios)
                ? "Nenhum usuario cadastrado."
                : ""
        ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);

        echo json_encode([
            "usuarios" => [],
            "mensagem" => "Erro ao buscar usuarios."
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

function rota_buscarUsuario(): void
{
    header("Content-Type: application/json; charset=utf-8");

    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $usuario = buscarUsuario($id);

        if ($usuario === false) {
            http_response_code(404);
            echo json_encode(
                "Não existe usuario com esse ID no sistema.",
                JSON_UNESCAPED_UNICODE
            );
            exit;
        }

        echo json_encode($usuario, JSON_UNESCAPED_UNICODE);
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("Erro no servidor.", JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function rota_atualizarUsuario(): void
{
    #global $usuariosPage;

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (atualizarUsuario($_POST)) {
            echo json_encode("Usuario Atualizado com sucesso", JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Não foi possivel Atualizar o usuario\nID do usuario não encontrado");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("ERRO NO SERVIDOR\nNão foi possivel Atualizar o usuario");
        exit;
    }
}

function rota_deletarUsuario(): void
{
    header("Content-Type: application/json; charset=utf-8");
    
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id  <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (deletarUsuario($id)) {
            echo json_encode("Usuario deletado com sucesso", JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Não foi possivel deletar o Usuario\nID do Usuario não encontrado");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("ERRO NO SERVIDOR\nNão foi possivel deletar o Usuario");
        exit;
    }
}
