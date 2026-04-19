<?php
$pageClass = 'page-tavern-stage';
$pageTitle = "The Tavern Stage";
$pageCss = "tavern";

require_once __DIR__ . '/../includes/header.php';
?>

<div class="bc-container tavern-container">

    <div class="bc-row">

        <!-- MAIN STAGE -->
        <div class="bc-col-12">

            <div class="bc-card tavern-core">

                <div class="bc-card-header">
                    <h3>The Tavern Stage</h3>
                </div>

                <div class="bc-card-body tavern-floor">

                    <!-- ======================
                         SINGLE SOURCE OF TRUTH
                         ====================== -->
                    <div id="tavernStage" class="tavern-stage">

                        <!-- Background -->
                        <img src="/images/tavern/bg-med.jpg" class="tavern-bg" alt="Tavern">

                        <!-- TALKING HEAD ZONE -->
                        <div id="talkingHeadZone"
                             data-zone="center"
                             data-mode="queue"></div>

                        <!-- NPC: BAR WENCH -->
                        <div id="npc_barWench"
                             class="npc npc-bar-wench"
                             data-npc="barWench"
                             data-state="idle"
                             data-mood="annoyed"></div>

                        <!-- FUTURE NPC SLOT -->
                        <div id="npc_overseer"
                             class="npc npc-overseer"
                             data-npc="overseer"
                             data-state="sleeping"></div>

                    </div>

                    <!-- ACTION BAR (INSIDE CONTEXT) -->
                    <div class="tavern-actions">
                        <button class="bc-btn" id="btnSpeak">Speak</button>
                        <button class="bc-btn" id="btnListen">Listen</button>
                        <button class="bc-btn" id="btnDrink">Order Drink</button>

                        <!-- TEST BUTTON (ENGINE HOOK) -->
                        <button class="bc-btn bc-btn-debug" id="btnTestTavern">
                            Test Engine
                        </button>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <button class="bc-btn bc-btn-danger" id="btnStopTavern">
        Stop
    </button>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>