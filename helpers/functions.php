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
               $roles[$_SESSION['role']] >= $roles[$role];
    }
}

// ==============================
// DATABASE HELPERS
// ==============================

if (!function_exists('fetchAll')) {
    /**
     * Execute a prepared statement and fetch all results as associative array
     */
    /*
    function fetchAll(PDO $pdo, string $sql, array $params = []): array {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    */

    function fetchAll(PDO $pdo, string $sql, array $params = []): array {
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute($params);
    } catch (PDOException $e) {
        echo "<pre>";
        echo "SQL:\n$sql\n\n";
        echo "Params:\n";
        print_r($params);
        echo "</pre>";
        throw $e;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

if (!function_exists('fetchOne')) {
    /**
     * Execute a prepared statement and fetch a single row
     */
    function fetchOne(PDO $pdo, string $sql, array $params = []): ?array {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}