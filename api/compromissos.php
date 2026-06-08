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

    if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
        // Parse incoming raw data (PHP doesn't populate $_DELETE)
        parse_str(file_get_contents('php://input'), $data);

        $id = isset($data['id']) ? (int) $data['id'] : 0;

        if ($id <= 0) {
            http_response_code(422);
            echo json_encode([
                "success" => false,
                "message" => "ID inválido para exclusão."
            ]);
            exit;
        }

        $statement = $pdo->prepare("DELETE FROM compromissos WHERE id = :id");
        $statement->execute(["id" => $id]);

        echo json_encode([
            "success" => $statement->rowCount() > 0,
            "deleted" => $statement->rowCount()
        ]);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "PUT") {
        // Parse raw input for PUT
        parse_str(file_get_contents('php://input'), $data);

        $id = isset($data['id']) ? (int) $data['id'] : 0;
        $task = isset($data['task']) ? trim($data['task']) : "";

        if ($id <= 0 || $task === "") {
            http_response_code(422);
            echo json_encode([
                "success" => false,
                "message" => "ID ou tarefa inválidos para atualização."
            ]);
            exit;
        }

        $statement = $pdo->prepare(
            "UPDATE compromissos SET tarefa = :tarefa WHERE id = :id"
        );
        $statement->execute([
            "tarefa" => $task,
            "id" => $id
        ]);

        echo json_encode([
            "success" => $statement->rowCount() > 0,
            "updated" => $statement->rowCount()
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
