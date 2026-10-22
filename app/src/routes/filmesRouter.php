
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.cookie_httponly", "1");
    session_start();
}

require_once dirname(__DIR__) . "/controllers/FilmesController.php";
require_once dirname(__DIR__) . "/utils/validarPost.php";

$filmesPage = "../../../public/filmes.php";
$rota = $_GET["rota"] ?? "";

$metodosPermitidos = [
    "filme/buscarFilmes" => "GET",
    "filme/buscarFilme" => "GET",
    "filme/cadastrar" => "POST",
    "filme/atualizar" => "POST",
    "filme/deletar" => "POST"
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
    case "filme/buscarFilmes":
        rota_buscarFilmes();
        break;

    case "filme/buscarFilme":
        rota_buscarFilme();
        break;

    case "filme/cadastrar":
        rota_cadastrarFilme();
        break;

    case "filme/atualizar":
        rota_atualizarFilme();
        break;

    case "filme/deletar":
        rota_deletarFilme();
        break;
}

function rota_cadastrarFilme(): void
{
    global $filmesPage;

    $campos = [
        "titulo",
        "genero",
        "categoria",
        "ano_de_lancamento"
    ];

    validador($campos, $_POST, $filmesPage);

    try {
        cadastrarFilme($_POST);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit("Erro ao cadastrar filme.");
    }

    #header("Location: $filmesPage");

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode("Filme Cadastrado com sucesso", JSON_UNESCAPED_UNICODE);
    exit;
}

function rota_buscarFilmes(): void
{
    header("Content-Type: application/json; charset=utf-8");

    try {
        $filmes = buscarFilmes();

        echo json_encode([
            "filmes" => $filmes,
            "mensagem" => empty($filmes)
                ? "Nenhum filme cadastrado."
                : ""
        ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);

        echo json_encode([
            "filmes" => [],
            "mensagem" => "Erro ao buscar filmes."
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

function rota_buscarFilme(): void
{
    header("Content-Type: application/json; charset=utf-8");

    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $filme = buscarFilme($id);

        if ($filme === false) {
            http_response_code(404);
            echo json_encode(
                "Não existe filme com esse ID no sistema.",
                JSON_UNESCAPED_UNICODE
            );
            exit;
        }

        echo json_encode($filme, JSON_UNESCAPED_UNICODE);
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("Erro no servidor.", JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function rota_atualizarFilme(): void
{
    #global $filmesPage;

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (atualizarFilme($_POST)) {
            echo json_encode("Filme Atualizado com sucesso", JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Não foi possivel Atualizar o filme\nID do filme não encontrado");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("ERRO NO SERVIDOR\nNão foi possivel Atualizar o filme");
        exit;
    }
}

function rota_deletarFilme(): void
{
    header("Content-Type: application/json; charset=utf-8");

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode("Informe um ID válido.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        if (deletarFilme($id)) {
            echo json_encode("Filme deletado com sucesso", JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Não foi possivel deletar o Filme\nID do Filme não encontrado");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode("ERRO NO SERVIDOR\nNão foi possivel deletar o Filme");
        exit;
    }
}
