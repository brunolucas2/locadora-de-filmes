
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

require_once dirname(__DIR__) . "/utils/verificarAutorizacao.php";

verificarAutorizacao();

require_once dirname(__DIR__) . "/controllers/EmprestimosController.php";
require_once dirname(__DIR__) . "/utils/validarPost.php";

$emprestimosPage = "../../../public/emprestimos.php";
$rota = $_GET["rota"] ?? "";

$metodosPermitidos = [
    "emprestimo/buscarEmprestimos" => "GET",
    "emprestimo/buscarEmprestimo" => "GET",
    "emprestimo/cadastrar" => "POST",
    "emprestimo/atualizar" => "POST",
    "emprestimo/deletar" => "POST"
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
    case "emprestimo/buscarEmprestimos":
        rota_buscarEmprestimos();
        break;

    case "emprestimo/buscarEmprestimo":
        rota_buscarEmprestimo();
        break;

    case "emprestimo/cadastrar":
        rota_cadastrarEmprestimo();
        break;

    case "emprestimo/atualizar":
        rota_atualizarEmprestimo();
        break;

    case "emprestimo/deletar":
        rota_deletarEmprestimo();
        break;
}

function rota_cadastrarEmprestimo(): void
{
    global $emprestimosPage;

    $campos = [
        "cliente_id",
        "filme_id",
        "usuario_id",
        "status_emprestimo"
    ];

    validador($campos, $_POST, $emprestimosPage);

    try {
        cadastrarEmprestimo($_POST);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit("Erro ao cadastrar emprestimo.");
    }

    #header("Location: $emprestimosPage");

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode("Emprestimo Cadastrado com sucesso", JSON_UNESCAPED_UNICODE);
    exit;
}

function rota_buscarEmprestimos(): void
{
    header("Content-Type: application/json; charset=utf-8");

    try {
        $emprestimos = buscarEmprestimos();

        echo json_encode([
            "emprestimos" => $emprestimos,
            "mensagem" => empty($emprestimos)
                ? "Nenhum emprestimo cadastrado."
                : ""
        ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);

        echo json_encode([
            "emprestimos" => [],
            "mensagem" => "Erro ao buscar emprestimos."
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

function rota_buscarEmprestimo(): void
{
    header("Content-Type: application/json; charset=utf-8");

    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $emprestimo = buscarEmprestimo($id);

        if ($emprestimo === false) {
            http_response_code(404);
            echo json_encode(
                "Não existe emprestimo com esse ID no sistema.",
                JSON_UNESCAPED_UNICODE
            );
            exit;
        }

        echo json_encode($emprestimo, JSON_UNESCAPED_UNICODE);
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("Erro no servidor.", JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function rota_atualizarEmprestimo(): void
{
    #global $emprestimosPage;

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (atualizarEmprestimo($_POST)) {
            echo json_encode(
                "Emprestimo Atualizado com sucesso",
                JSON_UNESCAPED_UNICODE
            );
        } else {
            echo json_encode(
                "Não foi possivel Atualizar o emprestimo\nID do emprestimo não encontrado",
                JSON_UNESCAPED_UNICODE
            );
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(
            "ERRO NO SERVIDOR\nNão foi possivel Atualizar o emprestimo",
            JSON_UNESCAPED_UNICODE
        );
        exit;
    }
}

function rota_deletarEmprestimo(): void
{
    header("Content-Type: application/json; charset=utf-8");

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (deletarEmprestimo($id)) {
            echo json_encode(
                "Emprestimo deletado com sucesso",
                JSON_UNESCAPED_UNICODE
            );
        } else {
            echo json_encode(
                "Não foi possivel deletar o Emprestimo\nID do Emprestimo não encontrado",
                JSON_UNESCAPED_UNICODE
            );
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(
            "ERRO NO SERVIDOR\nNão foi possivel deletar o Emprestimo",
            JSON_UNESCAPED_UNICODE
        );
        exit;
    }
}
