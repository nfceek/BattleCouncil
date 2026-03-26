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

    $inputs         = $data['inputs'];
    $view           = $data['data'];

    $squadStats     = $view['squadStats'] ?? [];
    $squads         = $view['squads'] ?? [];
    $creatures      = $view['creatures'] ?? [];
    $monsters       = $view['monsters'] ?? [];
    $attackGroups   = $view['attackGroups'] ?? [];
    $enemyType      = $view['enemyType'] ?? null;
  
    // ==============================
    // PAGE SETTINGS
    // ==============================
    $pageClass = 'page-monster-hunt';

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

                        <!-- Player Level -->
                        <div class="planner-section" style="margin-bottom:10px;">
                            <label><strong>Creature Level: </strong></label>
                            <select name="playerLevel" class="selectLevel">
                                <?php for($i=3;$i<=10;$i++): ?>
                                    <option value="<?= $i ?>" <?= ($inputs['playerLevel']==$i)?'selected':'' ?>>
                                        Level <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Unit Types -->
                        <div class="bonus-section" style="margin-bottom:12px;">
                            <label><strong>Troops: </strong></label>
                            <!--<label>
                                <input type="checkbox" name="useFighters" value="1" 
                                     //!empty($inputs['useFighters']) ? 'checked' : '' ?> disabled>
                                Fighters
                            </label>-->

                            <label>
                                <input type="checkbox" name="useCreatures" value="1" 
                                    <?= !empty($inputs['useCreatures']) ? 'checked' : '' ?> checked>
                                Creatures
                            </label>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit" 
                                    name="buildPlan" 
                                    value="1" 
                                    class="btn btn-primary btn-build"
                                    id="buildPlanBtn">
                                ⚔ Build Attack Plan
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

                    <?php
                    // init safety (prevents warnings)
                    $monsterHealthList   = $monsterHealthList   ?? [];
                    $monsterStrengthList = $monsterStrengthList ?? [];
                    $monsterTotalHealth  = $monsterTotalHealth  ?? 0;
                    $monsterTotalStrength= $monsterTotalStrength ?? 0;
                    ?>

                    <div class="inner-card">

                        <!-- =========================
                            SQUAD HEADER
                        ========================== -->
                        <div class="squad-text-block-flex">

                            <!-- LEFT: IMAGE -->
                            <div class="squad-image-container">
                                <img src="<?= '/images/monsters/' . e($squadStats['image_base'] ?? 'default') .'.png' ?>" class="squad-img" alt="<?= e($squadStats['name'] ?? '') ?>">
                            </div>

                            <!-- RIGHT: TEXT -->
                            <div class="squad-info">
                                
                                <!-- TOP LINE -->
                                <div class="squad-line squad-line-top">
                                    <h3>
                                        <?= e($squadStats['rarity'] ?? '') ?>
                                        <?= e($squadStats['name'] ?? '') ?>
                                        | Lvl <?= (int)($squadStats['level'] ?? 0) ?>
                                    </h3>
                                </div>

                                <!-- MIDDLE (optional counter bar) -->
                                <?php if (!empty($counterSignal)): ?>
                                    <div class="squad-line squad-line-middle">
                                        <div class="counter-bar">
                                            <?php foreach ($counterSignal as $t => $c): ?>
                                                <span class="counter <?= $c ?>"><?= $t ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- BOTTOM LINE -->
                                <div class="squad-line squad-line-bottom" style="padding-top:8px;padding-bottom:8px;">
                                    Valor: <?= shortNum($squadStats['valor'] ?? 0) ?>
                                    &nbsp;|&nbsp;
                                    XP: <?= shortNum($squadStats['xp'] ?? 0) ?>
                                </div>

                                <!-- =========================
                                    MONSTERS
                                ========================== -->
                                <?php if (!empty($monsters)): ?>
                                    <div class="squad-line squad-line-monsters">
                                        <div class="monster-grid">
                                            <?php foreach ($monsters as $monster): ?>
                                                <?php
                                                    $mel = $monster['bonus_mel'] ?? 0;
                                                    $mtd = $monster['bonus_mtd'] ?? 0;
                                                    $rng = $monster['bonus_rng'] ?? 0;
                                                    $fly = $monster['bonus_fly'] ?? 0;
                                                    $oth = $monster['bonus_oth'] ?? 0;

                                                    $health   = $monster['total_health'] ?? 0;
                                                    $strength = $monster['total_strength'] ?? 0;
                                                    $monsterHealthList[]   = $health;
                                                    $monsterStrengthList[] = $strength;
                                                    $monsterTotalHealth   += $health;
                                                    $monsterTotalStrength += $strength;
                                                ?>
                                                <details class="monster-row">
                                                    <summary class="monster-summary" style="text-align:left;padding-left:6px;">
                                                        <span class="col col-name">
                                                            <?= shortNum($monster['quantity'] ?? 0) ?> <?= e($monster['name']) ?> (<?= e($monster['type']) ?>)
                                                        </span>
                                                    </summary>

                                                    <div class="monster-calc">
                                                        <span class="col col-hlh">
                                                            Hth: <?= shortNum($health) ?>
                                                        </span>

                                                        <span class="col col-str">
                                                            Str: <?= shortNum($strength) ?>
                                                        </span>

                                                        <!--<span class="bonus-col">
                                                            <span class="dot <?= bonusDot($mel) ?>" title="Mel <?= $mel ?>%"></span> Mel
                                                            <span class="dot <?= bonusDot($mtd) ?>" title="Mtd <?= $mtd ?>%"></span> Mtd
                                                            <span class="dot <?= bonusDot($rng) ?>" title="Rng <?= $rng ?>%"></span> Rng
                                                            <span class="dot <?= bonusDot($fly) ?>" title="Fly <?= $fly ?>%"></span> Fly
                                                            <span class="dot <?= bonusDot($oth) ?>" title="Other <?= $oth ?>%"></span> Oth
                                                        </span>-->
                                                    </div>
                                                </details>
                                            <?php endforeach; ?>
                                        </div>
                                <?php
                                // ✅ compute AFTER loop (correct placement)
                                $monsterMaxHealth   = !empty($monsterHealthList) ? max($monsterHealthList) : 0;
                                $monsterMaxStrength = !empty($monsterStrengthList) ? max($monsterStrengthList) : 0;
                                ?>
                                <?php else: ?>
                                    <div style="text-align:center;">
                                        <p>No monsters assigned.</p>
                                    </div>
                                <?php endif; ?>
                            </div>    
                        </div>
                    </div>
                </div>

                    <hr style="margin: 10px"> 

                    <?php if (!empty($monsters)): ?>
                        <?php if (!empty($attackGroups)): ?>
                            <div id="creatureDisplay"></div>
                                <div class="group-switch">
                                    <button type="button" id="prev" class="btn btn-nav">← Prev</button>
                                    <button type="button" id="next" class="btn btn-nav">Next →</button>
                                </div>
                            <script>           
                                const attackGroups = <?= json_encode($attackGroups) ?>;
                                const monsterMaxHealth = <?= (int)$monsterMaxHealth ?>;
                                const monsterMaxStrength = <?= (int)$monsterMaxStrength ?>;
                                let currentIndex = 0;

                                function shortNum(n) {
                                    n = Number(n) || 0;

                                    if (n >= 1000000000) return (n / 1000000000).toFixed(0) + ' B';
                                    if (n >= 1000000)     return (n / 1000000).toFixed(0) + ' M';
                                    if (n >= 1000)        return (n / 1000).toFixed(0) + ' K';

                                    return n.toLocaleString();
                                }

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
                                        return '<span style="color:green;font-wieght:bold">0</span>';
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
                                        const losses = calcLosses(creature.health, units, p);

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
                                                    <h3>Formation #${creature.formation_no} ${creature.name} (${creature.type})</h3>
                                                </div>

                                                <div class="formation-text-middle" style="padding-top:8px;padding-bottom:8px;">
                                                    Base Str: ${shortNum(creature.strength)}
                                                    &nbsp;|&nbsp;
                                                    Base Hth: ${shortNum(creature.health)}
                                                </div>

                                                <!--<div class="formation-text-bottom">
                                                    Bonus Mods:
                                                    <div class="focus-pill">${bonusParts}</div>
                                                </div>-->
                                                
                                            </div>
                                        </div>

                                        <div class="creature-grid">
                                            <table class="creature-table">
                                                <thead>
                                                    <tr>
                                                        <th class="col-label"></th>
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
                                                        <td class="row-label">Units (STR)</td>
                                                        ${strRow}
                                                    </tr>
                                                    <tr>
                                                        <td class="row-label">Losses (HTH)</td>
                                                        ${hlhRow}
                                                    </tr>
                                                </tbody>
                                            </table>
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
                        <?php else: ?>
                            <p>No attack groups available.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div style="text-align:center;">
                            <p>No monsters assigned to this squad.</p>
                        </div>
                    <?php endif; ?>
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