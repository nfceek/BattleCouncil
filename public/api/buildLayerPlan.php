<?php
require_once __DIR__ . '/../../controllers/LayerController.php';
require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

// 🔥 READ RAW JSON INPUT
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// 🔥 FAIL FAST (critical debug)
if (!$input) {
    echo json_encode([
        'error' => 'No JSON payload received',
        'raw' => $raw
    ]);
    exit;
}

// 🔥 INJECT INTO $_GET (so controller works unchanged)
$_GET['troops']        = $input['troops'] ?? [];
$_GET['playerLevel']   = $input['playerLevel'] ?? 6;
$_GET['difficulty']    = $input['difficulty'] ?? 'rare';
$_GET['squadID']       = $input['squadID'] ?? 0;
$_GET['useCreatures']  = !empty($input['useCreatures']) ? 1 : null;
$_GET['useFighters']   = !empty($input['useFighters']) ? 1 : null;

/* 🔥 DEBUG (TEMP — REMOVE AFTER)
if (empty($_GET['troops'])) {
    echo json_encode([
        'error' => 'Troops missing after injection',
        'input' => $input
    ]);
    exit;
}
*/

// ✅ RUN CONTROLLER
$result = layerController($pdo);

// ✅ RETURN ONLY WHAT JS NEEDS
echo json_encode([
    'fighterOptions' => $result['fighterOptions'] ?? [],
    'monsters'       => $result['monsters'] ?? [],
    'layerCount'     => $result['layerCount'] ?? 3
]);