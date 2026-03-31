<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/../config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/../helpers/functions.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

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
    <!-- MAIN CONTENT -->
    <div class="container">
        <div class="mh-leader">
            <h1>Monster Hunting Calculator</h1>
        </div>
        <div class="mh-grid">

        <!-- Card 1: Rarity and Squad Selection -->
        <div class="bc-card">
            <div class="bc-img" style="height:40px;">
                <img src="/images/cards/war_table.jpg" alt="Troop Attack" 
                    style="width:100%; height:100%; object-fit:cover;opacity:.4;">
                <div class="bc-img-overlay">
                    <div class="bc-img-title">Monster & Attack Squad Selections</div>
                </div>
            </div>
            <div class="bc-content">
                <div class="rarity-select">
                    <label><strong>Monster Squad to Attack:</strong></label>
                    <div class="difficulty-group">
                        <?php
                        $difficulty = $inputs['difficulty'] ?? 'rare';
                        ?>
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
                <form method="GET">
                    <!-- Squad Dropdown -->
                    <div class="squad-select" style="margin-bottom:10px;">
                        <label><strong>Choose Squad: </strong></label>
                        <!-- Squad Dropdown -->
                        <select name="squadID">
                            <option value="">-- Choose Squad --</option>
                            <?php foreach ($squads as $squad): ?>
                                <option value="<?= $squad['squadID'] ?>"
                                    <?= ($inputs['selectedSquad'] == $squad['squadID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($squad['name']) . " Lvl (" . $squad['level'] . ")" ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>                        
                    <!-- Submit -->
                    <div>
                        <button type="submit" 
                                name="buildPlan" 
                                value="1" 
                                class="btn btn-primary btn-build"
                                id="buildPlanBtn">
                            ⚔ Select Units for Attack
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 1: Rarity and Squad Selection -->
        <div class="bc-card">
            <div class="bc-img" style="height:40px;">
                <img src="/images/cards/war_table.jpg"
                    style="width:100%; height:100%; object-fit:cover; opacity:.4;">
                <div class="bc-img-overlay">
                    <div class="bc-img-title">Troops and Units by Layer</div>
                </div>
            </div>
            <?php
            $units = [
                ''    => '-- Select Unit --',
                'mtd' => 'Mounted',
                'rng' => 'Archers',
                'mel' => 'Melee',
                'fly' => 'Flying',

                // future expansion
                // 'griffin' => 'Griffin',
                // 'magog'   => 'Magog',
            ];
            ?>

            <div class="bc-content">

            <form method="GET">

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

                        <div class="unit-row">

                            <?php for ($slot = 1; $slot <= 4; $slot++): ?>

                            <?php
                            $selectedUnit = $inputs['layers'][$layer]["unit{$slot}"] ?? '';
                            $selectedLevel = $inputs['layers'][$layer]["level{$slot}"] ?? null;
                            ?>

                            <div class="unit-group" data-layer="<?= $layer ?>">

                                <!-- Unit Select -->
                                <select name="layers[<?= $layer ?>][unit<?= $slot ?>]" class="unit-select">
                                    <?php foreach ($units as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= ($selectedUnit === $key ? 'selected' : '') ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <!-- Level Radios -->
                                <div class="unit-levels">
                                    <?php for ($i=6;$i<=9;$i++): ?>
                                        <label>
                                            <input type="radio"
                                                name="layers[<?= $layer ?>][level<?= $slot ?>]"
                                                value="<?= $i ?>"
                                                <?= ($selectedLevel == $i ? 'checked' : '') ?>>
                                            <?= $i ?>
                                        </label>
                                    <?php endfor; ?>
                                </div>

                            </div>

                            <?php endfor; ?>

                        </div>
                    </div>

                <?php endfor; ?>

                </div>

                <button type="submit" class="btn btn-primary">
                    ⚔ Build Attack Plan
                </button>

            </form>
            </div>
        </div>

<!--
        <div class="bc-card"> 
            <a href="#" class="bc-card">
                
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
            

            <div class="bc-card"> 
                <a href="<?= BASE_URL ?>/public/ledger.php" class="bc-card">
                
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

    </div>       
</div>

<script>
window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php
// ==============================
// FOOTER
// ==============================
require_once __DIR__ . '/../includes/footer_layer.php';