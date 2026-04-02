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

    $inputs      = $data['inputs'];
    $squads      = $data['squads'];
    $layerCount  = $data['layerCount'];   
    $config      = $data['config'];
    $bonusMatrix = $data['bonusMatrix'];
    $monsters       = $data['monsters'] ?? [];

/*
echo '<pre>';
print_r($monsters);
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

<!-- MAIN CONTENT -->
<div class="container">

    <div class="mh-leader">
        <h1>Monster Hunting Calculator</h1>
    </div>

    <div class="layer-grid">

    <!-- SINGLE FORM (wrap everything) -->
    <form method="GET" id="layerForm">

        <div class="bc-layer-card">
            <div class="bc-img" style="height:40px;">
                <img src="/images/cards/war_table.jpg"
                    style="width:100%; height:100%; object-fit:cover; opacity:.4;">
                <div class="bc-img-overlay">
                    <div class="bc-img-title">Monster & Attack Squad Selections</div>
                </div>
            </div>

            <div class="bc-content">

                <!-- Difficulty -->
                <div class="layer-rarity-select">
                    <label><strong>Monster Squad to Attack:</strong></label>

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

                <!-- Generate -->
                <div class="layer-generate-btn">
                    <button type="submit"
                            name="buildLayerPlan"
                            value="1"
                            id="generatePlanBtn"
                            class="btn btn-primary"
                            >
                        ⚔ Generate Attack Plan
                    </button>
                </div>

            </div>
        </div>

    </form>
        
        <!-- Card 2: Layers -->
        <div class="bc-layer-card">

            <div class="bc-img" style="height:40px;">
                <img src="/images/cards/war_table.jpg"
                     style="width:100%; height:100%; object-fit:cover; opacity:.4;">
                <div class="bc-img-overlay">
                    <div class="bc-img-title">Troops and Units by Layer</div>
                </div>
            </div>

            <div class="bc-content">

                <?php
                $units = [
                    ''    => '-- Select Unit --',
                    'mtd' => 'Mounted',
                    'rng' => 'Archers',
                    'mel' => 'Melee',
                    'fly' => 'Flying',
                ];
                ?>

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

                        <div class="layer-header">
                            <strong>Layer <?= $layer ?></strong>
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
        <div class="layer-header">
            <strong>Layer <?= $layer ?></strong>
        </div>

        <!-- Monster vs Fighter Split -->
        <div class="layer-row">

            <!-- LEFT: MONSTER -->
            <div class="layer-monster">

                <?php if ($monster): ?>
                    <div class="monster-name">
                        <strong><?= htmlspecialchars($monster['name']) ?></strong>
                    </div>

                    <div class="monster-meta">
                        Type: <?= htmlspecialchars($monster['type']) ?>
                    </div>

                    <div class="monster-meta">
                        Qty: <?= (int)$monster['quantity'] ?>
                    </div>
                <?php else: ?>
                    <div class="monster-meta">No monster</div>
                <?php endif; ?>

            </div>

            <!-- RIGHT: FIGHTERS -->
            <div class="layer-fighters">

                <!-- Round 1 -->
                <div class="unit-round">

                    <div class="unit-round-label"><strong>Round 1</strong></div>

                    <select name="layers[<?= $layer ?>][unit1]" class="unit-select">
                        <option value="">-- Select Unit --</option>
                        <?php foreach ($units as $key => $label): ?>
                            <option value="<?= $key ?>"
                                <?= ($selectedUnit1 === $key ? 'selected' : '') ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="unit-levels">
                        <?php for ($i=6;$i<=9;$i++): ?>
                            <label>
                                <input type="radio"
                                    name="layers[<?= $layer ?>][level1]"
                                    value="<?= $i ?>"
                                    <?= ($selectedLevel1 == $i ? 'checked' : '') ?>>
                                <?= $i ?>
                            </label>
                        <?php endfor; ?>
                    </div>

                </div>

                <!-- Round 2 -->
                <div class="unit-round">

                    <div class="unit-round-label"><strong>Round 2</strong></div>

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

                    <div class="unit-levels">
                        <?php for ($i=6;$i<=9;$i++): ?>
                            <label>
                                <input type="radio"
                                    name="layers[<?= $layer ?>][level2]"
                                    value="<?= $i ?>"
                                    <?= ($selectedLevel2 == $i ? 'checked' : '') ?>
                                    disabled>
                                <?= $i ?>
                            </label>
                        <?php endfor; ?>
                    </div>

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
                    ⚔ Build Attack Plan
                </button>
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