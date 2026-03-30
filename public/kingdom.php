<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/../config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/../helpers/functions.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

// ==============================
// PAGE SETTINGS
// ==============================
$pageClass = 'The Realm';

// ==============================
// HEADER
// ==============================
require_once __DIR__ . '/../includes/header.php';
?>

<!-- MAIN CONTENT -->
<div class="container">
    <h1>Welcome to <?= APP_NAME ?></h1>

    <div class="bc-grid">
        <div class="bc-card"> 
            <a href="#" class="bc-card">
                
                <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/../images/cards/realms1.png" alt="The Realm">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                    <h2>The Kingdom</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <p>
                    Want to know more about other lands? Are you searching for<br />
                    someone or something? This is your resource
                    </p>
                </div>
                </div>
            </a>
        </div>

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