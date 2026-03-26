<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
    require_once __DIR__ . '/core/bootstrap.php';      // sessions, environment
    require_once __DIR__ . '/config/config.php';      // BASE_URL, DB
    require_once __DIR__ . '/helpers/functions.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

// ==============================
// SERVICES & CONTROLLERS
// ==============================
    require_once __DIR__ . '/services/AttackEngine.php';
    require_once __DIR__ . '/services/MonsterHuntService.php';
    require_once __DIR__ . '/controllers/MonsterHuntController.php';

// ==============================
// CONTROLLER DATA
// ==============================
    $data = monsterHuntController($pdo);
    $inputs = $data['inputs'];
    $view   = $data['data'];

    $squads      = $view['squads'] ?? [];
    $creatures   = $view['creatures'] ?? [];
    $monsters    = $view['monsters'] ?? [];
    $attackGroups = $view['attackGroups'] ?? [];
    $enemyType   = $view['enemyType'] ?? null;
    //echo '<pre>'; print_r($data); exit;    
// ==============================
// PAGE SETTINGS
// ==============================
    $pageClass = 'page-index';

// ==============================
// HEADER
// ==============================
    require_once __DIR__ . '/includes/header.php';

/* page protected
requireLogin(); 
  if(hasRole('veteran')){
      // show veteran+ content
  }
*/

  ?>
    <!-- MAIN CONTENT -->
    <div class="container">
        <div class="mh-leader">
            <h1>Monster Hunting Calculator</h1>
        </div>
        <div class="mh-grid">
            <!-- Card 1: Troop Attack Selection -->
            <div class="bc-card">
                <div class="bc-img" style="height:40px;">
                    <img src="/images/cards/war_table.jpg" alt="Troop Attack" 
                        style="width:100%; height:100%; object-fit:cover;opacity:.4;">
                    <div class="bc-img-overlay">
                        <div class="bc-img-title">Monster & Attack Squad Selections</div>
                    </div>
                </div>

                <div class="bc-content">
                    <form method="GET">
                        <!-- Squad Dropdown -->
                        <div class="squad-select" style="margin-bottom:10px;">
                            <label><strong>Choose Squad</strong></label>
    <!-- Squad Dropdown -->
    <select name="squadID">
        <option value="">-- Choose Squad --</option>
        <?php foreach ($squads as $squad): ?>
            <option value="<?= $squad['squadID'] ?>"
                <?= ($inputs['selectedSquad'] == $squad['squadID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($squad['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
                        </div>

                        <!-- Player Level -->
                        <div class="planner-section" style="margin-bottom:10px;">
                            <label><strong>Player Level</strong></label>
                            <select name="playerLevel" class="selectLevel">
                                <?php for($i=1;$i<=10;$i++): ?>
                                    <option value="<?= $i ?>" <?= ($inputs['playerLevel']==$i)?'selected':'' ?>>
                                        Level <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Unit Types -->
                        <div class="bonus-section" style="margin-bottom:12px;">
                            <label><strong>Troops</strong></label>
                            <label>
                                <input type="checkbox" name="useFighters" value="1" 
                                    <?= !empty($inputs['useFighters']) ? 'checked' : '' ?> disabled>
                                Fighters
                            </label>
                            <label>
                                <input type="checkbox" name="useCreatures" value="1" 
                                    <?= !empty($inputs['useCreatures']) ? 'checked' : '' ?> checked>
                                Creatures
                            </label>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit" name="buildPlan" value="1" class="btn-primary" id="buildPlanBtn">
                                Build Attack Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card 3: Monster / Creature Attack Display -->
            <div class="bc-card">
                <div class="bc-img" style="height:40px;">
                    <img src="/images/cards/war_table.jpg" alt="Troop Attack" 
                        style="width:100%; height:100%; object-fit:cover;opacity:.4;">
                    <div class="bc-img-overlay">
                        <div class="bc-img-title">Monster & Attack Squad Formations</div>
                    </div>
                </div>

                <div class="bc-content">
                    <?php if (!empty($monsters)): ?>
                        <h4>Squad Monsters</h4>
                        <div class="monster-grid">
                            <?php foreach ($monsters as $monster): 
                                $mel = $monster['bonus_mel'] ?? 0;
                                $mtd = $monster['bonus_mtd'] ?? 0;
                                $rng = $monster['bonus_rng'] ?? 0;
                                $fly = $monster['bonus_fly'] ?? 0;
                                $oth = $monster['bonus_oth'] ?? 0;
                            ?>
                                <details class="monster-row">
                                    <summary class="monster-summary">
                                        <span class="col col-name"><?= htmlspecialchars($monster['name']) ?> (<?= htmlspecialchars($monster['type']) ?>)</span>
                                        <span class="col col-qty">Qty: <?= shortNum($monster['quantity'] ?? 1) ?></span>
                                        <span class="col col-hlh">Hth: <?= shortNum($monster['health'] ?? 0) ?></span>
                                        <span class="col col-str">Str: <?= shortNum($monster['strength'] ?? 0) ?></span>
                                    </summary>

                                    <div class="monster-calc">
                                        <span class="bonus-col">
                                            <span class="dot <?= bonusDot($mel) ?>" title="Mel <?= $mel ?>%">Mel</span>
                                            <span class="dot <?= bonusDot($mtd) ?>" title="Mtd <?= $mtd ?>%">Mtd</span>
                                            <span class="dot <?= bonusDot($rng) ?>" title="Rng <?= $rng ?>%">Rng</span>
                                            <span class="dot <?= bonusDot($fly) ?>" title="Fly <?= $fly ?>%">Fly</span>
                                            <span class="dot <?= bonusDot($oth) ?>" title="Other <?= $oth ?>%">Oth</span>
                                        </span>
                                    </div>
                                </details>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($attackGroups)): ?>
                            <h4 style="margin-top:15px;">Creature Attack Options: <?= count($attackGroups) ?></h4>
                            <div id="creatureDisplay"></div>

                            <div class="group-switch" style="margin-top:10px;">
                                <button id="prev">&lt; Prev</button>
                                <button id="next">Next &gt;</button>
                            </div>

                            <script>
                                const attackGroups = <?= json_encode($attackGroups) ?>;
                                let currentIndex = 0;

                                function renderCreature(i) {
                                    const creature = attackGroups[i][0];
                                    if (!creature) return;
                                    
                                    const bonusParts = Object.entries(creature.bonuses || {})
                                        .map(([type, val]) => `${type.toLowerCase()} +${val}%`)
                                        .join(' | ');

                                    const html = `
                                        <div class="creature-text-block" style="display:flex; gap:15px; align-items:flex-start; padding-left:8px;">
                                            <div class="creature-image-container">
                                                <img src="${creature.imgpath}" class="creature-img" style="max-width:120px;">
                                            </div>
                                            <div style="flex-grow:1;">
                                                <div class="formation-text-top">
                                                    <h3>Formation #${creature.formation_no} | ${creature.name} (${creature.type})</h3>
                                                </div>
                                                <div class="formation-text-middle">
                                                    Bonus Mods: <div class="focus-pill">${bonusParts}</div>
                                                </div>
                                                <div class="formation-text-bottom">
                                                    Base Str: ${creature.strength.toLocaleString()} | Base Hth: ${creature.health.toLocaleString()}
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                    document.getElementById('creatureDisplay').innerHTML = html;
                                }

                                document.getElementById('next').onclick = () => {
                                    currentIndex = (currentIndex + 1) % attackGroups.length;
                                    renderCreature(currentIndex);
                                };

                                document.getElementById('prev').onclick = () => {
                                    currentIndex = (currentIndex - 1 + attackGroups.length) % attackGroups.length;
                                    renderCreature(currentIndex);
                                };

                                renderCreature(currentIndex);
                            </script>
                        <?php endif; ?>

                    <?php else: ?>
                        <p>No monsters assigned to this squad.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
    window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
    </script>

<?php
// ==============================
// FOOTER
// ==============================
require_once __DIR__ . '/includes/footer.php';