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
    $plan = [];

    if (!empty($_GET['attackPlan'])) {
        $plan = json_decode($_GET['attackPlan'], true);

        echo '<pre>';
        print_r($plan);
        echo '</pre>';
    }

/*
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

        <!-- Header -->
        <div class="layer-header-round">
            <strong>Round <?= $layer ?></strong>
        </div>

        <div class="layer-row">

            <!-- LEFT: MONSTER (placeholder, JS will overwrite) -->
            <div class="layer-monster">
                <div class="monster-meta">Waiting for plan...</div>
            </div>

            <!-- RIGHT: FIGHTERS -->
            <div class="layer-fighters">

                <!-- Attack 1 -->
                <div class="unit-round attack1">
                    <div class="unit-round-label"><strong>Attack 1</strong></div>
                    <div class="unit-placeholder">--</div>
                </div>

                <!-- Attack 2 -->
                <div class="unit-round attack2">
                    <div class="unit-round-label"><strong>Attack 2</strong></div>
                    <div class="unit-placeholder">--</div>
                </div>

            </div>

        </div>
    </div>
<?php endfor; ?>

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

    </div> <!-- mh-grid -->

</div> <!-- container -->

<script>
    window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const bonusMatrix = <?= json_encode($bonusMatrix ?? []) ?>;
</script>

<script src="<?= BASE_URL ?>/assets/js/LayerEngine.js"></script>
<script src="<?= BASE_URL ?>/assets/js/layer.js"></script>

<?php
// ==============================
// FOOTER
// ==============================

require_once __DIR__ . '/../includes/footer_layer.php';