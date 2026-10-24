<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('BLS_DB_HOST') ?: '127.0.0.1';
    $name = getenv('BLS_DB_NAME') ?: 'bls_ebook';
    $user = getenv('BLS_DB_USER') ?: 'root';
    $pass = getenv('BLS_DB_PASS') ?: '';

    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    $pdo->exec("SET time_zone = '+07:00'");

    return $pdo;
}