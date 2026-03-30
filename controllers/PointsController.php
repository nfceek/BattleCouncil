<?php
require_once __DIR__ . '/../services/PointsService.php';

function pointsController(PDO $pdo) {
    $action = $_GET['action'] ?? '';

    $userId = 1; // replace with session user

    switch ($action) {

        case 'balance':
            echo json_encode([
                'balance' => PointsService::getBalance($pdo, $userId)
            ]);
            break;

        case 'ledger':
            echo json_encode([
                'ledger' => PointsService::getLedger($pdo, $userId)
            ]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
}