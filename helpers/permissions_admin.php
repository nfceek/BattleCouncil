<?php

require_once __DIR__ . '/../config/config.php';

/**
 * GET ALL ROLES
 */
function getRoles(PDO $pdo): array {
    return $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * GET ALL PERMISSIONS
 */
function getPermissions(PDO $pdo): array {
    return $pdo->query("SELECT DISTINCT permission_key FROM role_permissions ORDER BY permission_key")
        ->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * GET ROLE PERMISSIONS
 */
function getRolePermissions(PDO $pdo, int $roleId): array {
    $stmt = $pdo->prepare("SELECT permission_key FROM role_permissions WHERE role_id = ?");
    $stmt->execute([$roleId]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * ASSIGN PERMISSION
 */
function addPermissionToRole(PDO $pdo, int $roleId, string $key): void {
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO role_permissions (role_id, permission_key)
        VALUES (?, ?)
    ");
    $stmt->execute([$roleId, $key]);
}

/**
 * REMOVE PERMISSION
 */
function removePermissionFromRole(PDO $pdo, int $roleId, string $key): void {
    $stmt = $pdo->prepare("
        DELETE FROM role_permissions
        WHERE role_id = ? AND permission_key = ?
    ");
    $stmt->execute([$roleId, $key]);
}