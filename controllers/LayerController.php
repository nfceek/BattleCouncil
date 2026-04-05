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

// 🔥 SUPPORT JSON API INPUT
$rawInput = file_get_contents('php://input');
$json = json_decode($rawInput, true);

if (is_array($json)) {
    $_GET = array_merge($_GET, $json);
}

    /* -----------------------------
       Inputs
    ------------------------------*/
    
    $troops       = $_GET['troops'] ?? [];
    $playerLevel  = (int)($_GET['playerLevel'] ?? 6);
    $difficulty   = $_GET['difficulty'] ?? 'rare';
    $selectedSquad= (int)($_GET['squadID'] ?? 0);

    $useCreatures = !empty($_GET['useCreatures']);
    $useFighters  = !empty($_GET['useFighters']);
    // $buildLayerPlan = isset($_GET['buildLayerPlan']);    v2.13.0
    $config = getDifficultyConfig($difficulty);
    
/*  
echo '<pre>';
echo "=== INPUT DEBUG ===\n";
print_r([
    'troops' => $troops,
    'playerLevel' => $playerLevel,
    'difficulty' => $difficulty,
    'squadID' => $selectedSquad,
    'useCreatures' => $useCreatures,
    'useFighters' => $useFighters
]);
echo '</pre>';
exit;
*/
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
   Unit Pools: Creatures + Fighters
------------------------------*/
$units = [];
$fighters = [];
$creatures = [];
$fighterOptions = []; // initialize once

// --- Creatures ---
if (!empty($troops['bst']['enabled']) && $useCreatures) {

    $creatureLevel = (int)($troops['bst']['level'] ?? $playerLevel);

    $creatures = fetchAll($pdo, "
        SELECT 
            c.creatureID AS id,
            c.name,
            'bst' AS type,
            c.level,
            c.strength,
            c.health,
            c.imgpath,
            'creature' AS source
        FROM creature c
        WHERE c.level = ?
        ORDER BY c.strength DESC
        LIMIT 12
    ", [$creatureLevel]);

    $units = array_merge($units, $creatures);
}

// --- Fighters --- v2.13.0
//if ($buildLayerPlan && !empty($_GET['troops'])) {
if ($useFighters && !empty($troops)) {
    $conditions = [];
    $params     = [];

    foreach ($troops as $type => $data) {
        if ($type === 'bst') continue; // already handled
        if (empty($data['enabled']) || empty($data['level'])) continue;

        $conditions[] = "(f.type = ? AND f.level = ? AND f.unit = 'Reg')";
        $params[] = $type;
        $params[] = (int)$data['level'];
    }

    if (!empty($conditions)) {
        $fighters = fetchAll($pdo, "
            SELECT 
                f.fighterID AS id,
                f.name,
                f.type,
                f.level,
                f.strength,
                f.health,
                f.imgpath,
                f.unit,
                'fighter' AS source
            FROM fighter f
            WHERE " . implode(' OR ', $conditions) . "
            ORDER BY f.type, f.strength DESC
        ", $params);

        // merge into units
        $units = array_merge($units, $fighters);
    }
}

/* -----------------------------
   Build Fighter Options Array
------------------------------*/
if (!empty($units)) {
    foreach ($units as $u) {
        $fighterOptions[] = [
            'id'       => $u['id'] ?? null,
            'name'     => $u['name'] ?? 'Unknown',
            'type'     => $u['type'] ?? 'unk',
            'level'    => $u['level'] ?? 0,
            'strength' => $u['strength'] ?? 0,
            'health'   => $u['health'] ?? 0,
            'unit'     => $u['unit'] ?? $u['source'] ?? 'creature',
            'img'      => $u['imgpath'] ?? '',
            'score'    => $u['strength'] ?? 0,
        ];
    }

    // strongest first
    usort($fighterOptions, fn($a,$b) => $b['score'] <=> $a['score']);
}
    /*
    echo '<pre>';
    echo "=== FIGHTER OPTIONS ===\n";
    print_r($fighterOptions);
    echo '</pre>';
    */
    /* -----------------------------
    Final Return (CLEAN + COMPLETE)
    ------------------------------*/
    return [
        'inputs' => [
            'difficulty'     => $difficulty,
            'selectedSquad'  => $selectedSquad,
            'playerLevel'    => $playerLevel,
            //'buildLayerPlan' => $buildLayerPlan
            
        ],

        // UI data
        'squads'     => $squads,
        'monsters'   => $monsters,
        'squadStats' => $squadStats,

        // config
        'layerCount' => $config['layers'] ?? 3,
        'config'     => $config,

        // NEW: unit pools
        'fighters'   => $fighters ?? [],
        'creatures'  => $creatures ?? [],
        'units'      => $units ?? [],
        'fighterOptions' => $fighterOptions, // <-- ADD THIS

        // future engine output
        'bonusMatrix'=> [] // placeholder (safe)
    ];
}