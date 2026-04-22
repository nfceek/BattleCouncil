<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';

if (!isLoggedIn()) {
    header("Location: /public/login.php");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO message_board (user_id, title, message, tag)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $_SESSION['user_id'],
    $_POST['title'] ?? null,
    $_POST['message'],
    $_POST['tag'] ?? null
]);

header("Location: /public/message_board.php");
exit;