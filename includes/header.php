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

  <link rel="stylesheet" href="<?= BASE_URL ?>/../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="icon" href="data:image/svg+xml,<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">
</head>

<body class="<?= $pageClass ?? 'page-default' ?>">


  <!-- HEADER IMAGE -->
  <?php 
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

  <!-- NAVBAR BAND -->
  <nav class="navbar">
    <div class="nav-inner">

      <!-- LEFT: HAMBURGER -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
        <i class="fa-solid fa-bars"></i>
      </button>        

      <!-- CENTER: HOME -->
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
  </div>

  <?php else: ?>

      <a href="/public/login.php">Login</a>

  <?php endif; ?>

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