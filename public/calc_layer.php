<?php
// ==============================
// BOOTSTRAP
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';

require_once __DIR__ . '/../services/PointsService.php';
require_once __DIR__ . '/../services/ClanServices.php';
require_once __DIR__ . '/../controllers/LayerController.php';

// ==============================
// DATA
// ==============================
$data = layerController($pdo);

// ------------------------------
// SAFE INPUT NORMALIZATION
// ------------------------------
$inputs = $data['inputs'] ?? [];

// FORCE DEFAULTS (CRITICAL FOR JS ENGINE)
$inputs['bonusStr'] = (int)($inputs['bonusStr'] ?? 100);
$inputs['bonusHlh'] = (int)($inputs['bonusHlh'] ?? 100);

$inputs['difficulty'] = $inputs['difficulty'] ?? 'Rare';

// ------------------------------
$squads         = $data['squads'] ?? [];
$layerCount     = $data['layerCount'] ?? 3;
$config         = $data['config'] ?? [];
$bonusMatrix    = $data['bonusMatrix'] ?? [];
$monsters       = $data['monsters'] ?? [];
$fighterOptions = $data['fighterOptions'] ?? [];

// ==============================
// PAGE
// ==============================
$pageClass = 'Layering Calculator';

require_once __DIR__ . '/../includes/header.php';
?>

<script>
const BASE_URL = "<?= BASE_URL ?>";

// ✅ CRITICAL: ALWAYS SAFE DEFAULTS FOR JS ENGINE
window.__LAYER_INPUTS = {
    bonusStr: <?= (int)$inputs['bonusStr'] ?>,
    bonusHlh: <?= (int)$inputs['bonusHlh'] ?>,
    difficulty: "<?= htmlspecialchars($inputs['difficulty']) ?>"
};

window.__SQUADS = <?= json_encode($squads, JSON_UNESCAPED_UNICODE) ?>;
window.__BONUS_MATRIX = <?= json_encode($bonusMatrix, JSON_UNESCAPED_UNICODE) ?>;
</script>

<div class="container">

    <div class="mh-leader">
        <h1>Monster Hunting Calculator</h1>
    </div>

    <div class="layer-grid">

    <form method="GET" id="layerForm">

        <!-- ===================== -->
        <!-- CARD 1: TROOPS -->
        <!-- ===================== -->
        <div class="bc-layer-card">

            <div class="bc-content">

                <!-- Leadership -->
                <div class="input-block-leadership">
                    <label class="inline-attack-header">Leadership</label>

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

                <!-- ===================== -->
                <!-- BONUSES -->
                <!-- ===================== -->
                <div class="inline-group-capacity2">

                    <div>
                        <label>Strength Bonus</label>
                        <select name="bonusStr" class="input-small">
                            <?php for ($i = 100; $i <= 1200; $i += 100): ?>
                                <option value="<?= $i ?>" <?= ((int)$inputs['bonusStr'] === $i) ? 'selected' : '' ?>>
                                    <?= $i ?>%
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label>Health Bonus</label>
                        <select name="bonusHlh" class="input-small">
                            <?php for ($i = 100; $i <= 1200; $i += 100): ?>
                                <option value="<?= $i ?>" <?= ((int)$inputs['bonusHlh'] === $i) ? 'selected' : '' ?>>
                                    <?= $i ?>%
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

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
        <!-- ===================== -->
        <!-- CARD 2: MONSTERS -->
        <!-- ===================== -->
        <div class="bc-layer-card">

            <div class="bc-content">

                <?php $difficulty = strtolower($inputs['difficulty'] ?? 'rare'); ?>

                <div class="difficulty-group">

                    <?php foreach (['common','rare'] as $d): ?>     <!-- removed Epic from list for beta testing -->
                        <label class="difficulty-default">
                            <input
                                type="radio"
                                name="difficulty"
                                value="<?= ucfirst($d) ?>"
                                <?= $difficulty === $d ? 'checked' : '' ?>
                            >
                            <?= $d ?>
                        </label>
                    <?php endforeach; ?>

                </div>

                <div class="layer-squad-select">
                    <select name="squadID" id="squadSelect">
                        <option value="">-- Choose Squad --</option>
                        <?php foreach ($squads as $squad): ?>
                            <option value="<?= $squad['squadID'] ?>">
                                <?= htmlspecialchars($squad['name']) ?> (Lvl <?= $squad['level'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" id="generatePlanBtn" class="btn btn-primary">
                    ⚔ Generate Attack Plan
                </button>

            </div>
        </div>

        <!-- ===================== -->
        <!-- CARD 3: LAYERS -->
        <!-- ===================== -->
        <div class="bc-layer-card">

            <div class="layer-section">

                <?php for ($layer = 1; $layer <= $layerCount; $layer++): ?>
                    <div class="layer-block" data-layer="<?= $layer ?>">

                        <div class="layer-header-round"></div>

                        <div class="layer-row">
                            <div class="layer-monster">
                                <div class="monster-meta">Waiting for plan...</div>
                            </div>
                        </div>

                    </div>
                <?php endfor; ?>

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

    </form>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/LayerEngine.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/layer.js?v=<?= time() ?>"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>