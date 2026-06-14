<?php

namespace App\Services;

use App\Algorithms\ZScore;
use App\Models\Attendance;
use App\Models\Payroll;
use Carbon\Carbon;

class ZScoreService
{
    /**
     * Detect outlier durations for a single employee.
     * If $overrideData is provided it should be an array of numeric values keyed by date or index.
     * Returns array of detected outliers with index/date, value and z.
     */
    public function detectAttendanceOutliersForEmployee(int $employeeId, ?string $start = null, ?string $end = null, float $threshold = 3.0, array $overrideData = null): array
    {
        if ($overrideData !== null) {
            $values = array_values($overrideData);
            $keys = array_keys($overrideData);
        } else {
            $query = Attendance::where('employee_id', $employeeId);
            if ($start) {
                $query->where('select_date', '>=', Carbon::parse($start)->toDateString());
            }
            if ($end) {
                $query->where('select_date', '<=', Carbon::parse($end)->toDateString());
            }
            $rows = $query->orderBy('select_date')->get(['select_date', 'duration_minutes']);
            $values = $rows->map(fn($r) => is_numeric($r->duration_minutes) ? (float)$r->duration_minutes : 0.0)->toArray();
            $keys = $rows->map(fn($r) => $r->select_date)->toArray();
        }

        $out = ZScore::detectOutliers($values, $threshold);
        $mapped = [];
        foreach ($out as $i => $info) {
            $mapped[] = [
                'key' => $keys[$i] ?? $i,
                'index' => $i,
                'value' => $info['value'],
                'z' => $info['z'],
            ];
        }
        return $mapped;
    }

    /**
     * Detect outlier payroll values (e.g., total_payable) across all payrolls.
     * If $overrideData provided it should be numeric array.
     */
    public function detectSalaryOutliers(float $threshold = 3.0, array $overrideData = null): array
    {
        if ($overrideData !== null) {
            $values = array_values($overrideData);
            $keys = array_keys($overrideData);
        } else {
            $rows = Payroll::orderBy('date')->get(['id', 'total_payable']);
            $values = $rows->map(fn($r) => is_numeric($r->total_payable) ? (float)$r->total_payable : 0.0)->toArray();
            $keys = $rows->map(fn($r) => $r->id)->toArray();
        }

        $out = ZScore::detectOutliers($values, $threshold);
        $mapped = [];
        foreach ($out as $i => $info) {
            $mapped[] = [
                'key' => $keys[$i] ?? $i,
                'index' => $i,
                'value' => $info['value'],
                'z' => $info['z'],
            ];
        }
        return $mapped;
    }
}
