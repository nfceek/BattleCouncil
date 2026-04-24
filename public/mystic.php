<?php
$pageClass = 'page-mystic';
$pageTitle = "Mystic Lynnes";
$pageCss = "mystic";

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container tavern-container-stage">
    <div class="container-lead">
        Welcome Lynnes' Mystic Corner Tavern
    </div>
    <div class="bc-row">

        <!-- MAIN STAGE -->
        <div class="bc-col-12">

            <div class="bc-card tavern-core">



                    <div id="tavernStage" class="tavern-stage">

                        <!-- Background -->
                        <img src="/images/mystic/mystic-bg.jpg" class="mystic-bg" alt="Tavern">

                        <!-- TALKING HEAD ZONE -->
                        <div id="talkingHeadZone"
                             data-zone="center"
                             data-mode="queue"></div>

                        <!-- NPC: BAR WENCH -->
                        <div id="npc_lynne"
                             class="npc npc-Lynne"
                             data-npc="Lynne"
                             data-state="idle"
                             data-mood="annoyed"></div>

                    </div>

                    <!-- ACTION BAR (INSIDE CONTEXT) -->


                    <div class="tavern-input">

                        <!-- TEXT INPUT -->
                        <textarea id="tavernInput"
                                class="bc-input"
                                maxlength="200"
                                placeholder="Say something to the tavern..."
                        ></textarea>

                        <!-- COUNTER -->
                        <div class="tavern-input-meta">
                            <span id="charCount">0 / 200</span>
                        </div>

                        <!-- ACTION -->
                        <button class="bc-btn" id="btnPlayInput">
                            Play
                        </button>

                        <div class="tavern-actions">
                            <button class="bc-btn" id="btnSpeak">Speak</button>
                            <button class="bc-btn" id="btnListen">Listen</button>
                            <button class="bc-btn" id="btnDrink">Order</button>

                            <!-- TEST BUTTON (ENGINE HOOK) -->
                            <button class="bc-btn bc-btn-debug" id="btnTestTavern">
                                Test Engine
                            </button>
                            
                        </div>

                </div>

                </div>
            </div>

        </div>

    </div>
    <!--
    <button class="bc-btn bc-btn-danger" id="btnStopTavern">
        Stop
    </button>
    -->
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>