<?php

function squadController($pdo) {

    /* -----------------------------
       Inputs
    ------------------------------*/
    $rarity = $_GET['rarity'] ?? 'Common';
    if (!in_array($rarity, ['Common','Rare'])) $rarity = 'Common';

    $selectedSquad = isset($_GET['squadID']) ? (int)$_GET['squadID'] : 0;
    $playerLevel   = isset($_GET['playerLevel']) ? (int)$_GET['playerLevel'] : 8;

    $useFighters  = isset($_GET['useFighters']);
    $useCreatures = isset($_GET['useCreatures']);
    $buildPlan    = isset($_GET['buildPlan']);

    /* -----------------------------
       Squads
    ------------------------------*/
    $squads = fetchAll($pdo, "
        SELECT squadID, name, level, rarity, image_base
        FROM monster_squad
        WHERE rarity = ?
        ORDER BY name, level
    ", [$rarity]);

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

    $enemyType = $monsters[0]['type'] ?? null;

    /* -----------------------------
       Units (Creatures + Fighters)
    ------------------------------*/
    $units = [];
    $creatures = [];

    if ($buildPlan) {

        if ($useFighters) {
            $fighters = getFighters($pdo, $playerLevel, 'Reg');
            $units = array_merge($units, $fighters);
        }

        if ($useCreatures) {

            $creatures = fetchAll($pdo, "
                SELECT 
                    c.creatureID,
                    c.name,
                    c.type,
                    c.level,
                    c.strength,
                    c.health,
                    c.imgpath,
                    JSON_OBJECTAGG(cb.bonus_against, cb.bonus_percent) AS bonuses
                FROM creature c
                LEFT JOIN creature_bonus cb ON cb.creatureID = c.creatureID
                WHERE c.level = ?
                GROUP BY c.creatureID
                ORDER BY c.strength DESC
                LIMIT 12
            ", [$playerLevel]);

            $i = 1;
            foreach ($creatures as &$c) {
                $c['formation_no'] = $i++;
                $c['bonuses'] = $c['bonuses'] ? json_decode($c['bonuses'], true) : [];
            }
            unset($c);

            $units = array_merge($units, $creatures);
        }
    }

    /* -----------------------------
       Attack Engine
    ------------------------------*/
    $attackGroups = [];
    $counterSignal = [];

    if ($buildPlan && $selectedSquad > 0 && !empty($monsters)) {

        $weak = ['Mel'=>0,'Mtd'=>0,'Rng'=>0,'Fly'=>0,'Oth'=>0];

        foreach ($monsters as $m) {
            foreach ($weak as $k => $_) {
                $weak[$k] += $m["bonus_" . strtolower($k)];
            }
        }

        foreach ($weak as $k => $v) {
            $weak[$k] = round($v / max(count($monsters),1));
            $counterSignal[$k] = $v == 0 ? 'green' : ($v > 50 ? 'red' : 'yellow');
        }

        $scores = [];

        foreach ($units as $u) {
            $base = $u['strength'] ?? 0;

            $score =
                ($u['attack_vs_mel'] ?? $base) * (100 - $weak['Mel']) +
                ($u['attack_vs_mtd'] ?? $base) * (100 - $weak['Mtd']) +
                ($u['attack_vs_rng'] ?? $base) * (100 - $weak['Rng']) +
                ($u['attack_vs_fly'] ?? $base) * (100 - $weak['Fly']);

            $scores[] = $u + ['score' => $score];
        }

        usort($scores, fn($a,$b) => $b['score'] <=> $a['score']);

        $groups = array_chunk(array_slice($scores, 0, 12), 1);

        foreach ($groups as $g) {
            $attackGroups[] = $g;
            if (count($attackGroups) >= 4) break;
        }
    }

    /* -----------------------------
       Image Resolution
    ------------------------------*/
    $imagePath = resolveSquadImage($squadStats ?? []);   
    
    /*     
    echo '<pre>';
    echo "=== EXTRACT CHECK ===\n";
    //echo '<pre>'; print_r($data); exit;  
    //var_dump($squads ?? null);
    //var_dump($squadSelected ?? null);
    var_dump($squadStats ?? null);
    echo '</pre>';
    
    echo '<pre>';
    echo "FINAL IMAGE PATH:\n";
    var_dump($imagePath);
    echo "EXISTS:\n";
    var_dump(file_exists($_SERVER['DOCUMENT_ROOT'] . str_replace('/battlecouncil','',$imagePath)));
    echo '</pre>';
    */

    /* -----------------------------
       Final Return
    ------------------------------*/
    return [
        'inputs' => [
            'rarity'        => $rarity,
            'selectedSquad' => $selectedSquad,
            'playerLevel'   => $playerLevel,
            'useFighters'   => $useFighters,
            'useCreatures'  => $useCreatures,
            'buildPlan'     => $buildPlan,
        ],
        'data' => [
            'squads'        => $squads,
            'monsters'      => $monsters,
            'squadStats'    => $squadStats,
            'creatures'     => $creatures,
            'enemyType'     => $enemyType,
            'attackGroups'  => $attackGroups,
            'counterSignal' => $counterSignal,
            'imagePath'     => $imagePath
        ]
    ];
}