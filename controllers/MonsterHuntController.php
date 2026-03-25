<?php

function monsterHuntController($pdo) {

    /* -----------------------------
       Inputs
    ------------------------------*/
    $rarity = $_GET['rarity'] ?? 'Common';
    if(!in_array($rarity, ['Common','Rare'])) $rarity = 'Common';

    $selectedSquad = $_GET['squadID'] ?? '';
    $playerLevel   = isset($_GET['playerLevel']) ? (int)$_GET['playerLevel'] : 6;

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
       Monsters
    ------------------------------*/
    $monsters = [];

    if ($buildPlan && $selectedSquad) {
        $monsters = fetchAll($pdo, "
            SELECT m.monsterID, m.name, m.type, m.health, m.strength
            FROM squad_monster sm
            JOIN monster m ON m.monsterID = sm.monsterID
            WHERE sm.squadID = ?
        ", [$selectedSquad]);
    }

    $enemyType = $monsters[0]['type'] ?? null;

    /* -----------------------------
       Creatures
    ------------------------------*/
    $creatures = [];

    if ($buildPlan && $useCreatures) {
        $creatures = fetchAll($pdo, "/* your creature query */", [$playerLevel]);

        foreach ($creatures as &$c) {
            $c['bonuses'] = $c['bonuses'] ? json_decode($c['bonuses'], true) : [];
        }
        unset($c);
    }


    /* -----------------------------
    Attack Groups
    ------------------------------*/
    $attackGroups = buildAttackGroups($creatures ?? [], $enemyType ?? null, 2);

    /* -----------------------------
    Return to View
    ------------------------------*/
    return [
        'inputs' => [
            'rarity' => $rarity,
            'selectedSquad' => $selectedSquad,
            'playerLevel' => $playerLevel,
            'useFighters' => $useFighters,
            'useCreatures' => $useCreatures,
            'buildPlan' => $buildPlan,
        ],
        'data' => [
            'squads' => $squads,
            'creatures' => $creatures,
            'enemyType' => $enemyType,
            'attackGroups' => $attackGroups,
        ]
    ];
}