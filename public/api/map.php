<?php
require_once '../controllers/MapController.php';

$pdo = new PDO(...);

$kingdomId = $_GET['k'] ?? 274;

$controller = new MapController();
$data = $controller->getKingdomMap($pdo, $kingdomId);

header('Content-Type: application/json');
echo json_encode($data);