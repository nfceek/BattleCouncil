<?php
/**
 * ==================================================
 * PERMISSIONS HELPER (CLEAN DROP-IN)
 * ==================================================
 * Uses:
 *   users.role_id → role_permissions.role_id
 *
 * Requires:
 *   global $pdo from config.php
 */

/**
 * --------------------------------------------------
 * CURRENT USER (SESSION SAFE)
 * --------------------------------------------------
 */
function getCurrentUser(): array {
    return [
        'id'      => $_SESSION['user_id'] ?? $_SESSION['id'] ?? null,
        'role_id' => $_SESSION['role_id'] ?? null
    ];
}

/**
 * --------------------------------------------------
 * LOGIN CHECK
 * --------------------------------------------------
 */
function isUserLoggedIn(array $user): bool {
    return !empty($user['id']);
}

/**
 * --------------------------------------------------
 * LOAD PERMISSIONS (CACHED PER REQUEST)
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
            ON rp.role_id = u.role_id
        WHERE u.id = ?
    ");

    $stmt->execute([$userId]);

    $cache[$userId] = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    return $cache[$userId];
}

/**
 * --------------------------------------------------
 * CORE PERMISSION CHECK
 * --------------------------------------------------
 */
function hasPermission(array $user, string $permission): bool {
    global $pdo;

    $userId  = $user['id'] ?? null;
    $roleId  = $user['role_id'] ?? null;

    if (!$userId) {
        return false;
    }

    /**
     * 🔥 ADMIN OVERRIDE (role_id = 1)
     */
    if ((int)$roleId === 1) {
        return true;
    }

    $perms = getUserPermissions($pdo, (int)$userId);

    return in_array($permission, $perms, true);
}

/**
 * ==================================================
 * FEATURE PERMISSION WRAPPERS
 * ==================================================
 */

/* ---------------- MESSAGE BOARD ---------------- */

function canPostTip(array $user): bool {
    return hasPermission($user, 'post_tip');
}

function canAskQuestion(array $user): bool {
    return isUserLoggedIn($user);
}

function canReply(array $user): bool {
    return hasPermission($user, 'reply_message');
}

function canModerate(array $user): bool {
    return hasPermission($user, 'moderate_board');
}

function canVote(array $user): bool {
    return isUserLoggedIn($user);
}

/* ---------------- MAP ---------------- */

function canEditMap(array $user): bool {
    return hasPermission($user, 'edit_map');
}

/* ---------------- TAVERN ---------------- */

function canControlTavern(array $user): bool {
    return hasPermission($user, 'control_tavern');
}

/* ---------------- ATTACK PLANS ---------------- */

function canSaveAttackPlan(array $user): bool {
    return hasPermission($user, 'save_attack_plan');
}

function canShareAttackPlan(array $user): bool {
    return hasPermission($user, 'share_attack_plan');
}