<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/../config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/../helpers/functions.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

require_once __DIR__ . '/../services/PointsService.php';
require_once __DIR__ . '/../services/ClanServices.php';

$userId = 1;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = ClanServices::submit($pdo, $userId, $_POST);
}

$balance = PointsService::getBalance($pdo, $userId);
$ledger = PointsService::getLedger($pdo, $userId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clan Submission</title>
    <style>
        body { background:#111; color:#eee; font-family:Arial; padding:20px; }
        .card { background:#1c1c1c; padding:15px; margin-bottom:15px; border-radius:8px; }
        input, select { padding:6px; margin:4px; }
    </style>
</head>
<body>

<h1>Clan Intelligence System</h1>

<div class="card">
    <h2>Balance: <?= $balance ?></h2>
</div>

<?php if ($message): ?>
<div class="card">
    <strong><?= $message['success'] ? 'SUCCESS' : 'ERROR' ?></strong><br>
    <?= $message['success']
        ? "Points Awarded: " . $message['points_awarded']
        : $message['error'] ?>
</div>
<?php endif; ?>

<div class="card">
<form method="POST">

<h3>Clan Submission</h3>

Kingdom #: <input name="kingdom" required><br>
Clan Name: <input name="clan_name" required><br>
Clan Abbr (3): <input name="abbr" maxlength="3" required><br>

Capital X: <input name="x" required>
Capital Y: <input name="y" required><br>

ROE:
<select name="roe">
    <option value="">--</option>
    <option value="1">Yes</option>
    <option value="0">No</option>
</select>

Follows ROE:
<select name="follows_roe">
    <option value="">--</option>
    <option value="1">Yes</option>
    <option value="0">No</option>
</select><br>

Members: <input name="members"><br>
Leader: <input name="leader"><br>
Language: <input name="language"><br>

Size:
<select name="size">
    <option value="">--</option>
    <option value="S">Small</option>
    <option value="M">Medium</option>
    <option value="L">Large</option>
</select><br><br>

<button type="submit">Submit Clan</button>

</form>
</div>

<div class="card">
<h3>Recent Activity</h3>
<?php foreach ($ledger as $row): ?>
    <div><?= $row['points'] ?> | <?= $row['reason'] ?> | <?= $row['created_at'] ?></div>
<?php endforeach; ?>
</div>

</body>
</html>