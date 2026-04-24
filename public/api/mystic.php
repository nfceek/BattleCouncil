<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {

    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../controllers/MysticController.php';

    if (!isset($pdo)) {
        throw new Exception("PDO not found");
    }

    $input = json_decode(file_get_contents("php://input"), true);
    $question = $input['question'] ?? '';

    $controller = new MysticController($pdo);

    $result = $controller->ask([
        'question' => $question
    ]);

    echo json_encode([
        'success' => true,
        'answer' => $result['answer_text'] ?? 'No answer',
        'type' => $result['type'] ?? 'neutral',
        'rarity' => $result['rarity'] ?? 'common'
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}