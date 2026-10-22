<?php

    require_once dirname(__DIR__) . "/database/conn.php";
    require_once dirname(__DIR__) . "/database/querys.php";
    require_once dirname(__DIR__) . "/utils/criptografarCPF.php";

    function hashearSenha(string $senha): string
    {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        return $senha_hash;
    }

    function cadastrarUsuario(array $usuario): void
    {
        global $pdo, $usuariosSQL;

        $stmt = $pdo->prepare($usuariosSQL["cadastrarUsuario"]);
        $stmt->execute([
            ":nome" => $usuario["nome"],
            ":email" => $usuario["email"],
            ":cpf" => criptografarCPF($usuario["cpf"]),
            ":senha_hash" => hashearSenha($usuario["senha"]),
            ":nivel" => $usuario["nivel"] ?? "comum"
        ]);
    }

    function buscarUsuarios(): array
    {
        global $pdo, $usuariosSQL;

        $stmt = $pdo->prepare($usuariosSQL["buscarUsuarios"]);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as &$usuario) {
            $cpf = descriptografarCPF($usuario["cpf"]);

            $usuario["cpf"] = $cpf !== false ? $cpf : "CPF indisponível";
        }

        unset($usuario);

        return $usuarios;
    }

    function buscarUsuario(int $id): array|false
    {
        global $pdo, $usuariosSQL;

        $stmt = $pdo->prepare($usuariosSQL["buscarUsuario"]);
        $stmt->execute([":id" => $id]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario === false) {
            return false;
        }

        $cpf = descriptografarCPF($usuario["cpf"]);
        $usuario["cpf"] = $cpf !== false ? $cpf : "CPF indisponível";

        return $usuario;
    }

    function atualizarUsuario(array $usuario): bool
    {
        global $pdo, $usuariosSQL;

        $senha = $usuario["senha"] ?? "";

        $stmt = $pdo->prepare($usuariosSQL["atualizarUsuario"]);

        return $stmt->execute([
            ":nome" => $usuario["nome"] ?? "",
            ":cpf" => empty($cliente["cpf"]) ? "" : criptografarCPF($cliente["cpf"]),
            ":senha_hash" => $senha !== "" ? hashearSenha($senha) : "",
            ":id" => $usuario["id"]
        ]);
    }

    function deletarUsuario(int $id): bool
    {
        global $pdo, $usuariosSQL;

        $stmt = $pdo->prepare($usuariosSQL["deletarUsuario"]);

        $stmt->execute(["id" => $id]);

        return $stmt->rowCount() > 0;
    }
