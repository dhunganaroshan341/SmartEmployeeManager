<?php

namespace Tests\Unit;

use App\Services\ZScoreService;
use PHPUnit\Framework\TestCase;

class ZScoreServiceTest extends TestCase
{
    public function test_detect_attendance_outliers_with_override()
    {
        $service = new ZScoreService();
        // create an array where one value is an extreme outlier
        $data = [
            '2026-06-01' => 480, // 8 hours
            '2026-06-02' => 450,
            '2026-06-03' => 500,
            '2026-06-04' => 60,  // 1 hour -> likely an outlier
            '2026-06-05' => 470,
        ];

        $out = $service->detectAttendanceOutliersForEmployee(1, null, null, 2.5, $data);
        $this->assertNotEmpty($out);
        // Ensure the 1-hour entry (index 3) is present
        $foundKeys = array_column($out, 'key');
        $this->assertContains('2026-06-04', $foundKeys);
    }
}
