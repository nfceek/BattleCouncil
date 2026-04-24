<?php

header('Content-Type: application/json');

// 🔴 CRITICAL: prevent HTML errors leaking
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/controllers/MysticController.php';

try {

    $input = json_decode(file_get_contents("php://input"), true);
    $question = $input['question'] ?? '';

    $controller = new MysticController($pdo);
    $result = $controller->ask(['question' => $question]);

    echo json_encode([
        'success' => true,
        'answer' => $result['answer_text'],
        'type' => $result['type'],
        'rarity' => $result['rarity']
    ]);

} catch (Throwable $e) {

    // 🔥 RETURN CLEAN JSON ERROR
    echo json_encode([
        'success' => false,
        'answer' => 'The oracle is disturbed...',
        'error' => $e->getMessage()
    ]);
}