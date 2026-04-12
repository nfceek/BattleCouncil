<?php
$query = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clan Search</title>
</head>
<body>

<h2>Search Clans</h2>

<form method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Clan name or aka">
    <button type="submit">Search</button>
</form>

<?php
if ($query) {
    $pdo = new PDO(...);

    $stmt = $pdo->prepare("
        SELECT id, clan_name, clan_abbr, kingdom_id
        FROM kingdom_clans
        WHERE clan_name LIKE ? OR clan_abbr LIKE ?
        LIMIT 20
    ");

    $stmt->execute(["%$query%", "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $r) {
        echo "<div>";
        echo "<a href='/map.php?k={$r['kingdom_id']}'>";
        echo "{$r['clan_name']} ({$r['clan_abbr']})";
        echo "</a>";
        echo "</div>";
    }
}
?>

</body>
</html>