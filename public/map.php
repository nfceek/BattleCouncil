<?php
$pageTitle = "Kingdom Atlas";
$pageCss = "map";

$kingdomId = $_GET['k'] ?? null;

require_once __DIR__ . '/../includes/header.php';
?>

<script>
const BASE_URL = "<?= BASE_URL ?>";
const MAP_MODE = "hybrid";
const PRELOAD_KINGDOM = <?= $kingdomId ? (int)$kingdomId : 'null' ?>;
</script>


<div class="container bc-page">

    <!-- =========================
        1. KINGDOM CONTROL CARD
    ========================== -->
    <div class="bc-row">
        <div class="bc-col-12">
            <div class="bc-card">

                <div class="bc-card-header">
                    <h2>Kingdom Selector</h2>
                </div>

                <div class="bc-card-body">

                    <div class="bc-form-inline">

                        <select id="kingdomSelect">
                            <option value="">Select Kingdom</option>

                            <optgroup label="Main Realm (1–34)">
                                <?php for ($i = 1; $i <= 34; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </optgroup>

                            <optgroup label="Expansion (1000–1050)">
                                <?php for ($i = 1000; $i <= 1050; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </optgroup>

                        </select>

                        <button id="loadKingdomBtn" class="btn btn-primary">
                            Load Kingdom
                        </button>

                        <input 
                            type="text"
                            id="kingdomQuickInput"
                            placeholder="Enter Kingdom ID..."
                        />
                        <button id="backToWorldBtn" class="btn" style="display:none;">
                            ← Back to World
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- =========================
        2. MAP VIEW CARD
    ========================== -->
    <div class="bc-row">

        <!-- MAP -->
        <div class="bc-col-8">
            <div class="bc-card map-card">
                <div class="bc-card-body">
                    <div class="map-wrapper">
                        <!-- Background Map -->
                        <img id="mapBg"
                            class="map-bg"
                            src="/images/maps/kingdom.png"
                            alt="Kingdom Map">

                        <!-- Capital -->
                        <div id="capitalPin" class="capital-pin">
                            <img src="/images/capitals/default.png" width="45" height="45">
                        </div>

                        <!-- Dynamic Clan Pins -->
                        <div id="mapPins"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INFO PANEL -->
        <div class="bc-col-4">

            <div class="bc-card info-card">

                <div class="bc-card-header">
                    <h3>Clan Intel</h3>
                </div>

                <div class="bc-card-body">

                    <div id="mapInfo">
                        <p class="muted">Hover or click a clan to view details</p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>