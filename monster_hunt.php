<?php
$pageClass = 'page-monster-hunt';
include __DIR__ . '/includes/header.php'; 

/* page protected
requireLogin(); 
  if(hasRole('veteran')){
      // show veteran+ content
  }
*/


/* -----------------------------
   Helpers
------------------------------*/

function fetchAll($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
    return fetchAll($pdo, "
        SELECT
        f.name, f.type, f.level, f.strength, f.health,
        'fighter' AS unit_class,

        ROUND((f.strength+(f.strength*f.strength_bonus/100))+
        (f.strength*COALESCE(MAX(CASE WHEN fb.bonus_against='Mel' THEN fb.bonus_percent END),0)/100)) attack_vs_mel,

        ROUND((f.strength+(f.strength*f.strength_bonus/100))+
        (f.strength*COALESCE(MAX(CASE WHEN fb.bonus_against='Rng' THEN fb.bonus_percent END),0)/100)) attack_vs_rng,

        ROUND((f.strength+(f.strength*f.strength_bonus/100))+
        (f.strength*COALESCE(MAX(CASE WHEN fb.bonus_against='Mtd' THEN fb.bonus_percent END),0)/100)) attack_vs_mtd,

        ROUND((f.strength+(f.strength*f.strength_bonus/100))+
        (f.strength*COALESCE(MAX(CASE WHEN fb.bonus_against='Fly' THEN fb.bonus_percent END),0)/100)) attack_vs_fly

        FROM fighter f
        LEFT JOIN fighter_bonus fb ON fb.fighterID=f.fighterID
        WHERE f.level <= ? AND unit = ?
        GROUP BY f.fighterID
        ORDER BY f.level,f.name
    ", [$playerLevel, $unitType]);
}


/* -----------------------------
   Inputs
------------------------------*/

$rarity = $_GET['rarity'] ?? 'Common';

$monsterHealthList = [];
$monsterTotalHealth = 0;

$monsterStrengthList = [];
$monsterTotalStrength = 0;

if(!in_array($rarity, ['Common','Rare'])) $rarity = 'Common';
  $selectedSquad    = $_GET['squadID'] ?? '';
  $playerLevel      = isset($_GET['playerLevel']) ? (int)$_GET['playerLevel'] : 6;
  $useFighters      = isset($_GET['useFighters']);
  $useCreatures     = isset($_GET['useCreatures']);
  $buildPlan        = isset($_GET['buildPlan']);



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
   Attacks
------------------------------*/

$commonShots = getAttacksByRarity($pdo, 'Common');
$rareShots   = getAttacksByRarity($pdo, 'Rare');

/* -----------------------------
   History + Captains
------------------------------*/

$killShots = fetchAll($pdo, "
    SELECT
        sa.squadAttackID,
        sa.rarity,
        ms.name AS squadName,
        ms.level AS squadLevel,
        COUNT(sau.attackUnitID) AS unitCount
    FROM squad_attack sa
    LEFT JOIN monster_squad ms ON ms.squadID = sa.squadID
    LEFT JOIN squad_attack_units sau ON sau.squadAttackID = sa.squadAttackID
    GROUP BY sa.squadAttackID
    ORDER BY sa.rarity, ms.name
");

$captains = fetchAll($pdo, "
    SELECT characterID, name
    FROM characters
    WHERE role = 'Captain'
    ORDER BY name
");

/* split history */
$historyCommon = $historyRare = $historyEpic = [];

foreach($killShots as $k){
    if($k['rarity']=='Common') $historyCommon[] = $k;
    elseif($k['rarity']=='Rare') $historyRare[] = $k;
    elseif($k['rarity']=='Epic') $historyEpic[] = $k;
}

/* -----------------------------
   Fetch Squad + Monsters
------------------------------*/
$monsters = [];
if ($selectedSquad) {
    $monsters = fetchAll($pdo, "
        SELECT m.monsterID, m.name, m.type, m.health, m.strength
        FROM squad_monster sm
        JOIN monster m ON m.monsterID = sm.monsterID
        WHERE sm.squadID = ?
    ", [$selectedSquad]);
}

// Enemy type = type of first monster in squad
$enemyType = $monsters[0]['type'] ?? null;

/* -----------------------------
   Fetch Creatures
------------------------------*/
$creatures = [];
if($buildPlan && $useCreatures){
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
        LIMIT 5
    ", [$playerLevel]);

    // decode bonuses
    foreach ($creatures as &$c) {
        $c['bonuses'] = $c['bonuses'] ? json_decode($c['bonuses'], true) : [];
    }
    unset($c);
}

/* -----------------------------
   Squad Details
------------------------------*/

$squadStats = null;
$monsters   = [];

if ($selectedSquad) {

    $stats = fetchAll($pdo, "
        SELECT name, level, valor, frags, xp, rarity, image_base
        FROM monster_squad
        WHERE squadID = ? AND rarity = ?
    ", [$selectedSquad, $rarity]);

    $squadStats = $stats[0] ?? null;

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
   build creature array
------------------------------*/

$units = [];
$creatures = []; // make sure it's defined

if($buildPlan){

    if($useFighters){
        $units = array_merge($units, getFighters($pdo,$playerLevel,'Reg'));
    }

    if($useCreatures){
        // fetch creatures into $creatures
        $creatures = fetchAll($pdo,"
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
          LIMIT 5
        ", [$playerLevel]);

        // decode bonuses & add formation number
        $i = 1;
        foreach ($creatures as &$c) {
            $c['formation_no'] = $i++;
            $c['bonuses'] = $c['bonuses'] ? json_decode($c['bonuses'], true) : [];
        }
        unset($c);

        // merge into units if you need both fighters and creatures
        $units = array_merge($units, $creatures);
    }
}

// now $creatures is defined, $units contains everything


  /* new creatures section */

  function calculateScore($creature, $enemyType = null) {
      $base = $creature['base_attack'] ?? 100;
      $creaturePath = $creature['imgpath'] ?? null;
      $bonusPercent = $creature['bonus_percent'] ?? 0;
      $bonusType = $creature['bonus_type'] ?? null;

      // Match vs enemy type (basic for now)
      $typeMultiplier = ($enemyType && $bonusType === $enemyType) ? 1.0 : 0.5;

      return $base * (1 + $bonusPercent / 100) * $typeMultiplier;
  }
/* -----------------------------
   Build Attack Groups
------------------------------*/
function buildAttackGroups($creatures, $enemyType = null, $maxGroups = 2) {
    foreach ($creatures as &$c) {
        $best = 0;
        $match = 0;
        foreach ($c['bonuses'] as $type => $val) {
            if ($val > $best) $best = $val;
            if ($enemyType && strtolower($type) === strtolower($enemyType)) $match = $val;
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

$attackGroups = buildAttackGroups($creatures, $enemyType, 2);
/* -----------------------------
   Total Units
------------------------------*/
$totalUnits = 0;
foreach ($attackGroups as $g) $totalUnits += count($g);

?>
<pre>
creatures:
<?php print_r($creatures); ?>

enemyType:
<?php print_r($enemyType); ?>
</pre>
  <script>
  window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
  </script>
  <pre><?php print_r($attackGroups); ?></pre>
<?php


/* -----------------------------
   Attack Engine (unchanged logic)
------------------------------*/
$attackGroups = [];
$counterSignal = [];

if($buildPlan && $selectedSquad && $monsters){

    $weak = ['Mel'=>0,'Mtd'=>0,'Rng'=>0,'Fly'=>0,'Oth'=>0];

    foreach($monsters as $m){
        foreach($weak as $k=>$_){
            $weak[$k] += $m["bonus_".strtolower($k)];
            
        }
    }

    foreach($weak as $k=>$v){
        $weak[$k] = round($v / max(count($monsters),1));
        $counterSignal[$k] = $v == 0 ? 'green' : ($v > 50 ? 'red' : 'yellow');
    }

    $scores = [];

    foreach($units as $u){
        $score =
            ($u['attack_vs_mel'] ?? 0) * (100-$weak['Mel']) +
            ($u['attack_vs_mtd'] ?? 0) * (100-$weak['Mtd']) +
            ($u['attack_vs_rng'] ?? 0) * (100-$weak['Rng']) +
            ($u['attack_vs_fly'] ?? 0) * (100-$weak['Fly']);

        $scores[] = $u + ['score'=>$score];
    }

    usort($scores, fn($a,$b) => $b['score'] <=> $a['score']);

    //$groups = array_chunk(array_slice($scores,0,12),3);
    $groups = array_chunk(array_slice($scores,0,12),1);

    foreach($groups as $g){
        $attackGroups[] = $g;
        if(count($attackGroups)>=4) break;

    }
}

/* -----------------------------
   Squad Image Resolution
------------------------------*/

function resolveSquadImage($squadStats) {

    $base = strtolower($squadStats['image_base'] ?? 'default');
    $rarity = strtolower($squadStats['rarity'] ?? 'common');
    $level = (int)($squadStats['level'] ?? 1);

    $paths = [
        "/images/monsters/{$base}_{$rarity}_lvl{$level}.png",
        "/images/monsters/{$base}_{$rarity}.png",
        "/images/monsters/{$base}.png"
    ];

    foreach ($paths as $path) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            return $path;
        }
    }

    return '/images/monsters/default.png';
}

$imagePath = resolveSquadImage($squadStats ?? []);

  ?>

  <body class="page-monster-hunt">
    <div class="container">


    </div>


  <?php include __DIR__ .  '/includes/footer.php'; ?>
  </body>
</html>