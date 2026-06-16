<?php

require __DIR__ . '/api/database.php';

try {
    $pdo = getDatabaseConnection();
    echo "CONN_OK\n";
} catch (Throwable $e) {
    echo "CONN_ERR: " . $e->getMessage() . "\n";
}
