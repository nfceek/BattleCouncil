<?php
/*
echo '<pre>';
var_dump($_GET);
echo '</pre>';
*/
/**
 * Difficulty → query rules
 */
function getDifficultyConfig(string $difficulty): array {

    $difficulty = strtolower($difficulty);

    return match ($difficulty) {

        'common' => [
            'rarity'   => 'common',
            'minLevel' => 20,
            'layers'   => 2
        ],

        'epic' => [
            'rarity'   => 'epic',
            'minLevel' => 20,
            'layers'   => 4
        ],

        default => [ // rare
            'rarity'   => 'rare',
            'minLevel' => 5,
            'layers'   => 3
        ]
    };
}

function layerController(PDO $pdo): array {

    /* -----------------------------
       Inputs
    ------------------------------*/
    $difficulty = $_GET['difficulty'] ?? 'rare';
    $config = getDifficultyConfig($difficulty);

    $selectedSquad = isset($_GET['squadID']) ? (int)$_GET['squadID'] : 0;
    $playerLevel   = isset($_GET['playerLevel']) ? (int)$_GET['playerLevel'] : 6;

    $useFighters  = isset($_GET['useFighters']);
    $useCreatures = isset($_GET['useCreatures']);
    $buildLayerPlan = isset($_GET['buildLayerPlan']);

    /* -----------------------------
       Squads (FIXED QUERY)
    ------------------------------*/
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

    /* -----------------------------
       Squad + Monsters
    ------------------------------*/
    $monsters   = [];
    $squadStats = null;

    if ($selectedSquad > 0) {

        $squadStats = fetchOne($pdo, "
            SELECT name, level, rarity, image_base, valor, frags, xp
            FROM monster_squad
            WHERE squadID = ?
        ", [$selectedSquad]);

        $monsters = fetchAll($pdo, "
            SELECT 
                m.monsterID,
                m.name,
                m.type,
                sm.quantity,
                m.health,
                m.strength,

                (sm.quantity * m.health)   AS total_health,
                (sm.quantity * m.strength) AS total_strength,

                COALESCE(MAX(CASE WHEN mb.bonus_against='Mel' THEN mb.bonus_percent END),0) AS bonus_mel,
                COALESCE(MAX(CASE WHEN mb.bonus_against='Mtd' THEN mb.bonus_percent END),0) AS bonus_mtd,
                COALESCE(MAX(CASE WHEN mb.bonus_against='Rng' THEN mb.bonus_percent END),0) AS bonus_rng,
                COALESCE(MAX(CASE WHEN mb.bonus_against='Fly' THEN mb.bonus_percent END),0) AS bonus_fly,
                COALESCE(MAX(CASE WHEN mb.bonus_against='Oth' THEN mb.bonus_percent END),0) AS bonus_oth

            FROM squad_monster sm
            JOIN monster m ON m.monsterID = sm.monsterID
            LEFT JOIN monster_bonus mb ON mb.monsterID = m.monsterID
            WHERE sm.squadID = ?
            GROUP BY m.monsterID
            ORDER BY total_strength DESC
        ", [$selectedSquad]);
    }

    /* -----------------------------
       Final Return (CLEAN)
    ------------------------------*/
    return [
        'inputs'     => [
            'difficulty'   => $difficulty,
            'selectedSquad'=> $selectedSquad,
            'playerLevel'  => $playerLevel,
            'buildLayerPlan'    => $buildLayerPlan
        ],
        'squads'     => $squads,
        'monsters'   => $monsters,
        'squadStats' => $squadStats,
        'layerCount' => $config['layers'] ?? 3,   // 👈 if you add layers to config
        'config'     => $config,                  // 👈 FIX
        'bonusMatrix'=> []                        // 👈 placeholder (safe for now)
    ];
}