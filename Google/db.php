<?php

require_once __DIR__ . '/config.php';

/**
 * Open a PDO connection with the application database.
 */
function openDatabaseConnection(): PDO
{
    static $connection;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getRequiredConfig(DB_HOST);
    $name = getRequiredConfig(DB_NAME);
    $user = getRequiredConfig(DB_USER);
    $password = getRequiredConfig(DB_PASS);

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $connection = new PDO($dsn, $user, $password, $options);

    return $connection;
}
