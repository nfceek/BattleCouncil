<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'App 2.0' ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>

<header class="header">
  <div class="header-inner">
    <div class="logo">⚔️ App</div>

    <button class="menu-toggle" onclick="toggleMenu()">☰</button>

    <nav id="nav" class="nav">
      <a href="/">Home</a>
      <a href="/pages/dashboard.php">Dashboard</a>
      <a href="/pages/squads.php">Squads</a>
    </nav>
  </div>
</header>