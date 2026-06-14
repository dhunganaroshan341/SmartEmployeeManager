<?php

namespace App\Algorithms;

class ZScore
{
    public static function compute(array $values): array
    {
        $n = count($values);
        if ($n === 0) {
            return [];
        }
        $mean = array_sum($values) / $n;
        $sq = 0.0;
        foreach ($values as $v) {
            $sq += ($v - $mean) * ($v - $mean);
        }
        $std = sqrt($sq / $n);
        if ($std == 0.0) {
            return array_fill(0, $n, 0.0);
        }
        $zs = [];
        foreach ($values as $v) {
            $zs[] = ($v - $mean) / $std;
        }
        return $zs;
    }

    public static function detectOutliers(array $values, float $threshold = 3.0): array
    {
        $zs = self::compute($values);
        $out = [];
        foreach ($zs as $i => $z) {
            if (is_finite($z) && abs($z) >= $threshold) {
                $out[$i] = ['value' => $values[$i], 'z' => $z];
            }
        }
        return $out;
    }
}
