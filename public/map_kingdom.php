<?php
$pageTitle = "Kingdom Atlas";
$pageCss = "map";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="bc-map">
        <img id="mapBg" class="map-bg" src="">
        <div id="mapPins"></div>
        <div id="mapInfo" class="map-info"></div>
    </div>
</div>

<script>
const KINGDOM_ID = <?= (int)$kingdomId ?>;

fetch(`/public/api/map.php?k=${KINGDOM_ID}`)
    .then(res => res.json())
    .then(data => renderMap(data));
</script>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/map.js"></script>
<script src="/assets/js/languagePicker.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>