# Employee Algo 2 — K-Nearest Neighbors (KNN)

Purpose
- Classify a new employee record (e.g., performance class) by comparing it to historical records.

How it works
- Compute distance (Euclidean) between the new record's feature vector and every labeled record.
- Select the `k` nearest neighbors and take the majority label as the prediction.

Usage (PHP)
```
use App\Algorithms\KNN;

$knn = new KNN();
$dataset = [
    ['features'=>[40,8.5,2000], 'label'=>'Average'],
    ['features'=>[55,9.5,3500], 'label'=>'High'],
    // ...
];
$knn->fit($dataset);
$prediction = $knn->predict([50,9.0,3000], 3);
echo $prediction;
```

Notes
- Normalize features when scales differ (hours vs salary). Use Z-score or min-max scaling before `fit`/`predict`.
