<?php

class MysticController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * MAIN ENTRY: Mystic Lynne oracle response
     */
    public function ask(array $payload = []): array
    {
        $question = trim($payload['question'] ?? '');

        $answers = $this->getAllAnswers();

        if (empty($answers)) {
            return $this->formatAnswer([
                'answer_text' => 'The orb is silent...',
                'type' => 'neutral',
                'rarity' => 'common'
            ]);
        }

        $pool = $this->buildWeightedPool($answers);

        if (empty($pool)) {
            return $this->formatAnswer([
                'answer_text' => 'The mist refuses to form an answer...',
                'type' => 'neutral',
                'rarity' => 'common'
            ]);
        }

        // Optional future hook: question influence
        $selected = $pool[array_rand($pool)];

        return $this->formatAnswer($selected);
    }

    /**
     * FETCH ALL ANSWERS
     */
    private function getAllAnswers(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM mystic_lynne_answers");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * BUILD WEIGHTED POOL
     */
    private function buildWeightedPool(array $answers): array
    {
        $pool = [];

        foreach ($answers as $a) {

            $rarity = $a['rarity'] ?? 'common';
            $baseWeight = isset($a['weight']) ? (int)$a['weight'] : 1;

            $rarityWeight = match($rarity) {
                'common' => 10,
                'uncommon' => 6,
                'rare' => 3,
                'legendary' => 1,
                default => 5
            };

            $weight = max(1, $rarityWeight * max(1, $baseWeight));

            for ($i = 0; $i < $weight; $i++) {
                $pool[] = $a;
            }
        }

        return $pool;
    }

    /**
     * NORMALIZE OUTPUT (CRITICAL FOR JS STABILITY)
     */
    private function formatAnswer(array $row): array
    {
        return [
            'answer_text' => $row['answer_text'] ?? 'The oracle is unclear...',
            'type'        => $row['type'] ?? 'neutral',
            'rarity'      => $row['rarity'] ?? 'common'
        ];
    }
}