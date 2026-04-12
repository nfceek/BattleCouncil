<?php
$pdo = new PDO(...);

if ($_POST) {

    $stmt = $pdo->prepare("
        INSERT INTO clan_castles
        (clan_id, kingdom_id, name, x, y)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['clan_id'],
        $_POST['kingdom_id'],
        $_POST['name'],
        $_POST['x'],
        $_POST['y']
    ]);

    // FUTURE:
    // RewardHookService::onCreateCastle($userId);

    echo "Castle created";
}
?>

<form method="POST">
    <input name="kingdom_id" placeholder="Kingdom ID">
    <input name="clan_id" placeholder="Clan ID">
    <input name="name" placeholder="Castle Name">
    <input name="x" placeholder="X">
    <input name="y" placeholder="Y">
    <button type="submit">Create Castle</button>
</form>