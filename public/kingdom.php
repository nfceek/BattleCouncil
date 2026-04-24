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
            <a href="./map.php" class="bc-card">
                <div class="bc-img" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/../images/cards/realms2.png" alt="The Realm">
                </div>    

                <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2>The Kingdom</h2>
                    </div>

                    <div class="bc-content-inner" style="padding: 15px;">  
                        <div class="lead-list">
                            <div class="lead-item">
                                <span class="icon">👹</span>
                                <span> Want to know more ?</span>
                            </div>

                            <div class="lead-item">
                                <span class="icon">📊</span>
                                <span>Are you searching for someone ?</span>
                            </div>

                            <div class="lead-item">
                                <span class="icon">🏰</span>
                                <span>Are you searching for something ?</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


                
        <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/public/clan_create.php" class="bc-card"> 
                <div class="bc-img" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/../images/cards/ledger1.png" alt="The Ledger">
                </div>    

                <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2>The Ledger</h2>
                        <h3>Add Info -- Get Rewards</h3>
                    </div>

                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>
                            <u>Reward points pages:</u>
                        </p>
                        <div class="lead-list">
                            <div class="lead-item">
                                <span class="icon">👹</span>
                                <span>Enter Clan Capitals -- Get Points</span>
                            </div>

                            <div class="lead-item">
                                <span class="icon">📊</span>
                                <span>Enter Player Castle info -- Get Points</span>
                            </div>

                            <div class="lead-item">
                                <span class="icon">🏰</span>
                                <span>Look up Clan Info --  Pay Points</span>
                            </div>
                        </div>
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