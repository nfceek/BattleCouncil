<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/permissions.php';

$user = getCurrentUser();

if (!canVote($user)) {
    http_response_code(403);
    exit("Login required.");
}

$tipId  = (int)($_POST['tip_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);

if (!$tipId || $rating < 1 || $rating > 5) {
    exit("Invalid input.");
}

try {
    $pdo->beginTransaction();

    // insert or update vote
    $stmt = $pdo->prepare("
        INSERT INTO tip_votes (tip_id, user_id, rating)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE rating = VALUES(rating)
    ");
    $stmt->execute([$tipId, $user['id'], $rating]);

    // recalc averages
    $stmt = $pdo->prepare("
        SELECT AVG(rating) avg_rating, COUNT(*) total
        FROM tip_votes
        WHERE tip_id = ?
    ");
    $stmt->execute([$tipId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        UPDATE tips
        SET rating_avg = ?, rating_count = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $row['avg_rating'],
        $row['total'],
        $tipId
    ]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    exit("Vote failed.");
}

exit("OK");