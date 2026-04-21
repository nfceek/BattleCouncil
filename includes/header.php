<?php 

require_once __DIR__ . '/../config/config.php'; 

// -----------------------------
// PAGE FLAGS (safe defaults)
// -----------------------------
$requiresAuth = $requiresAuth ?? true;
$loadAppJS    = $loadAppJS ?? true;

// only load auth if needed
if ($requiresAuth) {
    require_once __DIR__ . '/../helpers/auth.php';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $pageTitle ?? APP_NAME ?></title>

<!-- CORE CSS -->
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

<!-- FONTS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- FAVICON -->
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">

<?php
// -----------------------------
// PAGE CSS (VERSIONED)
// -----------------------------
$cssMap = [
    'pricing'     => 'pricing.css',
    'map'         => 'map.css',
    'battle'      => 'battle.css',
    'clanCreate'  => 'clanCreate.css',
    'tavern'      => 'tavern.css'
];

if (!empty($pageCss) && isset($cssMap[$pageCss])) {
    $file = "/assets/css/" . $cssMap[$pageCss];
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;

    $version = file_exists($fullPath) ? filemtime($fullPath) : time();
    echo '<link rel="stylesheet" href="' . $file . '?v=' . $version . '">';
}
?>

<meta name="base-url" content="<?= BASE_URL ?>">

<!-- CORE JS -->
<script src="/assets/js/core.js"></script>

<?php if ($loadAppJS): ?>
<script src="/assets/js/app.js"></script>
<?php endif; ?>

<?php
// -----------------------------
// PAGE JS (CONTROLLED LOAD)
// -----------------------------
$jsMap = [
    'clanCreate' => ['kingdomPicker.js', 'languagePicker.js'],
    'map'        => ['map.js'],
    'tavern'     => ['tavern.js'],
];

if ($loadAppJS && !empty($pageCss) && isset($jsMap[$pageCss])) {
    foreach ($jsMap[$pageCss] as $file) {
        echo '<script src="/assets/js/' . $file . '" defer></script>';
    }
}
?>

</head>

<body class="<?= $pageClass ?? 'page-default' ?>">

<?php 
// -----------------------------
// HEADER IMAGE (no forced auth)
// -----------------------------
$headerImg = BASE_URL . "/images/site-header.png";
$headerAlt = "Battle Council";

if ($requiresAuth && function_exists('hasRole') && hasRole('admin')) {
    $headerImg = BASE_URL . "/images/site-header-admin.png";
    $headerAlt = "Admin Battle Council";
}
?>

<div class="site-header">
    <img src="<?= $headerImg ?>" alt="<?= $headerAlt ?>">
</div>

<!-- NAV -->
<nav class="navbar">
<div class="nav-inner">

<button class="menu-toggle" id="menuToggle">
    <i class="fa-solid fa-bars"></i>
</button>

<a href="<?= BASE_URL ?>/index.php" class="nav-home">
    <i class="fa-solid fa-house"></i>
</a>

<?php if ($requiresAuth && function_exists('isLoggedIn') && isLoggedIn()): ?>
<?php $username = $_SESSION['username'] ?? 'User'; ?>

<div class="title-name">
    <div class="title-user"><?= htmlspecialchars($username) ?></div>
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
    <a href="<?= BASE_URL ?>/index.php">Home</a>
    <a href="<?= BASE_URL ?>/calc_squad.php">Monster Hunt</a>
</div>

</nav>