<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;

class BaseMetricsService
{
    /**
     * Return aggregated employee metrics in given date range.
     * Keys returned:
     * - employee_id, name
     * - attendance_count, days_count
     * - total_duration_minutes, total_duration_hours
     * - avg_daily_minutes, avg_daily_hours
     * - total_overtime_hours
     * - avg_payable (if payrolls present)
     */
    public function getEmployeeMetrics(int $employeeId, ?string $start = null, ?string $end = null): array
    {
        $employee = Employee::find($employeeId);
        if (! $employee) {
            return ['error' => 'employee_not_found', 'employee_id' => $employeeId];
        }

        $query = Attendance::where('employee_id', $employeeId);
        if ($start) {
            $query->where('select_date', '>=', Carbon::parse($start)->toDateString());
        }
        if ($end) {
            $query->where('select_date', '<=', Carbon::parse($end)->toDateString());
        }

        $rows = $query->get(['select_date', 'duration_minutes', 'overtime']);

        $attendanceCount = $rows->count();
        $totalDurationMinutes = 0.0;
        $totalOvertimeHours = 0.0;
        $days = [];

        foreach ($rows as $r) {
            $days[] = $r->select_date;
            $minutes = is_numeric($r->duration_minutes) ? (float)$r->duration_minutes : 0.0;
            $totalDurationMinutes += $minutes;
            if ($r->overtime) {
                $totalOvertimeHours += $this->parseOvertimeToHours($r->overtime);
            }
        }

        $totalDurationHours = $totalDurationMinutes / 60.0;
        $daysCount = count(array_unique($days));
        $avgDailyMinutes = $daysCount > 0 ? ($totalDurationMinutes / $daysCount) : 0.0;
        $avgDailyHours = $avgDailyMinutes / 60.0;

        // payrolls
        $pquery = Payroll::where('employee_id', $employeeId);
        if ($start) {
            $pquery->where('date', '>=', Carbon::parse($start)->toDateString());
        }
        if ($end) {
            $pquery->where('date', '<=', Carbon::parse($end)->toDateString());
        }
        $payrolls = $pquery->get(['total_payable']);
        $avgPayable = null;
        if ($payrolls->count() > 0) {
            $sum = $payrolls->sum(fn($p) => is_numeric($p->total_payable) ? (float)$p->total_payable : 0.0);
            $avgPayable = $sum / $payrolls->count();
        }

        return [
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'attendance_count' => $attendanceCount,
            'days_count' => $daysCount,
            'total_duration_minutes' => round($totalDurationMinutes, 2),
            'total_duration_hours' => round($totalDurationHours, 2),
            'avg_daily_minutes' => round($avgDailyMinutes, 2),
            'avg_daily_hours' => round($avgDailyHours, 2),
            'total_overtime_hours' => round($totalOvertimeHours, 2),
            'avg_payable' => $avgPayable === null ? null : round($avgPayable, 2),
        ];
    }

    protected function parseOvertimeToHours(?string $overtime): float
    {
        if (! $overtime) {
            return 0.0;
        }
        // Expect formats like HH:MM:SS or H:MM
        $parts = explode(':', $overtime);
        if (count($parts) === 3) {
            $h = (float)$parts[0];
            $m = (float)$parts[1];
            $s = (float)$parts[2];
            return $h + ($m / 60.0) + ($s / 3600.0);
        }
        if (count($parts) === 2) {
            $h = (float)$parts[0];
            $m = (float)$parts[1];
            return $h + ($m / 60.0);
        }
        // fallback: numeric string representing minutes or hours
        $val = floatval($overtime);
        // if large, treat as minutes
        if ($val > 24) {
            return $val / 60.0;
        }
        return $val;
    }
}
