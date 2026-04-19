<?php
$pageClass = 'page-tavern';
$pageTitle = "The Tavern";
$pageCss = "tavern";

require_once __DIR__ . '/../includes/header.php';
?>


<!-- MAIN CONTENT -->
<div class="container tavern-container">
    <div style="padding-bottom: 10px">
        <h1>Eat, Drink & Be Merry ... Dammit !</h1>
    </div>
    <div class="bc-grid">

            <!-- ================= LEFT: MESSAGE BOARD ================= -->
            <div class="bc-card tavern-core">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/images/cards/message_board.jpg" alt="Player Archive">
                    </div>    
                    <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="future-header">Message Board</h2>
                        <h3 class="future">Future Use</h3>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>Gotta Tip or Tidbit of knowledge you want to share?</p>
                    </div>
                </div>
            </div>

            <!-- ================= CENTER: TAVERN CORE ================= -->
          <div class="bc-card tavern-core">
                <div class="bc-img-soon" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/tavern_stage.jpg" alt="Player Archive">
                </div>    
                <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="header">Tavern Stage</h2>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <div class="bc-card-body tavern-floor">

                            <!-- STAGE -->
                            <div class="tavern-stage" id="tavernStage">

                                <img src="/images/tavern/bg.png" class="tavern-bg" alt="Tavern Background">

                                <div id="talkingHeadZone"></div>

                            </div>

                            <!-- ACTION BAR -->
                            <div class="tavern-actions">
                                <button class="bc-btn" id="btnSpeak">Speak</button>
                                <button class="bc-btn" id="btnListen">Listen</button>
                                <button class="bc-btn" id="btnDrink">Order Drink</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= RIGHT: BAR + RUMORS ================= -->
            <div class="bc-card tavern-core">
                <div class="bc-img-soon" style="height: 220px;">
                    <img src="<?= BASE_URL ?>/images/cards/tavern_wench.png" alt="Player Archive">
                    </div>    
                    <div class="bc-content">
                    <div class="bc-content-leader" style="text-align:center">
                        <h2 class="future-header">The Bar</h2>
                        <h3 class="future">Future Use</h3>
                    </div>
                    <div class="bc-content-inner" style="padding: 15px;">  
                        <p>What cha drinkin` ?</p>
                    </div>
                </div>
            </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>