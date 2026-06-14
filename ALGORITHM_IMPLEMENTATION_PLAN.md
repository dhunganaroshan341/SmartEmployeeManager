# Algorithm Implementation Plan

## Goal
- Implement three data-driven utilities for employee analytics:
  - Z-Score outlier detection (attendance, salary, working hours)
  - K-Nearest Neighbors (KNN) for classifying new employee records
  - K-Means clustering to segment employees into performance groups

## Scope & Prioritization
- Phase 1 (Immediate): Z-Score utility + unit tests and integration example.
- Phase 2: K-Means clustering for grouping (exploratory analysis + cluster labeling).
- Phase 3: KNN classifier for assigning class labels to new records.
- Phase 4 (Optional): Dashboarding, scheduled batch jobs, retraining, monitoring.

## Data & Features
- Candidate features: attendance rate (%), average daily working hours, monthly salary, tasks completed, punctuality score.
- Requirements: consistent numeric encoding, no categorical text in raw features.

## Preprocessing
- Handle missing values: impute or drop; record counts for audit.
- Normalize numeric features: Z-score or Min-Max before KNN/KMeans.
- Optionally engineer features: rolling averages, attendance streaks, overtime ratios.

## Algorithm Implementation Details
- Z-Score
  - Compute mean & std; return array of z-scores and indices exceeding threshold (default |z|>=3).
  - Support per-employee time-windowed scans (day/week/month) later.
- K-Means
  - Input: matrix of normalized feature vectors.
  - Initialization: deterministic (first-k) or kmeans++ (future improvement).
  - Run multiple inits and pick the lowest inertia (sum of squared distances).
  - Post-process: compute centroid summaries and map clusters to `High`/`Average`/`Low` by centroid metrics.
- KNN
  - Input: labeled dataset (features + label).
  - Distance: Euclidean by default; allow Mahalanobis later if correlated features.
  - k selection: cross-validation (odd k for binary) and weighting by inverse distance as optional.

## Evaluation & Tests
- Z-Score: unit tests with synthetic arrays containing known outliers.
- K-Means: silhouette score, inertia, and manual inspection of centroid statistics.
- KNN: accuracy, confusion matrix on holdout set; cross-validation for hyperparameter `k`.

## Integration
- Add invokable service classes under `app/Algorithms` (done).
- Expose endpoints or artisan commands for:
  - Running batch outlier detection (`php artisan algo:detect-outliers`).
  - Running clustering and persisting cluster assignments.
  - Predicting a label for a new record via API.
- Provide examples in `README_EmployeeAlgo1.md`, `README_EmployeeAlgo2.md`, `README_EmployeeAlgo3.md`.

## Monitoring & Maintenance
- Store job runs and results (counts, flagged IDs) in logs or a small table for audits.
- Retrain or re-cluster periodically; log centroid drift and alert if large shifts occur.

## Roadmap / Complementary Work
- Feature engineering and selection (later).
- UI to visualize clusters and flagged outliers (later).
- Hyperparameter tuning pipeline (later).

## Next Concrete Tasks
1. Add unit tests for `ZScore::detectOutliers` and run them.
2. Add example artisan commands/controllers to run the algorithms on real DB data.
3. Add normalization utilities and integrate into `KNN`/`KMeans` usage examples.
