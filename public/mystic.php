<?php
$pageClass = 'page-mystic';
$pageTitle = "Mystic Lynnes";
$pageCss = "mystic";
$pageJs = 'mystic';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mystic-container-stage">
    <div class="container-lead">
        Questions Answered
    </div>
    <div class="bc-row">

        <!-- MAIN STAGE -->
        <div class="bc-col-12">

            <div class="bc-card mystic-core">



                    <div id="mysticStage" class="mystic-stage">

                        <!-- Background -->
                        <img src="/images/mystic/mystic-bg.jpg" class="mystic-bg" alt="mystic" style="padding-top:12px;">

                        <!-- TALKING HEAD ZONE -->
                        <div id="talkingHeadZone"
                             data-zone="center"
                             data-mode="queue"></div>

                        <div id="npc_lynne"
                            class="npc npc-Lynne"
                            data-npc="Lynne"
                            data-state="idle"
                            data-mood="annoyed">

                            <div class="npc-body">
                                <div class="lynne-mouth"></div>
                            </div>

                        </div>
                    </div>
                    <!-- ACTION BAR (INSIDE CONTEXT) -->

                    <div class="mystic-input">

                        <!-- TEXT INPUT -->
                        <textarea id="mysticInput"
                                class="bc-input"
                                maxlength="200"
                                placeholder="Do you ask something of the mystic ?"
                        ></textarea>

                        <!-- COUNTER -->
                        <div class="mystic-input-meta">
                            <span id="charCount">0 / 200</span>
                        </div>

                        <div class="mystic-actions">
                            <button class="bc-btn" id="btnPlayInput">
                                Ask Your Question
                            </button>

                           <!--<button class="bc-btn" id="btnPlayHome">
                                Home
                            </button>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--
    <button class="bc-btn bc-btn-danger" id="btnStopmystic">
        Stop
    </button>
    -->
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>