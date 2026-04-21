<?php 

require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../helpers/auth.php';

/* =========================
   PAGE FLAGS (DEFAULTS)
========================= */
$requiresApp = $requiresApp ?? true;   // 🔥 controls app.js + DB usage
$pageCss     = $pageCss ?? null;
$pageJs      = $pageJs ?? [];          // array support

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
    'pricing'     => 'pricing.css',
    'map'         => 'map.css',
    'battle'      => 'battle.css',
    'clanCreate'  => 'clanCreate.css',
    'tavern'      => 'tavern.css'
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
    <!-- CORE JS (ONLY WHEN NEEDED) -->
    <script src="/assets/js/core.js"></script>
    <script src="/assets/js/app.js"></script>
<?php endif; ?>

<?php
/* =========================
   PAGE JS (EXPLICIT CONTROL)
========================= */

if (!empty($pageJs)) {
    foreach ($pageJs as $file) {
        echo '<script src="/assets/js/' . $file . '" defer></script>';
    }
}
?>

</head>

<body class="<?= $pageClass ?? 'page-default' ?>">