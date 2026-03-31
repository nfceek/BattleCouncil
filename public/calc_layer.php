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

                        <!-- Troops Section -->
                        <div class="planner-section" style="margin-bottom:10px;">

                            <label><strong>Troop Level: </strong></label>
                            
                            <div>   <!-- div for inline -->
                                <div> <!-- div for Mtd -->
                                    <label>
                                        <input type="checkbox" name="useMtdFighters" value="1" 
                                            <?=!empty($inputs['useMtdFighters']) ? 'checked' : '' ?> disabled>
                                        Mounted
                                    </label>
                                    
                                    <select name="mtdLevel" class="selectLevel">
                                        <?php for($i=3;$i<=9;$i++): ?>
                                            <option value="<?= $i ?>" <?= ($inputs['mtdLevel']==$i)?'selected':'' ?>>
                                                Level <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div> <!-- div for rng -->
                                    <label>
                                        <input type="checkbox" name="useRngFighters" value="1" 
                                            <?=!empty($inputs['useRngFighters']) ? 'checked' : '' ?> disabled>
                                        Archers
                                    </label>
                                    
                                    <select name="rngLevel" class="selectLevel">
                                        <?php for($i=3;$i<=9;$i++): ?>
                                            <option value="<?= $i ?>" <?= ($inputs['rngLevel']==$i)?'selected':'' ?>>
                                                Level <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div> <!-- div for Mel -->
                                    <label>
                                        <input type="checkbox" name="useMelFighters" value="1" 
                                            <?=!empty($inputs['useMelFighters']) ? 'checked' : '' ?> disabled>
                                        Melee
                                    </label>
                                    
                                    <select name="melLevel" class="selectLevel">
                                        <?php for($i=3;$i<=9;$i++): ?>
                                            <option value="<?= $i ?>" <?= ($inputs['melLevel']==$i)?'selected':'' ?>>
                                                Level <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div> <!-- div for fly -->
                                    <label>
                                        <input type="checkbox" name="useFlyFighters" value="1" 
                                            <?=!empty($inputs['useFlyFighters']) ? 'checked' : '' ?> disabled>
                                        Flying
                                    </label>
                                    
                                    <select name="flyLevel" class="selectLevel">
                                        <?php for($i=3;$i<=9;$i++): ?>
                                            <option value="<?= $i ?>" <?= ($inputs['flylevel']==$i)?'selected':'' ?>>
                                                Level <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Creature Section -->
                        <div class="bonus-section" style="margin-bottom:12px;">
                            <label><strong>Creature Level: </strong></label>

                            <label>
                                <input type="checkbox" name="useCreatures" value="1" 
                                    <?= !empty($inputs['useCreatures']) ? 'checked' : '' ?> checked>
                                Creatures
                            </label>       

                            <select name="playerLevel" class="selectLevel">
                                <?php for($i=3;$i<=9;$i++): ?>
                                    <option value="<?= $i ?>" <?= ($inputs['playerLevel']==$i)?'selected':'' ?>>
                                        Level <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>

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
require_once __DIR__ . '/../includes/footer.php';