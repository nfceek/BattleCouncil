<?php
$kingdomId = $_GET['k'] ?? 274;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kingdom Atlas</title>
    <link rel="stylesheet" href="/public/map.css">
</head>
<body>

<div id="map-container">
    <div id="map"></div>
</div>

<script>
const KINGDOM_ID = <?= (int)$kingdomId ?>;

fetch(`/api/map.php?k=${KINGDOM_ID}`)
    .then(res => res.json())
    .then(data => renderMap(data));
</script>

<script src="/public/map.js"></script>

</body>
</html>