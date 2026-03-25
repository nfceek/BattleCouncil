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
$data = monsterHuntController($pdo);   // returns array of $squads, $creatures, $attackGroups etc.
//echo '<pre>'; print_r($data); exit;
extract($data);

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
            <!-- Card 1 -->
            <div class="bc-card">
                <div class="bc-img" style="height:40px;">
                    <img src="/images/cards/war_table.jpg" alt="Squad 1" style="width:100%; height:100%; object-fit:cover;opacity:.4;">
                    <div class="bc-img-overlay">
                        <div class="bc-img-title">1) Monster Squad Selection</div>
                    </div>
                </div>
                <div class="bc-content">
                    <div class="card-info" style='opacity:.6; padding-top:12px;margin-top:8px;font-size:.9rem;'>
                        <label>
                            Find a Squad to Attack
                        </label>
                    </div>            
                    <!-- Rarity -->
                    <div class="rarity-group">
                        <label>
                            <input type="radio" name="rarity" value="Common"
                            <?= ($inputs['rarity'] === 'Common') ? 'checked' : '' ?>
                            onchange="this.form.submit()" checked>
                            Common
                        </label>

                        <label>
                            <input type="radio" name="rarity" value="Rare"
                            <?= ($inputs['rarity'] === 'Rare') ? 'checked' : '' ?>
                            onchange="this.form.submit()" disabled>
                            Rare
                        </label>
                    </div>
                    <!-- Squad Dropdown -->
                    <div class="squad-select">
                        <select name="squadID" onchange="this.form.submit()">
                            <option value="">-- Choose Squad --</option>

                            <?php foreach ($squads as $squad): ?>
                                <option value="<?= $squad['squadID'] ?>"
                                <?= ($selectedSquad == $squad['squadID']) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($squad['name']) ?>
                                    (L<?= $squad['level'] ?>)

                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bc-card">
                <div class="bc-img" style="position:relative; height:40px;">
                    <img src="/images/cards/war_table.jpg" 
                        alt="Troop Attack" 
                        style="width:100%; height:100%; object-fit:cover; filter:brightness(0.4);">
                    <div class="bc-img-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                        <div class="bc-img-title" style="color:#fff; font-weight:bold; text-align:center;">
                            2) Troop Attack Selection
                        </div>
                    </div>
                </div>

                <div class="bc-content">
                    <div class="card-info" style="opacity:.8; padding-top:12px; margin-top:8px; font-size:.9rem;">
                        <label>Select a unit type to build the plan</label>
                    </div>

                    <form method="GET">
                        <input type="hidden" name="squadID" value="<?= htmlspecialchars($inputs['selectedSquad'] ?? '') ?>">
                        <input type="hidden" name="rarity" value="<?= htmlspecialchars($inputs['rarity'] ?? 'Common') ?>">

                        <!-- Planner Section -->
                        <div class="planner-section" style="margin-top:10px;">
                            <label><strong>Player Level</strong></label>
                            <select name="playerLevel" class="selectLevel">
                                <?php for($i=1; $i<=10; $i++): ?>
                                    <option value="<?= $i ?>" <?= (($inputs['playerLevel'] ?? 6) == $i) ? 'selected' : '' ?>>
                                        Level <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="bonus-section" style="margin-top:12px;">
                            <label><strong>Troops</strong></label>
                            <label>
                                <input type="checkbox" name="useFighters" value="1" <?= !empty($inputs['useFighters']) ? 'checked' : '' ?> disabled>
                                Fighters
                            </label>
                            <label>
                                <input type="checkbox" name="useCreatures" value="1" <?= !empty($inputs['useCreatures']) ? 'checked' : '' ?> checked>
                                Creatures
                            </label>
                        </div>

                        <!-- Submit -->
                        <div style="margin-top:12px;">
                            <?php if (!empty($inputs['selectedSquad'])): ?>
                                <button type="submit" name="buildPlan" value="1" class="btn-primary" id="buildPlanBtn">
                                    Build Attack Plan
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-primary" disabled>
                                    Select a Squad First
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bc-card">
                <div class="bc-img" style="height:40px;">
                    <img src="/images/cards/war_table.jpg" alt="Squad 1" style="width:100%; height:100%; object-fit:cover;opacity:.4;">
                    <div class="bc-img-overlay">
                        <div class="bc-img-title">3) Monster Squad Summary</div>
                    </div>
                </div>
                <div class="bc-content">
                    <?php 
                    if ($monsters): ?>
                                                
                        <div class="monster-grid">

                            <?php foreach ($monsters as $monster): ?>

                                <?php
                                    $mel = $monster['bonus_mel'] ?? 0;
                                    $mtd = $monster['bonus_mtd'] ?? 0;
                                    $rng = $monster['bonus_rng'] ?? 0;
                                    $fly = $monster['bonus_fly'] ?? 0;
                                    $oth = $monster['bonus_oth'] ?? 0;

                                    $health = $monster['total_health'] ?? 0;
                                    $monsterHealthList[] = $health;
                                    $monsterTotalHealth += $health;

                                    $strength = $monster['total_strength'] ?? 0;
                                    $monsterStrengthList[] = $strength;
                                    $monsterTotalStrength += $strength;
                                ?>

                                <details class="monster-row">
                                    <summary class="monster-summary">
                                        <span class="col col-name">
                                            <?= htmlspecialchars($monster['name']) ?> (<?= htmlspecialchars($monster['type']) ?>)
                                        </span>

                                        <span class="col col-qty">
                                            Qty: <?= shortNum($monster['quantity']) ?>
                                        </span>

                                        <span class="col col-hlh">
                                            Hth: <?= shortNum($monster['total_health']) ?>
                                        </span>

                                        <span class="col col-str">
                                            Str: <?= shortNum($monster['total_strength']) ?>
                                        </span>
                                    </summary>

                                    <div class="monster-calc">
                                        <span class="bonus-col">
                                            <span class="dot <?=bonusDot($mel)?>"></span> Mel
                                            <span class="dot <?=bonusDot($mtd)?>"></span> Mtd
                                            <span class="dot <?=bonusDot($rng)?>"></span> Rng
                                            <span class="dot <?=bonusDot($fly)?>"></span> Fly
                                            <span class="dot <?=bonusDot($oth)?>"></span> Oth
                                        </span>
                                    </div>
                                </details>

                            <?php endforeach; ?>

                        </div>
                    <?php else: ?>
                        <p>No monsters assigned.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bc-card">
                <div class="bc-img" style="height:40px;">
                    <img src="/images/cards/war_table.jpg" alt="Squad 1" style="width:100%; height:100%; object-fit:cover;opacity:.4;">
                    <div class="bc-img-overlay">
                        <div class="bc-img-title">4) Attack Formation</div>
                    </div>
                </div>
                <div class="bc-content">
                <?php if(!empty($attackGroups)): ?>
                    <div id="creatureDisplay">
                        <!-- This will be filled by JS -->
                    </div>

                    <div class="group-switch">
                        <button id="prev">&lt; Prev</button>
                        <button id="next">Next &gt;</button>
                    </div>

                    <p>Creature Attack Options: <?= count($attackGroups) ?></p>

                      <script>
                        const attackGroups = <?= json_encode($attackGroups) ?>;
                        const monsterMaxHealth = <?= (int)$monsterMaxHealth ?>;
                        const monsterMaxStrength = <?= (int)$monsterMaxStrength ?>;
                        let currentIndex = 0;

                        /* ------------------ CALCS ------------------ */
                        function calcUnitsNeeded(creatureStrength, percent = 0) {
                            const boosted = creatureStrength * (1 + percent / 100);
                            let units = Math.ceil(monsterMaxHealth / boosted);  

                            if (units < 1) return 1;

                            if (units > 500) return '<span style="color:red;">✖</span>';

                              return units.toLocaleString();
                        }

                        function calcLosses(creatureHealth, percent = 0, units) {
                            const boostedHP = creatureHealth * (1 + percent / 100) * units;
                            const diff = monsterMaxStrength - boostedHP;

                            /* ❌ catch bad inputs → show red X
                            if (units <= 0) {
                                return '<span style="color:red;">✖</span>';
                            }*/

                            if (diff >= boostedHP) {
                                if (monsterMaxStrength >= boostedHP) {
                                    // how many creatures die
                                    const spend = Math.ceil(monsterMaxStrength / creatureHealth);
                                      return `<span style="color:red;">${spend}</span>`;
                                }
                            }

                            if (diff <= 0) {
                                  // Creature survives completely → GREEN 0
                                  return '<span style="color:green;">NONE</span>';
                            }

                            // Partial losses: how many creatures "die" to cover the diff
                            const loss = Math.ceil(diff / creatureHealth);

                              // Never exceed the units sent
                              return Math.min(loss, units).toLocaleString();
                        }

                        /* ------------------ RENDER ------------------ */
                        function renderCreature(i) {
                            const creature = attackGroups[i][0];
                            if (!creature) return;

                            const bonusParts = Object.entries(creature.bonuses || {}).map(
                                ([type, val]) => `${type.toLowerCase()} +${Number(val).toLocaleString()}%`
                            ).join(' &nbsp;|&nbsp; ');

                            const levels = [0,200,400,600,800,1000,1200];

                            let strRow = '';
                            let hlhRow = '';

                            levels.forEach(p => {
                                const units = calcUnitsNeeded(creature.strength, p);
                                const losses = calcLosses(creature.health, p, units);

                                strRow += `<td>${units}</td>`;
                                hlhRow += `<td>${losses}</td>`;
                            });

                            const html = `
                                <div class="creature-text-block" style="display:flex; gap:15px; align-items:flex-start; padding-left:8px;">
                                    
                                    <div class="creature-image-container">
                                        <img src="${creature.imgpath}" class="creature-img" style="max-width:120px;">
                                    </div>

                                    <div style="flex-grow:1;">
                                        <div class="formation-text-top">
                                            <h3>Formation #${creature.formation_no} | ${creature.name} (${creature.type})</h3>
                                        </div>

                                        <div class="formation-text-middle">Bonus Mods: 
                                            <div class="focus-pill">
                                                ${bonusParts}
                                            </div>
                                        </div>

                                        <div class="formation-text-bottom">
                                            Base Str: ${Number(creature.strength).toLocaleString()}
                                            &nbsp;|&nbsp;
                                            Base Hth: ${Number(creature.health).toLocaleString()}
                                        </div>
                                    </div>
                                </div>

                                <div class="monster-grid" style="margin-top:15px;">
                                    <table class="bonus-grid">
                                        <thead>
                                            <tr>
                                                <th>${creature.name}</th>
                                                <th>Base</th>
                                                <th>200%</th>
                                                <th>400%</th>
                                                <th>600%</th>
                                                <th>800%</th>
                                                <th>1000%</th>
                                                <th>1200%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Units to Send (STR)</td>
                                                ${strRow}
                                            </tr>
                                            <tr>
                                                <td>Expected Losses (HTH)</td>
                                                ${hlhRow}
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            document.getElementById('creatureDisplay').innerHTML = html;
                        }

                        /* ------------------ NAV ------------------ */

                        document.getElementById('next').onclick = () => {
                            currentIndex = (currentIndex + 1) % attackGroups.length;
                            renderCreature(currentIndex);
                        };

                        document.getElementById('prev').onclick = () => {
                            currentIndex = (currentIndex - 1 + attackGroups.length) % attackGroups.length;
                            renderCreature(currentIndex);
                        };

                        /* ------------------ INIT ------------------ */

                        renderCreature(currentIndex);
                      </script>
                    <?php else: ?>
                        <p>No creatures found.</p>
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