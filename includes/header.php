<?php 

require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../helpers/auth.php';

/* =========================
   SAFE FLAGS (BACKWARD COMPATIBLE)
========================= */
$requiresApp  = $requiresApp  ?? true;   // controls auth + app behavior
$pageCss      = $pageCss      ?? null;

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
   PAGE CSS
========================= */

$cssMap = [
    'pricing'     =>  'pricing.css',
    'map'         =>  'map.css',
    'battle'      =>  'battle.css',
    'clanCreate'  =>  'clanCreate.css',
    'tavern'      =>  'tavern.css',
    'msg'         =>  'msg.css'
];

if ($pageCss && isset($cssMap[$pageCss])) {
    $file = "/assets/css/" . $cssMap[$pageCss];
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;

    echo '<link rel="stylesheet" href="' . $file .
        (file_exists($fullPath) ? '?v=' . filemtime($fullPath) : '') .
        '">';
}
?>

<!-- BASE URL -->
<meta name="base-url" content="<?= BASE_URL ?>">

<?php if ($requiresApp): ?>
    <!-- CORE JS -->
    <script src="/assets/js/core.js"></script>
    <script src="/assets/js/app.js"></script>
<?php endif; ?>

<?php
/* =========================
   PAGE JS
========================= */

$jsMap = [
    'clanCreate' => ['kingdomPicker.js', 'languagePicker.js'],
    'map'        => ['map.js'],
    'tavern'     => ['tavern.js'],
];

if ($requiresApp && $pageCss && isset($jsMap[$pageCss])) {
    foreach ($jsMap[$pageCss] as $file) {
        echo '<script src="/assets/js/' . $file . '" defer></script>';
    }
}
?>

</head>

<body class="<?= $pageClass ?? 'page-default' ?>">

<?php 
/* =========================
   AUTH (SAFE)
========================= */

$userLoggedIn = false;

if ($requiresApp) {
    $userLoggedIn = requireLogin();
}

/* =========================
   HEADER IMAGE
========================= */

$headerImg = BASE_URL . "/images/site-header.png";
$headerAlt = "Battle Council";

if ($requiresApp && function_exists('hasRole') && hasRole('admin')) {
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

<?php if ($requiresApp && isLoggedIn()): ?>
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
    <a href="#">👹 Talking Heads</a>
    <a href="#">🪖 Calculators</a>
    <a href="#">🐲 World Maps</a>
    <a href="#">👥 Members</a>

    <hr>

    <a href="<?= BASE_URL ?>/public/login.php">
        <i class="fa-solid fa-right-to-bracket"></i> Login
    </a>
    <a href="<?= BASE_URL ?>/register.php">
        <i class="fa-solid fa-user-plus"></i> Register
    </a>
</div>

</nav>

<?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
<div class="bc-container">
    <div class="bc-row">
        <div class="bc-col-12">

            <div class="bc-breadcrumb">
                <a href="<?= BASE_URL ?>/index.php">Home</a>

                <?php foreach ($breadcrumbs as $label => $link): ?>
                    <span class="bc-breadcrumb-sep">›</span>

                    <?php if ($link): ?>
                        <a href="<?= $link ?>"><?= htmlspecialchars($label) ?></a>
                    <?php else: ?>
                        <span class="bc-breadcrumb-current"><?= htmlspecialchars($label) ?></span>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>