<?php

namespace Tests\Unit;

use App\Algorithms\ZScore;
use PHPUnit\Framework\TestCase;

class ZScoreTest extends TestCase
{
    public function test_compute_and_detect_outliers()
    {
        $values = [8, 9, 7.5, 100, 8.5];
        $zs = ZScore::compute($values);
        $this->assertCount(5, $zs);

        $out = ZScore::detectOutliers($values, 3.0);
        $this->assertNotEmpty($out);
        $this->assertArrayHasKey(3, $out); // the value 100 should be flagged
        $this->assertEquals(100, $out[3]['value']);
    }
}
