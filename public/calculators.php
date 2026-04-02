<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/../config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/../helpers/auth.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

// ==============================
// PAGE SETTINGS
// ==============================
$pageClass = 'Calculators';

// ==============================
// HEADER
// ==============================
require_once __DIR__ . '/../includes/header.php';
?>

<!-- MAIN CONTENT -->
<div class="container">
    <div style="padding-bottom: 10px">
        <h1>Calculate ... Then Kill</h1>
    </div>
    <div class="bc-grid">
        <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/public/calc_squad.php" class="bc-card">
                
                <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/orcs.png" alt="Battle Council">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                    <h2>Small Game</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <p>
                    Make quick work of smaller monster squads on the world map.<br />
                    Recommended for: Level 4/5/6 player & monster squads 1-20.
                    </p>
                </div>
                </div>
            </a>
        </div>

        <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/public/calc_layer.php" class="bc-card">
                
                <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/rare_squads.png" alt="Battle Council">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                    <h2>Rare & Epic Squads</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <p>
                    Larger squads require layering of troops.<br />This is NOT 
                    stacking.<br />You are sending troops and<br />expect to survive the 
                    encounter.<br />Be sure to have Doria loaded.
                    </p>
                </div>
                </div>
            </a>
        </div>

        <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/public/calc_citadel.php" class="bc-card">
                
                <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/citadels.png" alt="Battle Council">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                    <h2>Citadel Cracker</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <p>
                    Get the trebuche out.<br />It's time to go stomp
                    some citadels.<br />Calculate your attack,  
                    Watch the walls fall<br >Take the rewards!
                    </p>
                </div>
                </div>

            </a>
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
require_once __DIR__ . '/../includes/footer.php';