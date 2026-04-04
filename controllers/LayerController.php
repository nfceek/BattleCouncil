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
    $difficulty     = $_GET['difficulty'] ?? 'rare';
    $config         = getDifficultyConfig($difficulty);
    $selectedSquad  = (int)($_GET['squadID'] ?? 0);
    $playerLevel    = (int)($_GET['playerLevel'] ?? 6);
    $useFighters    = isset($_GET['useFighters']);
    $useCreatures   = isset($_GET['useCreatures']);
    $buildLayerPlan = isset($_GET['buildLayerPlan']);
    $troops         = $_GET['troops'] ?? [];

    /* -----------------------------
       Squads
    ------------------------------*/
    $squads = fetchAll($pdo, "
        SELECT squadID, name, level, rarity, image_base
        FROM monster_squad
        WHERE rarity = ? AND level >= ?
        ORDER BY level ASC, name ASC
    ", [$config['rarity'], $config['minLevel']]);

    /* -----------------------------
       Monsters
    ------------------------------*/
    $monsters = [];
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
                (sm.quantity * m.health) AS total_health,
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
       Unit Pools: Fighters + Creatures
    ------------------------------*/
    $units = [];
    $fighters = [];
    $creatures = [];

    if ($buildLayerPlan && !empty($troops)) {

        // --- Fighters ---
        $conditions = [];
        $params = [];

        foreach ($troops as $type => $data) {
            if ($type === 'bst') continue; // creatures handled separately
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
                    'fighter' AS source
                FROM fighter f
                WHERE " . implode(' OR ', $conditions) . "
                ORDER BY f.type, f.strength DESC
            ", $params);

            $units = array_merge($units, $fighters);
        }

        // --- Creatures ---
        if (!empty($troops['bst']['enabled']) && $useCreatures) {
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
            ", [$playerLevel]);

            $units = array_merge($units, $creatures);
        }
    }

    /* -----------------------------
       Fighter Options for Dropdowns
    ------------------------------*/
    $fighterOptions = [];

    foreach ($units as $u) {
        $id = $u['id'] ?? null;
        $fighterOptions[] = [
            'id'       => $id,
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

    // sort strongest first
    usort($fighterOptions, fn($a,$b) => $b['score'] <=> $a['score']);

    /* -----------------------------
       Final Return
    ------------------------------*/
    return [
        'inputs' => [
            'difficulty'     => $difficulty,
            'selectedSquad'  => $selectedSquad,
            'playerLevel'    => $playerLevel,
            'buildLayerPlan' => $buildLayerPlan,
            'troops'         => $troops
        ],

        // UI data
        'squads'     => $squads,
        'monsters'   => $monsters,
        'squadStats' => $squadStats,

        // config
        'layerCount' => $config['layers'] ?? 3,
        'config'     => $config,

        // Unit pools
        'fighters'       => $fighters,
        'creatures'      => $creatures,
        'units'          => $units,
        'fighterOptions' => $fighterOptions,

        // future engine output
        'bonusMatrix' => []
    ];
}