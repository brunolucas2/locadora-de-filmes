<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

require_once dirname(__DIR__) . "/controllers/ClientesController.php";
require_once dirname(__DIR__) . "/utils/validarPost.php";

$clientesPage = "../../../public/clientes.php";
$rota = $_GET["rota"] ?? "";

$metodosPermitidos = [
    "cliente/buscarClientes" => "GET",
    "cliente/buscarCliente" => "GET",
    "cliente/cadastrar" => "POST",
    "cliente/atualizar" => "POST",
    "cliente/deletar" => "POST"
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
    case "cliente/buscarClientes":
        rota_buscarClientes();
        break;

    case "cliente/buscarCliente":
        rota_buscarCliente();
        break;

    case "cliente/cadastrar":
        rota_cadastrarCliente();
        break;

    case "cliente/atualizar":
        rota_atualizarCliente();
        break;

    case "cliente/deletar":
        rota_deletarCliente();
        break;
}

function rota_cadastrarCliente(): void
{
    global $clientesPage;

    $campos = [
        "nome",
        "cpf",
        "telefone"
    ];

    validador($campos, $_POST, $clientesPage);

    try {
        cadastrarCliente($_POST);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit("Erro ao cadastrar cliente.");
    }

    #header("Location: $clientesPage");

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode("Cliente Cadastrado com sucesso", JSON_UNESCAPED_UNICODE);
    exit;
}

function rota_buscarClientes(): void
{
    header("Content-Type: application/json; charset=utf-8");

    try {
        $clientes = buscarClientes();

        echo json_encode([
            "clientes" => $clientes,
            "mensagem" => empty($clientes)
                ? "Nenhum cliente cadastrado."
                : ""
        ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);

        echo json_encode([
            "clientes" => [],
            "mensagem" => "Erro ao buscar clientes."
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

function rota_buscarCliente(): void
{
    header("Content-Type: application/json; charset=utf-8");

    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $cliente = buscarCliente($id);

        if ($cliente === false) {
            http_response_code(404);
            echo json_encode(
                "Não existe cliente com esse ID no sistema.",
                JSON_UNESCAPED_UNICODE
            );
            exit;
        }

        echo json_encode($cliente, JSON_UNESCAPED_UNICODE);
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("Erro no servidor.", JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function rota_atualizarCliente(): void
{
    #global $clientesPage;

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id  <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (atualizarCliente($_POST)) {
            echo json_encode("Cliente Atualizado com sucesso", JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Não foi possivel Atualizar o Cliente\nID do cliente não encontrado");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("ERRO NO SERVIDOR\nNão foi possivel Atualizar o Cliente");
        exit;
    }
}

function rota_deletarCliente(): void
{
    header("Content-Type: application/json; charset=utf-8");
    
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id  <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (deletarCliente($id)) {
            echo json_encode("Cliente deletado com sucesso", JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Não foi possivel deletar o Cliente\nID do cliente não encontrado");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("ERRO NO SERVIDOR\nNão foi possivel deletar o Cliente");
        exit;
    }
}
