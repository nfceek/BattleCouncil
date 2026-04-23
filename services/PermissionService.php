<?php

class PermissionService {

    public static function has(PDO $pdo, int $userId, string $permission): bool {

        $stmt = $pdo->prepare("
            SELECT 1
            FROM users u
            JOIN role_permissions rp ON rp.role_id = u.role_id
            WHERE u.id = ?
            AND rp.permission_key = ?
            LIMIT 1
        ");

        $stmt->execute([$userId, $permission]);

        return (bool) $stmt->fetchColumn();
    }

    public static function require(PDO $pdo, int $userId, string $permission): void {
        if (!self::has($pdo, $userId, $permission)) {
            http_response_code(403);
            die("Forbidden");
        }
    }
}