<?php
function getDatabaseConnection(): PDO
{
    $config = require __DIR__ . "/config.php";

    // Try MySQL first (normal project setup)
    try {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $config["host"],
            $config["database"],
            $config["charset"]
        );

        $pdo = new PDO($dsn, $config["username"], $config["password"], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $pdo;
    } catch (Throwable $e) {
        // Fall back to SQLite when MySQL is not available.
        $sqlitePath = __DIR__ . "/../data/simplepractice.sqlite";
        $sqliteDir = dirname($sqlitePath);

        if (!is_dir($sqliteDir)) {
            @mkdir($sqliteDir, 0755, true);
        }

        $dsn = "sqlite:" . $sqlitePath;

        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Ensure table exists in SQLite
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS compromissos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tarefa TEXT NOT NULL,
                data_criacao DATETIME NOT NULL DEFAULT (datetime('now'))
            )"
        );

        return $pdo;
    }
}
