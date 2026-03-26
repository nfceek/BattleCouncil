
<?php

function getAttacksByRarity($pdo, $rarity) {
    return fetchAll($pdo, "
        SELECT 
            sa.squadAttackID,
            sa.gameID,
            sa.squadID,
            sa.rarity,
            sa.level,
            sa.troop,
            sa.qty,
            sa.characterID,
            sa.loss,
            c.name AS captainName
        FROM squad_attack sa
        LEFT JOIN characters c ON c.characterID = sa.characterID
        WHERE sa.gameID = 1
        AND sa.rarity = ?
        ORDER BY sa.level, sa.squadAttackID
    ", [$rarity]);
}

function getFighters($pdo, $playerLevel, $unitType) {
    return fetchAll($pdo, "/* your full query unchanged */", [$playerLevel, $unitType]);
}
