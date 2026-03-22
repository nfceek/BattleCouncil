<?php require_once __DIR__ . '/../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $pageTitle ?? APP_NAME ?></title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">
</head>

<body>

<!-- HEADER IMAGE -->
<div class="site-header">
  <img src="<?= BASE_URL ?>/images/site-header.png" alt="Battle Council">
</div>

<!-- NAVBAR BAND -->
<nav class="navbar">
  <div class="nav-inner">

    <!-- LEFT: HAMBURGER -->
    <button class="menu-toggle" id="menuToggle">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- CENTER: HOME -->
    <a href="/index.php" class="nav-home">
      <i class="fa-solid fa-house"></i>
    </a>

    <!-- RIGHT: LOGIN -->
    <a href="/login.php" class="nav-login">
      <i class="fa-solid fa-user"></i>
    </a>

  </div>

  <!-- MOBILE MENU -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="/index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="/monster_hunt.php">⚔️ Monster Hunt</a>
    <a href="/monster_editor.php">👹 Monster Editor</a>
    <a href="/squad_editor.php">🪖 Squad Editor</a>
    <a href="/matrix_data.php">🐲 Matrix Data</a>
    <a href="/member_dashboard.php">👥 Members</a>

    <hr>

    <!-- AUTH -->
    <a href="/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
    <a href="/register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
  </div>
</nav>