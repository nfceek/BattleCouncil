<?php
require_once __DIR__ . '/../../config/config.php';

$stmt = $pdo->query("
    SELECT kingdom_number, capital_x, capital_y, capital_bldg
    FROM kingdoms
");

$kingdoms = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['kingdoms' => $kingdoms]);
exit;