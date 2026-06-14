<?php

namespace App\Algorithms;

class KMeans
{
    protected array $centroids = [];

    protected function distance(array $a, array $b): float
    {
        $sum = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $d = ($a[$i] - $b[$i]);
            $sum += $d * $d;
        }
        return sqrt($sum);
    }

    public function fit(array $data, int $k, int $maxIter = 100): array
    {
        $n = count($data);
        if ($n === 0) {
            return ['centroids' => [], 'labels' => []];
        }

        // initialize centroids as first k points (simple deterministic init)
        $this->centroids = array_slice($data, 0, $k);

        $labels = array_fill(0, $n, -1);

        for ($iter = 0; $iter < $maxIter; $iter++) {
            $changed = false;

            // assign
            for ($i = 0; $i < $n; $i++) {
                $best = 0;
                $bestDist = $this->distance($data[$i], $this->centroids[0]);
                for ($c = 1; $c < $k; $c++) {
                    $d = $this->distance($data[$i], $this->centroids[$c]);
                    if ($d < $bestDist) {
                        $bestDist = $d;
                        $best = $c;
                    }
                }
                if ($labels[$i] !== $best) {
                    $changed = true;
                    $labels[$i] = $best;
                }
            }

            if (! $changed) {
                break;
            }

            // update centroids
            $sums = array_fill(0, $k, null);
            $counts = array_fill(0, $k, 0);
            foreach ($labels as $i => $c) {
                if ($sums[$c] === null) {
                    $sums[$c] = array_fill(0, count($data[$i]), 0.0);
                }
                foreach ($data[$i] as $j => $v) {
                    $sums[$c][$j] += $v;
                }
                $counts[$c]++;
            }
            for ($c = 0; $c < $k; $c++) {
                if ($counts[$c] === 0) {
                    continue;
                }
                foreach ($sums[$c] as $j => $total) {
                    $this->centroids[$c][$j] = $total / $counts[$c];
                }
            }
        }

        return ['centroids' => $this->centroids, 'labels' => $labels];
    }
}
