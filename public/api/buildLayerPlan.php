<?php
require_once __DIR__ . '/../../controllers/LayerController.php';
require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

// 🔥 READ RAW JSON INPUT
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// 🔥 FAIL FAST
if (!$input) {
    echo json_encode([
        'error' => 'No JSON payload received',
        'raw' => $raw
    ]);
    exit;
}

// =========================
// MAP INPUT → GET (legacy controller compatibility)
// =========================
$_GET['troops']        = $input['troops'] ?? [];
$_GET['playerLevel']   = $input['playerLevel'] ?? 6;
$_GET['difficulty']    = $input['difficulty'] ?? 'rare';
$_GET['squadID']       = $input['squadID'] ?? 0;
$_GET['useCreatures']  = !empty($input['useCreatures']) ? 1 : null;
$_GET['useFighters']   = !empty($input['useFighters']) ? 1 : null;

// ✅ IMPORTANT: BONUS VALUES (FIX)
$bonusStr = (int)($input['bonusStr'] ?? 100);
$bonusHlh = (int)($input['bonusHlh'] ?? 100);

$_GET['bonusStr'] = $bonusStr;
$_GET['bonusHlh'] = $bonusHlh;

// =========================
// RUN CONTROLLER
// =========================
$result = layerController($pdo);

// =========================
// RETURN JSON (INCLUDE BONUSES)
// =========================
echo json_encode([
    'fighterOptions' => $result['fighterOptions'] ?? [],
    'monsters'       => $result['monsters'] ?? [],
    'layerCount'     => $result['layerCount'] ?? 3,

    'squads'         => $result['squads'] ?? [],

    'bonusStr'       => $bonusStr,
    'bonusHlh'       => $bonusHlh
]);