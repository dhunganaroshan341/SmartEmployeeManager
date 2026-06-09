**AHP (Analytic Hierarchy Process)** properly instead of a 

# Goal

Question:

> How well is an employee performing?

Instead of:

```text
Attendance = 30%
Tasks = 40%
Review = 30%
```

we let AHP determine those weights systematically.

---

# Step 1: Define Criteria

For a payroll/employee system, I'd use:

```text
Attendance
Task Completion
Task Quality
Deadline Adherence
Manager Rating
```

Let's name them:

```text
A = Attendance
B = Task Completion
C = Task Quality
D = Deadline Adherence
E = Manager Rating
```

---

# Step 2: Pairwise Comparison Matrix

AHP compares every criterion against every other criterion.

Example:

|   | A | B   | C   | D   | E   |
| - | - | --- | --- | --- | --- |
| A | 1 | 1/3 | 1/5 | 1/3 | 1/4 |
| B | 3 | 1   | 1/2 | 1   | 1   |
| C | 5 | 2   | 1   | 2   | 2   |
| D | 3 | 1   | 1/2 | 1   | 1   |
| E | 4 | 1   | 1/2 | 1   | 1   |

Interpretation:

```text
Task Quality is 5x more important than Attendance.
Manager Rating is 4x more important than Attendance.
```

These numbers come from HR/business requirements.

---

# Step 3: Calculate Weights

After normalization you might get:

| Criterion          | Weight |
| ------------------ | ------ |
| Attendance         | 0.08   |
| Task Completion    | 0.22   |
| Task Quality       | 0.34   |
| Deadline Adherence | 0.18   |
| Manager Rating     | 0.18   |

Meaning:

```text
Attendance        8%
Task Completion  22%
Task Quality     34%
Deadline         18%
Manager Rating   18%
```

---

# Step 4: Employee Scores

Suppose Roshan has:

| Criterion       | Score |
| --------------- | ----- |
| Attendance      | 95    |
| Task Completion | 90    |
| Task Quality    | 88    |
| Deadline        | 92    |
| Manager Rating  | 85    |

Final score:

```text
95 × 0.08
+
90 × 0.22
+
88 × 0.34
+
92 × 0.18
+
85 × 0.18
```

= 89.08

---

# Laravel Structure

```text
App/
└── Services/
    └── Analytics/
        ├── AhpWeightCalculator.php
        └── EmployeePerformanceService.php
```

---

# EmployeePerformanceService

```php
class EmployeePerformanceService
{
    public function calculate(
        array $scores,
        array $weights
    ): float {

        $result = 0;

        foreach ($weights as $key => $weight) {
            $result += $scores[$key] * $weight;
        }

        return round($result, 2);
    }
}
```

---

# Employee Scores Builder

Create a service:

```php
EmployeePerformanceFeatureBuilder
```

Output:

```php
[
    'attendance' => 95,
    'task_completion' => 90,
    'task_quality' => 88,
    'deadline_adherence' => 92,
    'manager_rating' => 85,
]
```

---

# Database Table

```sql
employee_performance_scores
```

```text
id
employee_id

attendance_score
task_completion_score
task_quality_score
deadline_score
manager_score

final_score

created_at
updated_at
```

---

# Rating Bands

```text
90-100  Outstanding

80-89   Excellent

70-79   Good

60-69   Average

0-59    Needs Improvement
```

---

# What I Would Do in Your System

Since you're building this in Laravel and probably don't yet have manager reviews or task quality scoring, I'd design these tables first:

```text
tasks
task_assignments
task_submissions
performance_reviews
```

and define exactly how each score is computed:

| Metric             | Calculation                     |
| ------------------ | ------------------------------- |
| Attendance         | Present days / Working days     |
| Task Completion    | Completed / Assigned            |
| Task Quality       | Review score given by manager   |
| Deadline Adherence | On-time tasks / Completed tasks |
| Manager Rating     | Monthly review (1-5)            |

Once those formulas are fixed, the AHP implementation becomes straightforward and your performance score becomes reproducible and explainable. The next algorithm (TOPSIS for promotion recommendations) can then directly consume the performance score generated here.
