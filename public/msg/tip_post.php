<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/permissions.php';

$user = getCurrentUser();

if (!canPostTip($user)) {
    http_response_code(403);
    exit("Not authorized to post tips.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request.");
}

$title   = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$tag     = trim($_POST['tag'] ?? '');

if (!$message) {
    exit("Message required.");
}

$stmt = $pdo->prepare("
    INSERT INTO tips (user_id, title, message, tag)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $user['id'],
    $title ?: null,
    $message,
    $tag ?: null
]);

header("Location: /public/message_board.php?tab=tips");
exit;