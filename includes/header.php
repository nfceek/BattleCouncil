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

<!-- future login directed menu
<?php //if (isLoggedIn()): ?>
    <a href="/member_dashboard.php">Dashboard</a>
    <a href="/logout.php">Logout</a>
<?php //else: ?>
    <a href="/login.php">Login</a>
    <a href="/register.php">Register</a>
<?php //endif; ?>
-->
<!-- HEADER IMAGE -->
<?php 
$userLoggedIn = requireLogin();
/*print_r($user);

  if(hasRole('admin')){
    echo '<div class="site-header">';
    echo '<img src="<?= BASE_URL ?>/images/site-header-admin.png" alt="Admin Battle Council">';
    echo '</div>';
  }else{
    echo '<div class="site-header">';
    echo '<img src="<?= BASE_URL ?>/images/site-header.png" alt="Battle Council">';
    echo '</div>'; 
  }
    */
?>

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
    <a href="<?= BASE_URL ?>/index.php" class="nav-home">
      <i class="fa-solid fa-house"></i>
    </a>

    <!-- RIGHT: LOGIN / USER 
    <?php //if (isLoggedIn()): ?>
        <a href="#" class="nav-login">
  
          <a href="/member_dashboard.php" class="nav-login">
        
            <i class="fa-solid fa-user"></i>
            <? //e($_SESSION['username'] ?? 'User') ?>
        </a>
    <?php //else: ?>
        <a href="/login.php" class="nav-login">
            <i class="fa-solid fa-user"></i>
            Login
        </a>
    <?php //endif; ?>
-->
  </div>

  <!-- MOBILE MENU -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-house"></i> Home</a>
    
    <a href="<?= BASE_URL ?>/calc_squad.php">⚔️ Monster Hunt</a>
    <a href="#">👹 Monster Editor</a>
    <a href="#">🪖 Squad Editor</a>
    <a href="#">🐲 Matrix Data</a>
    <a href="#">👥 Members</a>
    <a href="#">Log out</a>

    <hr>

    <!-- AUTH -->
    <a href="<?= BASE_URL ?>/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
    <a href="<?= BASE_URL ?>/register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
  </div>
</nav>