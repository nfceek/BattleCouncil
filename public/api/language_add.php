<?php

require_once __DIR__ . '/../../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');

if (!$name) {
    echo json_encode(['error' => 'Invalid name']);
    exit;
}

// prevent duplicates
$stmt = $pdo->prepare("SELECT languageID FROM languages WHERE name = ?");
$stmt->execute([$name]);

if ($stmt->fetch()) {
    echo json_encode(['exists' => true]);
    exit;
}

// insert
$stmt = $pdo->prepare("INSERT INTO languages (name) VALUES (?)");
$stmt->execute([$name]);

echo json_encode([
    'success' => true,
    'id' => $pdo->lastInsertId(),
    'name' => $name
]);