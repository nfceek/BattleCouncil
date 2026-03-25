<?php

/* -----------------------------
   CORE: Build Attack Groups
------------------------------*/
function buildAttackGroups($creatures, $enemyType = null, $maxGroups = 2) {

    foreach ($creatures as &$c) {
        $best = 0;
        $match = 0;

        foreach ($c['bonuses'] as $type => $val) {
            if ($val > $best) $best = $val;
            if ($enemyType && strtolower($type) === strtolower($enemyType)) {
                $match = $val;
            }
        }

        $final = $match ?: $best;

        $c['score'] = ($c['strength'] ?? 100) * (1 + $final / 100);
    }
    unset($c);

    usort($creatures, fn($a,$b) => $b['score'] <=> $a['score']);

    $groups = [];

    foreach ($creatures as $c) {
        $groups[] = [$c];
        if (count($groups) >= $maxGroups) break;
    }

    return $groups;
}
