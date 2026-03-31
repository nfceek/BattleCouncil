<?php
require_once __DIR__ . '/../services/MonsterHuntService.php';

/**
 * Difficulty → rules mapping
 */
function getLayerConfig(string $difficulty): array {

    $difficulty = strtolower($difficulty);

    $config = match ($difficulty) {

        'common' => [
            'layers' => 2,
            'rarity' => 'common',
            'minLevel' => 20
        ],

        'epic' => [
            'layers' => 4,
            'rarity' => 'common',
            'minLevel' => 1
        ],

        default => [
            'layers' => 3,
            'rarity' => 'rare',
            'minLevel' => 10
        ]
    };

    return $config;
}


/**
 * Example: build bonus matrix (placeholder for now)
 * Later: replace with DB-driven squad attack data
 */
function buildBonusMatrix(int $layerCount): array {

    $matrix = [];

    for ($i = 1; $i <= $layerCount; $i++) {

        $matrix[$i] = [
            'mtd' => 0,
            'rng' => 0,
            'mel' => 0,
            'fly' => 0
        ];
    }

    return $matrix;
}


/**
 * Main Controller
 */
function layerController(PDO $pdo): array {

    // -------------------------
    // Inputs
    // -------------------------
    $inputs = [
        'difficulty'   => $_GET['difficulty'] ?? 'rare',
        'selectedSquad'=> $_GET['squadID'] ?? '',
        'layerCount'   => isset($_GET['layerCount']) ? (int)$_GET['layerCount'] : null,
        'layers'       => $_GET['layers'] ?? []
    ];

    // Validate difficulty
    $valid = ['common','rare','epic'];
    if (!in_array($inputs['difficulty'], $valid)) {
        $inputs['difficulty'] = 'rare';
    }

    // -------------------------
    // Config
    // -------------------------
    $config = getLayerConfig($inputs['difficulty']);

    // Allow UI override (bounded)
    $layerCount = $inputs['layerCount'] ?? $config['layers'];
    $layerCount = max(1, min(4, $layerCount));

    // -------------------------
    // Query squads
    // -------------------------
    $squads = fetchAll($pdo, "
        SELECT squadID, name, level, rarity, image_base
        FROM monster_squad
        WHERE rarity = ?
          AND level >= ?
        ORDER BY level ASC, name ASC
    ", [
        $config['rarity'],
        $config['minLevel']
    ]);

    // -------------------------
    // Bonus Matrix (future combat logic)
    // -------------------------
    $bonusMatrix = buildBonusMatrix($layerCount);

    // -------------------------
    // Return
    // -------------------------
    return [
        'inputs'      => $inputs,
        'squads'      => $squads,
        'layerCount'  => $layerCount,
        'config'      => $config,
        'bonusMatrix' => $bonusMatrix
    ];
}