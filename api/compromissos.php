<?php

header("Content-Type: application/json; charset=utf-8");

require __DIR__ . "/database.php";

try {
    $pdo = getDatabaseConnection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $task = trim($_POST["task"] ?? "");

        if ($task === "") {
            http_response_code(422);
            echo json_encode([
                "success" => false,
                "message" => "Informe uma tarefa para registrar o compromisso."
            ]);
            exit;
        }

        $statement = $pdo->prepare(
            "INSERT INTO compromissos (tarefa, data_criacao) VALUES (:tarefa, NOW())"
        );
        $statement->execute([
            "tarefa" => $task
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Compromisso registrado no banco de dados.",
            "id" => $pdo->lastInsertId()
        ]);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $statement = $pdo->query(
            "SELECT id, tarefa, data_criacao FROM compromissos ORDER BY data_criacao DESC"
        );

        echo json_encode([
            "success" => true,
            "items" => $statement->fetchAll()
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método não permitido."
    ]);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erro ao acessar o banco de dados."
    ]);
}
