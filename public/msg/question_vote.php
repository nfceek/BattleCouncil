<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/permissions.php';

$user = getCurrentUser();

if (!canVote($user)) {
    http_response_code(403);
    exit("Login required.");
}

$questionId = (int)($_POST['question_id'] ?? 0);

if (!$questionId) {
    exit("Invalid input.");
}

try {
    $pdo->beginTransaction();

    // prevent duplicate
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO question_votes (question_id, user_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$questionId, $user['id']]);

    // update count
    $stmt = $pdo->prepare("
        UPDATE questions
        SET votes = (
            SELECT COUNT(*) FROM question_votes WHERE question_id = ?
        )
        WHERE id = ?
    ");
    $stmt->execute([$questionId, $questionId]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    exit("Vote failed.");
}

exit("OK");