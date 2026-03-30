<?php

class PointsService {

    public static function add(PDO $pdo, int $userId, int $points, string $reason, ?int $refId = null): bool {

        // Ledger only (NO transaction here)
        $stmt = $pdo->prepare("
            INSERT INTO points_ledger (user_id, points, reason, reference_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $points, $reason, $refId]);

        // Update balance cache
        $stmt = $pdo->prepare("
            INSERT INTO user_points (user_id, balance)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance)
        ");
        $stmt->execute([$userId, $points]);

        return true;
    }

    public static function getBalance(PDO $pdo, int $userId): int {
        $stmt = $pdo->prepare("SELECT balance FROM user_points WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) ($stmt->fetchColumn() ?? 0);
    }

    public static function getLedger(PDO $pdo, int $userId): array {
        $stmt = $pdo->prepare("
            SELECT points, reason, reference_id, created_at
            FROM points_ledger
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}