<?php

function getDatabaseConnection(): PDO
{
    $config = require __DIR__ . "/config.php";

    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=%s",
        $config["host"],
        $config["database"],
        $config["charset"]
    );

    return new PDO($dsn, $config["username"], $config["password"], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}
