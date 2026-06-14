# Employee Algo 3 — K-Means Clustering

Purpose
- Group employees into clusters (e.g., High, Average, Low performers) using numeric features such as attendance rate, average working hours, and salary.

How it works
- K-means partitions data into `k` clusters by minimizing within-cluster variance.
- After clustering, map clusters to labels (High/Average/Low) based on centroid metrics.

Usage (PHP)
```
use App\Algorithms\KMeans;

$km = new KMeans();
$data = [[40,8.5,2000],[55,9.5,3500],[30,7.0,1500]]; // numeric feature vectors
$result = $km->fit($data, 3);
print_r($result);
```

Notes
- Random initialization can affect results; run multiple times and choose best inertia.
- Normalize features before clustering.
