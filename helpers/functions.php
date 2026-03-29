<?php
// ==============================
// GENERAL HELPERS
// ==============================

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

/*
if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isLoggedIn()) {
            header("Location: " . BASE_URL . "/login.php");
            exit;
        }
    }
}
*/

if (!function_exists('hasRole')) {
    function hasRole($role) {
        $roles = ['officer'=>1,'veteran'=>2,'superior'=>3,'admin'=>4];
        return isset($_SESSION['role']) &&
               isset($roles[$_SESSION['role']]) &&
               $roles[$_SESSION['role']] >= $roles[$role];
    }
}

// ==============================
// DATABASE HELPERS
// ==============================

if (!function_exists('fetchAll')) {

    function fetchAll(PDO $pdo, string $sql, array $params = []): array {
        $stmt = $pdo->prepare($sql);

        preg_match_all('/\?/', $sql, $matches);
        $expected = count($matches[0]);
        $actual = count($params);

        if ($expected !== $actual) {
            echo "<pre style='color:red'>";
            echo "PARAM MISMATCH\n";
            echo "Expected: $expected\n";
            echo "Actual: $actual\n\n";
            echo "SQL:\n$sql\n\n";
            print_r($params);
            echo "</pre>";
            exit;
        }

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


if (!function_exists('fetchOne')) {

    function fetchOne(PDO $pdo, string $sql, array $params = []): ?array {

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

// ==============================
// DISPLAY HELPERS
// ==============================

if (!function_exists('shortNum')) {
    function shortNum($n) {
        if (!is_numeric($n)) return $n;

        if ($n >= 1000000000) return round($n / 1000000000, 2) . 'B';
        if ($n >= 1000000)    return round($n / 1000000, 2) . 'M';
        if ($n >= 1000)       return round($n / 1000, 2) . 'K';

        return number_format($n);
    }
}

function bonusDot($val) {
    if ($val <= 0) return 'low';
    if ($val < 25) return 'mid';
    if ($val < 75) return 'high';
    return 'max';
}

function resolveSquadImage($squadStats) {

    $base   = strtolower($squadStats['image_base'] ?? 'default');
    $rarity = strtolower($squadStats['rarity'] ?? 'common');
    $level  = (int)($squadStats['level'] ?? 3);

    // filesystem base (REAL path)
    $fsBase = $_SERVER['DOCUMENT_ROOT'] . '/battlecouncil';

    // browser path (what <img> uses)
    $webBase = '/battlecouncil';

    $paths = [
        "/images/monsters/{$base}_{$rarity}_{$level}.png",
        "/images/monsters/{$base}_{$rarity}.png",
        "/images/monsters/{$base}.png"
    ];

    foreach ($paths as $path) {
        if (file_exists($fsBase . $path)) {
            return $webBase . $path;
        }
    }

    return $webBase . "/images/monsters/default.png";
}
