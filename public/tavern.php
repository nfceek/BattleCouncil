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
                            <h2>The Kings' Rook</h2>
                        </div>

                        <div class="bc-content-inner" style="padding: 15px;">
                            What is the key?
                        </div>
                    </div>
                </a>
            </div>
            <!-- ================= RIGHT: BAR + RUMORS ================= -->
            <div class="bc-card tavern-core">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/images/cards/lynnes-a.jpg" alt="Lynnes Mystic">
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
                        <h2 class="future-header">Mystic Lynnes</h2>
                        <h3 class="future">Future Use</h3>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>What does yer future portend ?</p>
                    </div>
                </div>
            </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>