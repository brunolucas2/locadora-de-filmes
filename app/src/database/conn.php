<?php

$envPath = dirname(__DIR__, 3) . '/.env';
$env = parse_ini_file($envPath);

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$port = $env['DB_PORT'];
$database = $env['DB_NAME'];
$password = $env['DB_PASSWORD'];;
$charset = "utf8mb4";


$connUrl = "mysql:host=$host;dbname=$database;charset=$charset";

try {
    $pdo = new PDO (
        $connUrl,
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    die("Erro ao conectar!" . $e->getMessage());
}