<?php
$pageClass = 'page-market-tavern-stage';
$pageTitle = "The market-tavern Stage";
$pageCss = "market-tavern";

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container market-tavern-container-stage">
    <div class="container-lead">
        Welcome to the Talking Head market-tavern
    </div>
    <div class="bc-row">

        <!-- MAIN STAGE -->
        <div class="bc-col-12">

            <div class="bc-card market-tavern-core">



                <div class="bc-card-body market-tavern-floor">
                    <div class="market-tavernSelect">
                            <!-- NPC SELECT (future ready) -->
                        <select id="npcSelect" class="bc-input">
                            <option value="barWench">Choose Character</option>
                            <option value="blueDragon">Blue Dragon</option>
                            <option value="barWench">Bar Wench</option>
                        </select>
                    </div>

                    <div id="market-tavernStage" class="market-tavern-stage">

                        <!-- Background -->
                        <img src="/images/market-tavern/bg/bg_tan.jpg" class="market-tavern-bg" alt="market-tavern">

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


                    <div class="market-tavern-input">

                        <!-- TEXT INPUT -->
                        <textarea id="market-tavernInput"
                                class="bc-input"
                                maxlength="200"
                                placeholder="Say something to the market-tavern..."
                        ></textarea>

                        <!-- COUNTER -->
                        <div class="market-tavern-input-meta">
                            <span id="charCount">0 / 200</span>
                        </div>

                        <!-- ACTION -->
                        <button class="bc-btn" id="btnPlayInput">
                            Play
                        </button>

                        <div class="market-tavern-actions">
                            <button class="bc-btn" id="btnSpeak">Speak</button>
                            <button class="bc-btn" id="btnListen">Listen</button>
                            <button class="bc-btn" id="btnDrink">Order</button>

                            <!-- TEST BUTTON (ENGINE HOOK) -->
                            <button class="bc-btn bc-btn-debug" id="btnTestmarket-tavern">
                                Test Engine
                            </button>
                            
                        </div>

                </div>

                </div>
            </div>

        </div>

    </div>
    <!--
    <button class="bc-btn bc-btn-danger" id="btnStopmarket-tavern">
        Stop
    </button>
    -->
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>