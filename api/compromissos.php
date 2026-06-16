<?php

header("Content-Type: application/json; charset=utf-8");

require __DIR__ . "/database.php";

$useFileStorage = false;
$jsonFile = __DIR__ . "/../Public/assets/data/compromissos.json";

try {
    $pdo = getDatabaseConnection();
} catch (Throwable $e) {
    $pdo = null;
    $useFileStorage = true;

    // Ensure data directory exists
    $dir = dirname($jsonFile);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    if (!file_exists($jsonFile)) {
        file_put_contents($jsonFile, json_encode([]));
    }
}

function loadItems(string $file): array
{
    $data = @file_get_contents($file);
    if ($data === false) return [];
    $items = json_decode($data, true);
    return is_array($items) ? $items : [];
}

function saveItems(string $file, array $items): bool
{
    return file_put_contents($file, json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

try {
    if ($useFileStorage) {
        // File-based API
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $task = trim($_POST["task"] ?? "");
            if ($task === "") {
                http_response_code(422);
                echo json_encode(["success" => false, "message" => "Informe uma tarefa para registrar o compromisso."]);
                exit;
            }

            $items = loadItems($jsonFile);
            $ids = array_column($items, 'id');
            $newId = $ids ? (max($ids) + 1) : 1;
            $now = (new DateTime())->format('Y-m-d H:i:s');
            $items[] = ["id" => $newId, "tarefa" => $task, "data_criacao" => $now];
            saveItems($jsonFile, $items);

            echo json_encode(["success" => true, "message" => "Compromisso registrado (arquivo)", "id" => $newId]);
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $items = loadItems($jsonFile);
            usort($items, function ($a, $b) {
                return strcmp($b['data_criacao'], $a['data_criacao']);
            });
            echo json_encode(["success" => true, "items" => $items]);
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
            parse_str(file_get_contents('php://input'), $data);
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            if ($id <= 0) {
                http_response_code(422);
                echo json_encode(["success" => false, "message" => "ID inválido para exclusão."]);
                exit;
            }
            $items = loadItems($jsonFile);
            $before = count($items);
            $items = array_values(array_filter($items, fn($it) => (int)$it['id'] !== $id));
            $deleted = $before - count($items);
            saveItems($jsonFile, $items);
            echo json_encode(["success" => $deleted > 0, "deleted" => $deleted]);
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "PUT") {
            parse_str(file_get_contents('php://input'), $data);
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            $task = isset($data['task']) ? trim($data['task']) : "";
            if ($id <= 0 || $task === "") {
                http_response_code(422);
                echo json_encode(["success" => false, "message" => "ID ou tarefa inválidos para atualização."]);
                exit;
            }
            $items = loadItems($jsonFile);
            $updated = 0;
            foreach ($items as &$it) {
                if ((int)$it['id'] === $id) {
                    $it['tarefa'] = $task;
                    $updated++;
                    break;
                }
            }
            saveItems($jsonFile, $items);
            echo json_encode(["success" => $updated > 0, "updated" => $updated]);
            exit;
        }

        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não permitido."]);
        exit;
    }

    // If we have a real PDO connection, use the SQL-backed logic
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
