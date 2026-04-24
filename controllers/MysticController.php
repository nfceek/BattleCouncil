<?php

class MysticController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * MAIN ENTRY: returns a Mystic Lynne response
     */
    public function ask(array $payload = []): array
    {
        $question = $payload['question'] ?? '';

        $answers = $this->getAllAnswers();

        if (!$answers) {
            return [
                'answer_text' => 'The orb is silent...',
                'type' => 'neutral',
                'rarity' => 'common'
            ];
        }

        $pool = $this->buildWeightedPool($answers);

        $selected = $pool[array_rand($pool)];

        return $selected;
    }

    /**
     * FETCH ALL ANSWERS
     */
    private function getAllAnswers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM mystic_lynne_answers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * BUILD WEIGHTED RANDOM POOL
     */
    private function buildWeightedPool(array $answers): array
    {
        $pool = [];

        foreach ($answers as $a) {

            $rarityWeight = match($a['rarity']) {
                'common' => 10,
                'uncommon' => 6,
                'rare' => 3,
                'legendary' => 1,
                default => 5
            };

            $weight = $rarityWeight * (int)$a['weight'];

            for ($i = 0; $i < $weight; $i++) {
                $pool[] = $a;
            }
        }

        return $pool;
    }
}