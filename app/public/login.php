<?php
session_start();

if (
    isset($_SESSION["usuario"]["exp"]) &&
    time() < $_SESSION["usuario"]["exp"]
) {
    if ($_SESSION["usuario"]["nivel"] == "administrador") {
        header("Location: /pages/usuarios.php");
        exit;
    } else {
        header("Location: /pages/locadora.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Locadora</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <main>
        <h1>Locadora de Filmes</h1>
        <h2>Entrar</h2>

        <form action="../src/routes/authRouter.php" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" required>

            <button type="submit">Entrar</button>
        </form>
    </main>

</body>

</html>