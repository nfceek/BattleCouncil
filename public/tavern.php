<?php
$pageClass = 'page-tavern';
$pageTitle = "The Tavern";
$pageCss = "tavern";

require_once __DIR__ . '/../includes/header.php';
?>


<!-- MAIN CONTENT -->
<div class="container tavern-container">
    <div class="container-lead">
        Eat, Drink & Be Merry ... Dammit !
    </div>
    <div class="bc-grid">

            <!-- ================= LEFT: MESSAGE BOARD ================= -->
            <div class="bc-card tavern-core">
                <a href="<?= BASE_URL ?>/public/message_board.php" class="bc-card">
                    <div class="bc-img" style="height: 220px;">
                        <img src="<?= BASE_URL ?>/images/cards/message_board.jpg" alt="Crowing Wall">
                        </div>    
                        <div class="bc-content">
                        <div class="bc-content-leader" style="text-align:center">
                            <h2>The Crowing Wall</h2>
                        </div>
                        <div class="bc-content-inner" style="padding: 15px;">  
                            <p>Gotta Tip or Tidbit of knowledge you want to share?</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- ================= CENTER: TAVERN CORE ================= -->
            <div class="bc-card tavern-core">
                <a href="<?= BASE_URL ?>/public/tavern_stage.php" class="bc-card">
                    <div class="bc-img" style="height: 220px;">
                        <img src="<?= BASE_URL ?>/images/cards/tavern_wench.png"  alt="Talking Heads">
                    </div>

                    <div class="bc-content">
                        <div class="bc-content-leader" style="text-align:center">
                            <h2>The Talking Head Tavern</h2>
                        </div>

                        <div class="bc-content-inner" style="padding: 15px;">
                            C'mon in. Have a pint and share a few lies
                        </div>
                    </div>
                </a>
            </div>
            <!-- ================= RIGHT: BAR + RUMORS ================= -->
            <div class="bc-card tavern-core">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/images/cards/lynnes-b.jpg" alt="Lynnes Mystic">
                    </div>    
                    <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="future-header">Mystic Lynnes</h2>
                        <h3 class="future">Future Use</h3>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>What does yer future portend ?</p>
                    </div>
                </div>
            </div>

            <!-- ================= Kings Rook ================= -->
            <div class="bc-card tavern-core">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/images/cards/kingsrook.jpg" alt="Kings Rook">
                    </div>    
                    <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="future-header">The Kings' Rook</h2>
                        <h3 class="future">Future Use</h3>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>What is the key?</p>
                    </div>
                </div>
            </div>

  
            <!-- ================= The Mercer ================= -->
            <div class="bc-card"> 
            <a href="#" class="bc-card">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/../images/cards/gear_info.png" alt="The Realm">
                </div> 
                <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="future-header">Finery & Armory</h2>
                        <h3 class="future-header">Get Info -- Spend Rewards </h3>
                    </div>

                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>
                        The 2 things the games needs<br />
                         Time and something of value
                        </p>
                    </div>

                    <p>
                        <u>Info pages:</u>
                    </p>
                    <div class="lead-list">
                        <div class="lead-item">
                            <span class="icon">👹</span>
                            <span>How long does it take?</span>
                        </div>

                        <div class="lead-item">
                            <span class="icon">📊</span>
                            <span>Learn what to wear in the Realm</span>
                        </div>

                        <div class="lead-item">
                            <span class="icon">🏰</span>
                            <span>Gear for your Hero & Captains</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>