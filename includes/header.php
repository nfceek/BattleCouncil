<?php 

require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../helpers/auth.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $pageTitle ?? APP_NAME ?></title>

<!-- CORE CSS -->
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

<!-- FONTS / ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- FAVICON -->
<link rel="icon" href="data:image/svg+xml,<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">

<?php
/* =========================
   PAGE CSS (WITH VERSIONING)
========================= */

$cssMap = [
    'pricing'     => 'pricing.css',
    'map'         => 'map.css',
    'battle'      => 'battle.css',
    'clanCreate'  => 'clanCreate.css',
];

if (!empty($pageCss) && isset($cssMap[$pageCss])) {
    $file = "/assets/css/" . $cssMap[$pageCss];
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;

    if (file_exists($fullPath)) {
        echo '<link rel="stylesheet" href="' . $file . '?v=' . filemtime($fullPath) . '">';
    } else {
        echo '<link rel="stylesheet" href="' . $file . '">';
    }
}
?>

<!-- BASE URL FOR JS -->
<meta name="base-url" content="<?= BASE_URL ?>">

<!-- CORE JS (always loaded first) -->
<script src="/assets/js/core.js"></script>
<script src="/assets/js/app.js"></script>

<?php
/* =========================
   PAGE JS (CONTROLLED LOAD)
========================= */

$jsMap = [
    'clanCreate' => ['kingdomPicker.js', 'languagePicker.js'],
    'map'        => ['map.js'], // 🔥 unified map engine
];

if (!empty($pageCss) && isset($jsMap[$pageCss])) {
    foreach ($jsMap[$pageCss] as $file) {
        echo '<script src="/assets/js/' . $file . '" defer></script>';
    }
}
?>

</head>

<body class="<?= $pageClass ?? 'page-default' ?>">

<?php 
/* =========================
   HEADER IMAGE
========================= */

$userLoggedIn = requireLogin();

$headerImg = BASE_URL . "/images/site-header.png";
$headerAlt = "Battle Council";

if (hasRole('admin')) {
    $headerImg = BASE_URL . "/images/site-header-admin.png";
    $headerAlt = "Admin Battle Council";
}
?>

<div class="site-header">
    <img src="<?= $headerImg ?>" alt="<?= $headerAlt ?>">
</div>

<!-- NAVBAR -->
<nav class="navbar">
<div class="nav-inner">

<!-- LEFT -->
<button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
    <i class="fa-solid fa-bars"></i>
</button>        

<!-- CENTER -->
<a href="<?= BASE_URL ?>/index.php" class="nav-home">
    <i class="fa-solid fa-house"></i>
</a>

<?php if (isLoggedIn()): ?>
<?php $username = $_SESSION['username'] ?? 'User'; ?>

<div class="title-name">
    <div class="title-user">
        <?= htmlspecialchars($username) ?>
    </div>

    <div class="title-logout">
        <a href="/public/logout.php">( Logout )</a>
    </div>
</div>

<?php else: ?>

<a href="/public/login.php">Login</a>

<?php endif; ?>

</div>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-house"></i> Home</a>

    <a href="<?= BASE_URL ?>/calc_squad.php">⚔️ Monster Hunt</a>
    <a href="#">👹 Monster Editor</a>
    <a href="#">🪖 Squad Editor</a>
    <a href="#">🐲 Matrix Data</a>
    <a href="#">👥 Members</a>

    <hr>

    <a href="<?= BASE_URL ?>/public/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
    <a href="<?= BASE_URL ?>/register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
</div>

</nav>