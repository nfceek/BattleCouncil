<?php

require_once __DIR__ . '/../../config/config.php';

$stmt = $pdo->query("
    SELECT languageID, name 
    FROM languages 
    WHERE is_active = 1
    ORDER BY name
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));