<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/../config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/../helpers/auth.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

require_once __DIR__ . '/../services/PointsService.php';
require_once __DIR__ . '/../services/ClanServices.php';

// ==============================
// SERVICES & CONTROLLERS
// ==============================
require_once __DIR__ . '/../controllers/LayerController.php';

$data = layerController($pdo);

    $inputs         = $data['inputs'];
    $squads         = $data['squads'];
    $layerCount     = $data['layerCount'];   
    $config         = $data['config'];
    $bonusMatrix    = $data['bonusMatrix'];
    $monsters       = $data['monsters'] ?? [];
    $fighterOptions = $data['fighterOptions'] ?? [];

/*
echo '<pre>';
print_r($fighterOptions);
echo '</pre>';


echo '<pre>';
print_r($squads);
echo '</pre>';
*/
// ==============================
// EXAMPLE POINTS REWARD ASSIGNMENT -- FUTURE USE
/* ==============================
if ($battleResult['win']) {

    $pointsEarned = 10;

    // Example scaling
    if ($battleResult['rarity'] === 'rare') {
        $pointsEarned += 15;
    }

    PointsService::add(
        $pdo,
        $userId,
        $pointsEarned,
        'monster_hunt_win',
        $battleResult['hunt_id'] ?? null
    );
}
*/

// ==============================
// PAGE SETTINGS
// ==============================
$pageClass = 'Layering Calculator';

// ==============================
// HEADER
// ==============================
require_once __DIR__ . '/../includes/header.php';

?>
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<!-- MAIN CONTENT -->
<div class="container">

    <div class="mh-leader">
        <h1>Monster Hunting Calculator</h1>
    </div>

    <div class="layer-grid">

    <!-- SINGLE FORM (wrap everything) -->
    <form method="GET" id="layerForm">
        <!-- Card 1: Get Attack Group Info -->
<!-- Card 1: Attack Squad Selections -->
<div class="bc-layer-card">

    <div class="bc-img" style="height:40px;">
        <img src="/images/cards/war_table.jpg"
            style="width:100%; height:100%; object-fit:cover; opacity:.4;">
        <div class="bc-img-overlay">
            <div class="bc-img-title">Attack Squad Setup</div>
        </div>
    </div>

    <div class="bc-content">

        <!-- Leadership Type -->
        <div class="input-block-leadership">
            <label class="inline-attack-header">Leadership of Attack* <small>Optional -- in testing</small></label>

            <div class="inline-group">
                <?php
                $leadershipOptions = [
                    'hero' => 'Hero',
                    'capt' => 'Capt',
                    '3capt' => '3 Capt',
                    'all'  => 'Hero & 3 Capt'
                ];

                $selectedLeadership = $inputs['leadership'] ?? '';
                ?>

                <?php foreach ($leadershipOptions as $key => $label): ?>
                    <label>
                        <input type="radio"
                            name="leadership"
                            value="<?= $key ?>"
                            <?= $selectedLeadership === $key ? 'checked' : '' ?>>
                        <?= $label ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="input-block-capacity">
        <!-- Command Capacity -->        
        <label class="inline-attack-header">Command Capacity* <small>Optional -- in testing</small></label>
            <div class="inline-group-capacity1">
                <div>
                    <label>Leadership</label>
                    <input type="number"
                        name="leadership"
                        value="<?= $inputs['leadership'] ?? '' ?>"
                        class="input-small">
                        <span class="icon-slot"></span>
                </div>

                <div>
                    <label>Authority</label>
                    <input type="number"
                        name="authority"
                        value="<?= $inputs['authority'] ?? '' ?>"
                        class="input-small">
                        <span class="icon-slot"></span>
                </div>

                <div>
                    <label>Dominance</label>
                    <input type="number"
                        name="dominance"
                        value="<?= $inputs['dominance'] ?? '' ?>"
                        class="input-small">
                        <span class="icon-slot"></span>
                </div>

            </div>
            <div class="inline-group-capacity2">
                <div>
                    <label>Strength Bonus</label>
                    <input type="number"
                        name="bonusStr"
                        value="<?= $inputs['bonusStr'] ?? '' ?>"
                        class="input-small">
                        <span class="icon-slot"></span>
                </div>

                                <div>
                    <label>Health Bonus</label>
                    <input type="number"
                        name="bonusHlh"
                        value="<?= $inputs['bonusHlh'] ?? '' ?>"
                        class="input-small">
                        <span class="icon-slot"></span>
                </div>
            </div>
        </div>
        <!-- Troop Selection -->
        <div class="input-block-troops">
            <label class="inline-attack-header">Available Troops</label>
            <div class="troop-global-controls">

                <!-- Select All -->
                <label class="global-toggle">
                    <input type="checkbox" id="selectAllTroops">
                    Select All 
                </label>

                <!-- Global Level -->
                <div class="global-level">

                    <?php for ($i=5;$i<=9;$i++): ?>
                        <label>
                            <input type="radio" class="global-level-radio" name="globalLevel" value="<?= $i ?>">
                            <?= $i ?>

                        </label>
                    <?php endfor; ?>
                </div>

            </div>
            <div class="troop-grid">
                <?php
                $troops = [
                    'mtd' => 'Mounted',
                    'rng' => 'Archers',
                    'mel' => 'Melee',
                    'fly' => 'Flying',
                    'bst' => 'Creature'
                ];
                ?>
                <?php foreach ($troops as $key => $label): ?>
                    <?php
                    $enabled = !empty($inputs['troops'][$key]['enabled']);
                    $level   = $inputs['troops'][$key]['level'] ?? null;
                    ?>

                    <div class="troop-card">

                        <div class="troop-title"><?= $label ?></div>

                        <!-- Enable -->
                        <label class="troop-enable">
                            <input type="checkbox"
                                class="troop-checkbox"
                                data-troop-type="<?= $key ?>"
                                name="troops[<?= $key ?>][enabled]">
                                Use
                        </label>

                        <div class="troop-levels <?= !$enabled ? 'disabled' : '' ?>">
                            <?php for ($i = 5; $i <= 9; $i++): ?>
                                <label>
                                    <input type="radio"
                                        class="troop-level-radio"
                                        data-troop-type="<?= $key ?>"
                                        name="troops[<?= $key ?>][level]"
                                        value="<?= $i ?>"> <!-- MUST have value -->
                                    <?= $i ?>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

    </div>
</div>
        <!-- Card 2: Get Monster Info -->
        <div class="bc-layer-card">
            <div class="bc-img" style="height:40px;">
                <img src="/images/cards/war_table.jpg"
                    style="width:100%; height:100%; object-fit:cover; opacity:.4;">
                <div class="bc-img-overlay">
                    <div class="bc-img-title">Monster Squad Selections</div>
                </div>
            </div>
            <div class="bc-content">
                <!-- Difficulty -->
                <div class="layer-rarity-select">
                    <div class="input-block-squad">
                        <div class="inline-attack-header">Monster Squad to Attack</div>
                        <?php $difficulty = $inputs['difficulty'] ?? ''; ?>
                        <div class="difficulty-group">

                            <label>
                                <input type="radio" name="difficulty" value="common"
                                    <?= $difficulty === 'common' ? 'checked' : '' ?>>
                                Common
                            </label>

                            <label>
                                <input type="radio" name="difficulty" value="rare"
                                    <?= $difficulty === 'rare' ? 'checked' : '' ?>>
                                Rare
                            </label>
                            <label>
                                <input type="radio" name="difficulty" value="epic"
                                    <?= $difficulty === 'epic' ? 'checked' : '' ?>>
                                Epic
                            </label>
                        </div>

                        <!-- Squad -->
                        <div class="layer-squad-select">
                            <label><strong>Choose Squad:</strong></label>
                            <select name="squadID" id="squadSelect" >
                                <option value="">-- Choose Squad --</option>
                                <?php foreach ($squads as $squad): ?>
                                    <option value="<?= $squad['squadID'] ?>"
                                        <?= ($inputs['selectedSquad'] == $squad['squadID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($squad['name']) ?> (Lvl <?= $squad['level'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Generate -->
                <div class="layer-generate-btn">
                    <button 
                        type="button" 
                        id="generatePlanBtn"
                        class="btn btn-primary">
                        ⚔ Generate Attack Plan
                    </button>
                </div>
            </div>
        </div>
    </form>
        
        <!-- Card 3: Layers -->
        <div class="bc-layer-card">

            <div class="bc-img" style="height:40px;">
                <img src="/images/cards/war_table.jpg"
                     style="width:100%; height:100%; object-fit:cover; opacity:.4;">
                <div class="bc-img-overlay">
                    <div class="bc-img-title">Troops and Units by Layer</div>
                </div>
            </div>

            <div class="bc-content">
                <!-- Layer Count -->
                <div class="layer-control">
                    <label><strong>Layers:</strong></label>
                    <select name="layerCount" id="layerCount">
                        <?php for ($i=1;$i<=4;$i++): ?>
                            <option value="<?= $i ?>" <?= ($layerCount == $i ? 'selected' : '') ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Layers -->
                <div class="layer-section">

                <?php for ($layer = 1; $layer <= $layerCount; $layer++): ?>
                    <div class="layer-block" data-layer="<?= $layer ?>">
                        <div class="layer-header-round">
                            <strong>Round <?= $layer ?></strong>
                        </div>
                    <div class="layer-section">
                    <?php for ($layer = 1; $layer <= $layerCount; $layer++): ?>
                        <?php
                        // Map monster to layer
                        $monster = $monsters[$layer - 1] ?? null;
                        $selectedUnit1  = $inputs['layers'][$layer]['unit1'] ?? '';
                        $selectedLevel1 = $inputs['layers'][$layer]['level1'] ?? null;
                        $selectedUnit2  = $inputs['layers'][$layer]['unit2'] ?? '';
                        $selectedLevel2 = $inputs['layers'][$layer]['level2'] ?? null;
                        ?>
                        <div class="layer-block">

                            <!-- Layer Header -->
                            <div class="layer-header-layer">
                                <strong>Layer <?= $layer ?></strong>
                            </div>

                            <!-- Monster vs Fighter Split -->
                            <div class="layer-row">

                                <!-- LEFT: MONSTER -->
                                <div class="layer-monster">

                                    <?php if ($monster): ?>
                                        <div class="monster-name">
                                            <strong><?= htmlspecialchars($monster['name']) ?></strong>  ( <?= htmlspecialchars($monster['type']) ?>  )
                                        </div>

                                        <div class="monster-meta">
                                            Qty: <?= (int)$monster['quantity'] ?>
                                        </div>

                                        <div class="monster-meta">
                                            Str: <?= (int)$monster['strength'] ?>
                                        </div>

                                        <div class="monster-meta">
                                            ttl Str: <?= (int)$monster['total_strength'] ?>
                                        </div>

                                        <div class="monster-meta">
                                            ttl Hlh: <?= (int)$monster['total_health'] ?>
                                        </div>

                                                                                
                                        <div class="monster-meta">
                                            Id: <?= (int)$monster['monsterID'] ?>
                                        </div>


                                    <?php else: ?>
                                        <div class="monster-meta">No monster</div>
                                    <?php endif; ?>

                                </div>

                                    <div class="unit-round">

                                        <div class="unit-round-label"><strong>Attack 1</strong></div>
                                        <?php
                                        echo '<pre>';
                                        print_r($fighterOptions);
                                        echo '</pre>';
                                        ?>
                                        <select name="layers[<?= $layer ?>][unit1]" class="unit-select">
                                            <option value="">-- Select Unit --</option>
                                            <?php foreach ($fighterOptions as $f): ?>
                                                <option value="<?= $f['id'] ?>">
                                                    <?= htmlspecialchars($f['name']) ?> (<?= strtoupper($f['unit']) ?> L<?= $f['level'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>

                                    <!-- Round 2 -->
                                    <div class="unit-round">
                                        <div class="unit-round-label"><strong>Attack 2</strong></div>
                                        <select name="layers[<?= $layer ?>][unit2]"
                                                class="unit-select round2"
                                                disabled>
                                            <option value="">-- Select Unit --</option>
                                            <?php foreach ($units as $key => $label): ?>
                                                <option value="<?= $key ?>"
                                                    <?= ($selectedUnit2 === $key ? 'selected' : '') ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                        </div>
                    </div>
                <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Final Action -->
        <div class="bc-layer-card">
            <div class="bc-content">
                <button type="submit" name="buildPlan" value="1" class="btn btn-primary">
                    ⚔ Build Attack Plan 2
                </button>
                <button id="clear-selection">Clear Selection</button>
            </div>
        </div>



<!--
        <div class="bc-layer-card"> 
            <a href="#" class="bc-layer-card">
                
                <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/../images/cards/gear_info.png" alt="The Realm">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                    <h2>Finery & Armory</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <p>
                    Learn what to wear in the Realm? Learn about<br />
                    gear for your Hero & Captains
                    </p>
                </div>
                </div>
             </a>   
        </div>
            

            <div class="bc-layer-card"> 
                <a href="<?= BASE_URL ?>/public/ledger.php" class="bc-layer-card">
                
                <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/../images/cards/ledger1.png" alt="The Ledger">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                    <h2>The Ledger</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <p>
                    Help out BattleCouncil and get rewards<br />
                    This is the reward points info page
                    </p>
                </div>
                </div>
                </a>
        </div>
-->

    </div> <!-- mh-grid -->

</div> <!-- container -->

<script>
window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php
// ==============================
// FOOTER
// ==============================
require_once __DIR__ . '/../includes/footer_layer.php';