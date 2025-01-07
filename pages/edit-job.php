<?php
session_start();
require_once '../classes/Job.php';

// Check if user is logged in and is an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

$job = new Job();
$jobId = isset($_GET['id']) ? $_GET['id'] : '';
$companyId = $_SESSION['company_id'];

// Fetch job details
try {
    $jobDetails = $job->getJobById($jobId);
    
    // Verify that this job belongs to the logged-in employer's company
    if (!$jobDetails || $jobDetails['company_id'] !== $companyId) {
        header('Location: dashboard.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching job details.";
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $updateData = [
            'id' => $jobId,
            'company_id' => $companyId,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'requirements' => $_POST['requirements'],
            'salary_range' => $_POST['salary_range'],
            'location' => $_POST['location'],
            'type' => $_POST['type']
        ];

        $job->updateJob($updateData);
        $_SESSION['success'] = "Job updated successfully!";
        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating job: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job - Job Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #e9ecef;
            min-height: 100vh;
        }
        
        .form-control, .form-select {
            background-color: #212529;
            border: 1px solid #2c3034;
            color: #e9ecef;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #2c3034;
            border-color: #375a7f;
            color: #e9ecef;
            box-shadow: 0 0 0 0.25rem rgba(55, 90, 127, 0.25);
        }
        
        .form-control::placeholder {
            color: #6c757d;
        }
        
        .form-label {
            color: #e9ecef;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert {
            background-color: #212529;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-danger {
            background-color: #2c0b0e;
            border-color: #842029;
            color: #ea868f;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary {
            background-color: #373b3e;
            border-color: #373b3e;
        }
        
        .btn-secondary:hover {
            background-color: #4d5154;
            border-color: #4d5154;
        }
        
        .btn-primary {
            background-color: #375a7f;
            border-color: #375a7f;
        }
        
        .btn-primary:hover {
            background-color: #2f4d6f;
            border-color: #2f4d6f;
        }
        
        select option {
            background-color: #212529;
            color: #e9ecef;
        }
        
        .form-card {
            background-color: #212529;
            border: 1px solid #2c3034;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        h2 {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">✨ Job Board</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <h2 class="mb-4">✏️ Edit Job</h2>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            ⚠️ <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">💼 Job Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($jobDetails['title']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">📋 Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo trim(htmlspecialchars($jobDetails['description'])); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="requirements" class="form-label">📌 Requirements</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="4" required><?php echo trim(htmlspecialchars($jobDetails['requirements'])); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="salary_range" class="form-label">💰 Salary Range</label>
                            <input type="text" class="form-control" id="salary_range" name="salary_range"
                                   value="<?php echo htmlspecialchars($jobDetails['salary_range']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">📍 Location</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   value="<?php echo htmlspecialchars($jobDetails['location']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">⌚ Job Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="full-time" <?php echo $jobDetails['type'] === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                                <option value="part-time" <?php echo $jobDetails['type'] === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                                <option value="contract" <?php echo $jobDetails['type'] === 'contract' ? 'selected' : ''; ?>>Contract</option>
                                <option value="internship" <?php echo $jobDetails['type'] === 'internship' ? 'selected' : ''; ?>>Internship</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">💫 Update Job</button>
                            <a href="dashboard.php" class="btn btn-secondary ms-2">← Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>