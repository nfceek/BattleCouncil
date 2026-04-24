<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/permissions.php';

$user = getCurrentUser();

/**
 * SECURITY CHECK
 */
if (!canAskQuestion($user)) {
    http_response_code(403);
    exit("Login required.");
}

/**
 * METHOD CHECK
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Invalid request.");
}

/**
 * INPUT CLEANING
 */
$message = trim($_POST['message'] ?? '');

/**
 * VALIDATION
 */
if ($message === '') {
    exit("Message is required.");
}

/**
 * INSERT QUESTION (UNIFIED TABLE MODEL)
 */
$stmt = $pdo->prepare("
    INSERT INTO message_board (user_id, type, message)
    VALUES (?, 'question', ?)
");

$stmt->execute([
    $user['id'],
    $message
]);

/**
 * REDIRECT BACK TO TAB
 */
header("Location: /public/message_board.php?tab=questions");
exit;