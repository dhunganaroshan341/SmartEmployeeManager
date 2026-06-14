<?php

namespace App\Algorithms;

class KNN
{
    protected array $data = [];

    public function fit(array $data): void
    {
        $this->data = $data;
    }

    protected function euclidean(array $a, array $b): float
    {
        $sum = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $d = ($a[$i] - $b[$i]);
            $sum += $d * $d;
        }
        return sqrt($sum);
    }

    public function predict(array $features, int $k = 3)
    {
        $distances = [];
        foreach ($this->data as $i => $row) {
            $d = $this->euclidean($features, $row['features']);
            $distances[] = ['index' => $i, 'dist' => $d, 'label' => $row['label'] ?? null];
        }

        usort($distances, fn($a, $b) => $a['dist'] <=> $b['dist']);
        $neighbors = array_slice($distances, 0, max(1, $k));

        $counts = [];
        foreach ($neighbors as $n) {
            $lbl = $n['label'] ?? 'unknown';
            $counts[$lbl] = ($counts[$lbl] ?? 0) + 1;
        }

        arsort($counts);
        return array_key_first($counts);
    }
}
