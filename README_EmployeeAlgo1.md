# Employee Algo 1 — Z-Score Outlier Detection

Purpose
- Use Z-score to identify unusual values in employee attendance, salary, or working hours.

How it works
- Compute mean and standard deviation for a numeric series.
- Z = (x - mean) / std. Values with |Z| >= threshold (default 3.0) are flagged as outliers.

Usage (PHP)
```
use App\Algorithms\ZScore;

$values = [8, 9, 7.5, 100, 8.5]; // e.g., daily working hours
$outliers = ZScore::detectOutliers($values, 3.0);
print_r($outliers);
```

Notes
- Good for identifying extreme single-value anomalies. For time-series or seasonality, consider contextual methods.
