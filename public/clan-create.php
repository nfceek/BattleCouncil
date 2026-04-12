<?php
$pdo = new PDO(...);

if ($_POST) {

    $stmt = $pdo->prepare("
        INSERT INTO kingdom_clans
        (kingdom_id, clan_name, clan_abbr, capital_x, capital_y, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['kingdom_id'],
        $_POST['name'],
        $_POST['abbr'],
        $_POST['x'],
        $_POST['y'],
        1 // user id placeholder
    ]);

    // FUTURE HOOK:
    // RewardHookService::onCreateClan($userId);

    echo "Clan created";
}
?>

<form method="POST">
    <input name="kingdom_id" placeholder="Kingdom ID">
    <input name="name" placeholder="Clan Name">
    <input name="abbr" placeholder="ABC">
    <input name="x" placeholder="X">
    <input name="y" placeholder="Y">
    <button type="submit">Create Clan</button>
</form>