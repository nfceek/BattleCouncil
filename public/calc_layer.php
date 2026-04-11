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
            <!-- Modifiers -->        
            <label class="inline-attack-header">
                Bonus Modifiers
            </label>

            <div class="inline-group-capacity2">

                <!-- Strength Bonus -->
                <?php $bonusStr = $inputs['bonusStr'] ?? 100; ?>

                <div>
                    <label>Strength Bonus</label>

                    <?php $bonusStr = $inputs['bonusStr'] ?? 100; ?>

                    <select name="bonusStr" class="input-small">
                        <?php for ($i = 100; $i <= 1200; $i += 100): ?>
                            <option value="<?= $i ?>" <?= ((int)$bonusStr === $i) ? 'selected' : '' ?>>
                                <?= $i ?>%
                            </option>
                        <?php endfor; ?>
                    </select>

                    <span class="icon-slot"></span>
                </div>

                <!-- Health Bonus -->
                <div>
                    <label>Health Bonus</label>

                    <?php $bonusHlh = $inputs['bonusHlh'] ?? 100; ?>

                    <select name="bonusHlh" class="input-small">
                        <option value="100" <?= $bonusHlh == 100 ? 'selected' : '' ?>>100%</option>

                        <?php for ($i = 200; $i <= 1200; $i += 100): ?>
                            <option value="<?= $i ?>" <?= $bonusHlh == $i ? 'selected' : '' ?>>
                                <?= $i ?>%
                            </option>
                        <?php endfor; ?>
                    </select>

                    <span class="icon-slot"></span>
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
                                <input type="radio" name="difficulty" value="Common"
                                    <?= $difficulty === 'Common' ? 'checked' : '' ?>>
                                Common
                            </label>

                            <label>
                                <input type="radio" name="difficulty" value="Rare"
                                    <?= $difficulty === 'Rare' ? 'checked' : '' ?>>
                                Rare
                            </label>
                            <label>
                                <input type="radio" name="difficulty" value="Epic"
                                    <?= $difficulty === 'Epic' ? 'checked' : '' ?>>
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
                                        <?= htmlspecialchars($difficulty) ?> <?= htmlspecialchars($squad['name']) ?> (Lvl <?= $squad['level'] ?>)
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

                </div>

        <!-- Layers -->
        <div class="layer-section">

        <?php for ($layer = 1; $layer <= $layerCount; $layer++): ?>
            <div class="layer-block" data-layer="<?= $layer ?>">

                <!-- Header -->
                <div class="layer-header-round">            
                </div>

                <div class="layer-row">

                    <!-- MONSTER (placeholder, JS will overwrite) -->
                    <div class="layer-monster">
                        <div class="monster-meta">Waiting for plan...</div>
                    </div>

                </div>
            </div>
        <?php endfor; ?>

        </div>

        <!-- Final Action -->
        <div class="bc-layer-card">
            <div class="bc-content">

                <button id="clear-selection" class="btn btn-primary">Clear Selection</button>
            </div>
        </div>

         <!-- Command Capacity --> 
        <div class="input-block-capacity">
            <label class="inline-attack-header">Command Capacity</label>
            <div class="inline-group-capacity1">
                <div class="capacity-item">
                    <label class="capacity-label">
                        <img src="/images/icons/leadership.png" class="capacity-icon" style="width:15px; height:15px;">
                        Leadership
                    </label>
                    <div class="capacity-value leadership-value">0
                    </div>
                </div>

                <div class="capacity-item">
                    <label class="capacity-label">
                        <img src="/images/icons/dominance.png" class="capacity-icon" style="width:15px; height:15px;">
                        Dominance
                    </label>
                    <div class="capacity-value dominance-value">0
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- mh-grid -->

</div> <!-- container -->

<script>
    window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const bonusMatrix = <?= json_encode($bonusMatrix ?? []) ?>;
</script>

<script src="<?= BASE_URL ?>/assets/js/LayerEngine.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/layer.js?v=<?= time() ?>"></script>

<?php
// ==============================
// FOOTER
// ==============================

require_once __DIR__ . '/../includes/footer.php';