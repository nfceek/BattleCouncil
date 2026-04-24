<?php
$pageClass = 'page-market';
$pageTitle = "The market";
$pageCss = "market";

require_once __DIR__ . '/../includes/header.php';
?>


<!-- MAIN CONTENT -->
<div class="container market-container">
    <div class="container-lead">
        Lust for Knowledge !
    </div>
    <div class="bc-grid">

            <!-- ================= LEFT: MESSAGE BOARD ================= -->
            <div class="bc-card market-core">
                <a href="<?= BASE_URL ?>/public/message_board.php" class="bc-card">
                    <div class="bc-img" style="height: 220px;">
                        <img src="<?= BASE_URL ?>/images/cards/message_board.jpg" alt="Crowing Wall">
                        </div>    
                        <div class="bc-content">
                        <div class="bc-content-leader" style="text-align:center">
                            <h2>The Crowing Wall</h2>
                        </div>
                        <div class="bc-content-inner" style="padding: 15px;">  
                            <p>Gotta Tip or Tidbit of knowledge? Share it!</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- ================= CENTER: market ================= -->
            <div class="bc-card market-core">
                <a href="<?= BASE_URL ?>/index.php" class="bc-card">
                    <div class="bc-img-soon" style="height: 220px;">
                        <img src="<?= BASE_URL ?>/images/cards/market_stage.jpg"  alt="Market Stage">
                    </div>

                    <div class="bc-content">
                        <div class="bc-content-leader" style="text-align:center">
                            <h2 class="future-header">Town Square</h2>
                        </div>

                        <div class="bc-content-inner" style="padding: 15px;">
                            Watch the game characters come alive
                        </div>
                    </div>
                </a>
            </div>
            <!-- ================= RIGHT: BAR + RUMORS ================= -->
            <div class="bc-card market-core">
                <a href="<?= BASE_URL ?>/public/mystic.php" class="bc-card">
                    <div class="bc-img" style="height: 220px;">
                        <img src="<?= BASE_URL ?>/images/cards/lynnes-b.jpg" alt="Lynnes Mystic">
                        </div>    
                        <div class="bc-content">
                        <div class="bc-content-leader" style="text-align:center">
                            <h2>Mystic Lynnes</h2>
                            <h3>Questions ?</h3>
                        </div>
                        <div class="bc-content-inner" style="padding: 15px;">  
                            <p>What does yer future portend ?</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- ================= Kings Rook ================= -->
            <div class="bc-card market-core">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/images/cards/kingsrook.jpg" alt="Kings Rook">
                    </div>    
                    <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="future-header">The Kings' Rook</h2>
                        <h2 class="future-header">Tavern</h2>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>The Topic is Dragons....Discuss</p>
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
                            The games in 2 words: Time & Value
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
                    <!-- ================= shiny bits ================= -->
            <div class="bc-card market-core">
                <a href="<?= BASE_URL ?>/index.php" class="bc-card">
                    <div class="bc-img-soon" style="height: 220px;">
                        <img src="<?= BASE_URL ?>/images/cards/shiny_bits.png"  alt="Shoppe">
                    </div>

                    <div class="bc-content">
                        <div class="bc-content-leader" style="text-align:center">
                            <h2 class="future-header">Shiny Bits</h2>
                            <h3 class="future">Gift Shoppe</h3>
                        </div>

                        <div class="bc-content-inner" style="padding: 15px;">
                            Got Gold in Yer Britches to Spend ?
                        </div>
                    </div>
                </a>
            </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>