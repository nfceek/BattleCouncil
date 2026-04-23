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
    <div class="container-lead">
        Calculate ... Then Kill
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
                        <div class="lead-list">
                            <div class="lead-section">
                                <p class="lead-warning">
                                    Make quick work of smaller monster squads
                                </p>
                            </div>
                            <div class="lead-list">
                                <div class="lead-item">
                                    <u>
                                        <span class="icon"></span>
                                        <span>Recommended for:</span>
                                    </u>
                                </div>

                                <div class="lead-item">
                                    <span class="icon">🏰</span>
                                    <span>Level 4/5/6 players</span>
                                </div>

                                <div class="lead-item">
                                    <span class="icon">👹</span>
                                    <span>Game Map Monster Squads 1-20</span>
                                </div>

                            </div>
                        </div>
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
                        <div class="lead-list">
                            <div class="lead-section">
                                <p class="lead-warning">
                                    Larger squads require layering of troops This is NOT stacking.
                                </p>
                            </div>
                            <div class="lead-list">
                                <div class="lead-item">
                                    <span class="icon">📊</span>
                                    <span>You are Sending Multi Types of Troops</span>
                                </div>

                                <div class="lead-item">
                                    <span class="icon">🏰</span>
                                    <span>You Expect to Survive</span>
                                </div>

                                <div class="lead-item">
                                    <span class="icon">⚔️</span>
                                    <span>Be Sure to Have Doria Loaded!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/public/calc_citadel.php" class="bc-card">
                
                <div class="bc-img-soon" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/citadels.png" alt="Battle Council">
                </div>    

                <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                <h2 class="future-header">Citadel Cracker</h2>
                <h3 class="future">Future Use</h3>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                    <div class="lead-section">
                    <p class="lead-warning">
                        Get the trebuche out!
                    </p>
                    <div class="lead-list">
                        <div class="lead-item">
                            <span class="icon">🏰</span>
                            <span>It's time to go stomp some citadels.</span>
                        </div>

                        <div class="lead-item">
                            <span class="icon">📊</span>
                            <span>Calculate your attack</span>
                        </div>

                        <div class="lead-item">
                            <span class="icon">⚔️</span>
                            <span>Watch the walls fall</span>
                        </div>

                        <div class="lead-item">
                            <span class="icon">⚔️</span>
                            <span>Take the rewards!</span>
                        </div>
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