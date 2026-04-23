<?php
/**
 * ==================================================
 * PERMISSIONS HELPER (DROP-IN)
 * ==================================================
 * Connects:
 *  users.user_level_id → role_permissions
 *
 * Usage:
 *   require_once 'helpers/permissions.php';
 *
 *   $user = getCurrentUser();
 *
 *   if (canPostTip($user)) { ... }
 *   if (hasPermission($user, 'edit_map')) { ... }
 */

/**
 * --------------------------------------------------
 * NORMALIZE CURRENT USER
 * --------------------------------------------------
 */
function getCurrentUser(): array {
    return [
        'id' => $_SESSION['user_id'] ?? $_SESSION['id'] ?? null
    ];
}

/**
 * --------------------------------------------------
 * LOAD USER PERMISSIONS (CACHED PER REQUEST)
 * --------------------------------------------------
 */
function getUserPermissions(PDO $pdo, int $userId): array {
    static $cache = [];

    if (isset($cache[$userId])) {
        return $cache[$userId];
    }

    $stmt = $pdo->prepare("
        SELECT rp.permission_key
        FROM users u
        JOIN role_permissions rp 
            ON rp.role_id = u.user_level_id
        WHERE u.id = ?
    ");

    $stmt->execute([$userId]);

    $perms = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $cache[$userId] = $perms ?: [];

    return $cache[$userId];
}

/**
 * --------------------------------------------------
 * CORE PERMISSION CHECK
 * --------------------------------------------------
 */
function hasPermission(array $user, string $permission): bool {
    global $pdo;

    $userId = $user['id'] ?? null;
    if (!$userId) return false;

    $perms = getUserPermissions($pdo, (int)$userId);

    return in_array($permission, $perms);
}

/**
 * ==================================================
 * FEATURE HELPERS (YOUR RULES)
 * ==================================================
 */

/**
 * Tips
 */
function canPostTip(array $user): bool {
    return hasPermission($user, 'post_tip');
}

/**
 * Questions (logged in only)
 */
function canAskQuestion(array $user): bool {
    return !empty($user['id']);
}

/**
 * Replies / Answers
 */
function canReply(array $user): bool {
    return hasPermission($user, 'reply_message');
}

/**
 * Moderation (superior+)
 */
function canModerate(array $user): bool {
    return hasPermission($user, 'moderate_board');
}

/**
 * Optional: voting (if you want to gate later)
 */
function canVote(array $user): bool {
    return !empty($user['id']);
}