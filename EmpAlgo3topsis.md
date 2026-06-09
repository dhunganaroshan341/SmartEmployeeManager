Now let's build the **3rd algorithm properly using TOPSIS**.

This is actually where things get interesting because TOPSIS is a real decision-making algorithm used in HR systems, supplier selection, scholarship ranking, recruitment, promotions, etc.

---

# Goal

Question:

> Which employees should be promoted first?

Instead of:

```text
if performance > 90
    promote
```

TOPSIS ranks everyone against an "ideal employee."

---

# Criteria

We'll use:

| Code | Criteria               |
| ---- | ---------------------- |
| C1   | Performance Score      |
| C2   | Attendance             |
| C3   | Tenure (Months Worked) |
| C4   | Leadership Score       |
| C5   | Project Success Rate   |

Example employee data:

| Employee | Perf | Attend | Tenure | Leadership | Projects |
| -------- | ---- | ------ | ------ | ---------- | -------- |
| Roshan   | 89   | 95     | 24     | 80         | 92       |
| John     | 84   | 90     | 36     | 88         | 85       |
| Sarah    | 92   | 93     | 18     | 78         | 95       |

---

# Step 1: Create Decision Matrix

```text
           C1   C2   C3   C4   C5

Roshan     89   95   24   80   92
John       84   90   36   88   85
Sarah      92   93   18   78   95
```

---

# Step 2: Normalize

TOPSIS converts values to comparable scales.

For each column:

r_{ij}=\frac{x_{ij}}{\sqrt{\sum x_{ij}^{2}}}

Example for Performance:

```text
sqrt(
89² +
84² +
92²
)
=
153.8
```

Then:

```text
Roshan = 89 / 153.8
John   = 84 / 153.8
Sarah  = 92 / 153.8
```

---

# Step 3: Apply Weights

Suppose HR decides:

| Criteria    | Weight |
| ----------- | ------ |
| Performance | 0.35   |
| Attendance  | 0.20   |
| Tenure      | 0.15   |
| Leadership  | 0.15   |
| Projects    | 0.15   |

Multiply normalized values by weights.

---

# Step 4: Determine Ideal Employee

Positive ideal:

```text
Best Performance
Best Attendance
Best Tenure
Best Leadership
Best Project Rate
```

Negative ideal:

```text
Worst Performance
Worst Attendance
Worst Tenure
Worst Leadership
Worst Project Rate
```

---

# Step 5: Distance Calculation

Distance from best:

S_i^{+}=\sqrt{\sum(v_{ij}-A_j^{+})^2}

Distance from worst:

S_i^{-}=\sqrt{\sum(v_{ij}-A_j^{-})^2}

---

# Step 6: Closeness Score

Final ranking score:

CC_i=\frac{S_i^{-}}{S_i^{+}+S_i^{-}}

Range:

```text
0 → terrible candidate
1 → ideal candidate
```

Example:

| Employee | TOPSIS Score |
| -------- | ------------ |
| Roshan   | 0.81         |
| Sarah    | 0.74         |
| John     | 0.62         |

Ranking:

```text
#1 Roshan
#2 Sarah
#3 John
```

---

# Laravel Structure

```text
App\Services\Analytics\

PromotionRecommendationService
TopsisCalculator
PromotionFeatureBuilder
```

---

# PromotionFeatureBuilder

Collect data:

```php
[
    'employee_id' => 1,
    'performance' => 89,
    'attendance' => 95,
    'tenure' => 24,
    'leadership' => 80,
    'project_success' => 92,
]
```

for every employee.

---

# TopsisCalculator

Methods:

```php
normalizeMatrix()

applyWeights()

findPositiveIdeal()

findNegativeIdeal()

calculateDistances()

calculateCloseness()
```

---

# Database Table

```sql
promotion_recommendations
```

```text
id
employee_id

performance_score
attendance_score
tenure_score
leadership_score
project_success_score

topsis_score
rank

created_at
updated_at
```

---

# Final Architecture

You now have three recognized algorithms:

### Algorithm 1

**Employee Retention Prediction**

* Logistic Regression
* Predicts leave/stay probability

### Algorithm 2

**Performance Evaluation**

* AHP
* Calculates weighted employee performance

### Algorithm 3

**Promotion Recommendation**

* TOPSIS
* Ranks employees for promotion

Data flow:

```text
Attendance
Tasks
Reviews
Payroll
      │
      ▼
AHP Performance Score
      │
      ▼
TOPSIS Promotion Ranking

Attendance
Tasks
Reviews
      │
      ▼
Logistic Regression
      │
      ▼
Retention Prediction
```

This is a solid architecture for a Laravel-based HR/Payroll system because each algorithm has a distinct purpose and uses recognized methods that you can justify in your project report and presentation.
