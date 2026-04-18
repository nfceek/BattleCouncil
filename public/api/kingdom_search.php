<?php

require_once __DIR__ . '/../../config/config.php';

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("
    SELECT kingdomID 
    FROM kingdoms
    WHERE kingdomID LIKE ?
    ORDER BY kingdomID
    LIMIT 10
");

$stmt->execute(["%$q%"]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));