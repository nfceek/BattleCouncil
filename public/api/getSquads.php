<?php
require_once __DIR__ . '/../../controllers/LayerController.php';
require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

$difficulty = $_GET['difficulty'] ?? 'rare';

$config = getDifficultyConfig($difficulty);

$squads = fetchAll($pdo, "
    SELECT squadID, name, level
    FROM monster_squad
    WHERE rarity = ?
      AND level >= ?
    ORDER BY level ASC, name ASC
", [
    $config['rarity'],
    $config['minLevel']
]);

echo json_encode($squads);
exit;