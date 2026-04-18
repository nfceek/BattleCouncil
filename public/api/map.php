<?php

require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

// prevent HTML errors
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {

    $k = $_GET['k'] ?? null;

    /* =========================
       WORLD MODE
    ========================== */
    if (!$k) {

        $stmt = $pdo->query("
            SELECT 
                kingdomID,
                name,
                capital_x,
                capital_y,
                capital_bldg
            FROM kingdoms
        ");

        $kingdoms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'mode' => 'world',
            'kingdoms' => $kingdoms
        ]);
        exit;
    }

    /* =========================
       KINGDOM MODE
    ========================== */
    $stmt = $pdo->prepare("
        SELECT 
            kingdomID,
            name,
            capital_x,
            capital_y,
            capital_bldg AS icon
        FROM kingdoms
        WHERE kingdomID = ?
        LIMIT 1
    ");
    $stmt->execute([$k]);

    $kingdom = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kingdom) {
        echo json_encode(['error' => 'Kingdom not found']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            name,
            shortname,
            x,
            y,
            language
        FROM clans
        WHERE kingdom = ?
    ");
    $stmt->execute([$k]);

    $clans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'mode' => 'kingdom',
        'kingdom' => $kingdom,
        'clans' => $clans
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'error' => 'API failure',
        'message' => $e->getMessage()
    ]);
}