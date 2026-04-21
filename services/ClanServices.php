<?php
require_once __DIR__ . '/PointsService.php';

class ClanServices {

    public static function submit(PDO $pdo, int $userId, array $data): array {

        try {
            $pdo->beginTransaction();

            // Normalize inputs
            $kingdomNumber = (int)$data['k'];
            $clanName = trim($data['clan_name']);
            $abbr = strtoupper(substr(trim($data['clan_abbr']), 0, 3));
            $x = (int)$data['x'];
            $y = (int)$data['y'];
            $leader = trim($data['leader']);
            $lvl = (int)$data['level'];
            $submitter = 'system';  // TODO: hook into security and pull userID for this 

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
                INSERT INTO clans (
                    k,x,y,
                    `name`, shortname,
                    kingdom, leader, lvl,
                    language, roe,
                    submitter
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $kingdomNumber,
                $x,
                $y,
                $clanName,
                $abbr,
                $kingdomNumber,
                $leader,
                $lvl,
                $data['language'] ?? null,
                $data['roe'] ?? null,
                $submitter 
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