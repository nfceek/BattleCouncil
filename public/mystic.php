<?php
$pageClass = 'page-mystic';
$pageTitle = "Mystic Lynnes";
$pageCss = "mystic";
$pageJs  = "mystic";   

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container tavern-container-stage">
    <div class="container-lead">
        Welcome to the Talking Head Tavern
    </div>

    <div class="bc-row">

        <div class="bc-col-12">
            <div class="bc-card tavern-core">

                <div id="tavernStage" class="tavern-stage">

                    <!-- BACKGROUND -->
                    <img src="/images/mystic/mystic-bg.jpg"
                         class="mystic-bg"
                         alt="Mystic Tavern Background">

                    <!-- OUTPUT ZONE -->
                    <div id="talkingHeadZone"
                         data-zone="center"
                         data-mode="queue"></div>

                    <!-- NPC -->
                    <div id="npc_lynne"
                         class="npc npc-Lynne"
                         data-npc="Lynne"
                         data-state="idle"
                         data-mood="mysterious">
                    </div>

                </div>

                <!-- INPUT AREA -->
                <div class="tavern-input">

                    <textarea id="tavernInput"
                              class="bc-input"
                              maxlength="200"
                              placeholder="Ask Mystic Lynne your question..."></textarea>

                    <div class="tavern-input-meta">
                        <span id="charCount">0 / 200</span>
                    </div>

                    <button class="bc-btn" id="btnPlayInput">
                        Ask the Oracle
                    </button>

                    <div class="tavern-actions">
                        <button class="bc-btn" id="btnSpeak">Speak</button>
                        <button class="bc-btn" id="btnListen">Listen</button>
                        <button class="bc-btn" id="btnDrink">Order</button>

                        <button class="bc-btn bc-btn-debug" id="btnTestTavern">
                            Test Engine
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>