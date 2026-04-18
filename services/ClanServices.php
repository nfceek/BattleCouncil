<?php
require_once __DIR__ . '/PointsService.php';

class ClanServices {

    public static function submit(PDO $pdo, int $userId, array $data): array {

        try {
            $pdo->beginTransaction();

            // Normalize inputs
            $kingdomNumber = (int)$data['kingdom'];
            $clanName = trim($data['clan_name']);
            $abbr = strtoupper(substr(trim($data['abbr']), 0, 3));
            $x = (int)$data['x'];
            $y = (int)$data['y'];

            // Basic validation
            if (!$kingdomNumber || !$clanName || strlen($abbr) !== 3) {
                throw new Exception("Invalid input");
            }

            // Ensure kingdom exists
            $stmt = $pdo->prepare("
                INSERT INTO kingdoms (kingdomID)
                VALUES (?)
                ON DUPLICATE KEY UPDATE id=id
            ");
            $stmt->execute([$kingdomNumber]);

            $stmt = $pdo->prepare("SELECT id FROM kingdoms WHERE kingdomID = ?");
            $stmt->execute([$kingdomNumber]);
            $kingdomId = (int)$stmt->fetchColumn();

            // Insert clan
            $stmt = $pdo->prepare("
                INSERT INTO kingdom_clans (
                    kingdom_id, clan_name, clan_abbr,
                    capital_x, capital_y,
                    roe, follows_roe,
                    member_count, clan_leader,
                    default_language, clan_size,
                    created_by
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $kingdomId,
                $clanName,
                $abbr,
                $x,
                $y,
                $data['roe'] ?? null,
                $data['follows_roe'] ?? null,
                $data['members'] ?? null,
                $data['leader'] ?? null,
                $data['language'] ?? null,
                $data['size'] ?? null,
                $userId
            ]);

            $clanId = $pdo->lastInsertId();

            // Award points (inside SAME transaction)
            PointsService::add(
                $pdo,
                $userId,
                25,
                'clan_submission',
                $clanId
            );

            $pdo->commit();

            return [
                'success' => true,
                'points_awarded' => 25
            ];

        } catch (PDOException $e) {

            $pdo->rollBack();

            if ($e->errorInfo[1] === 1062) {
                return [
                    'success' => false,
                    'error' => 'Capital already exists → no points awarded'
                ];
            }

            return [
                'success' => false,
                'error' => 'Database error'
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}