<?php
session_start();
require_once '../classes/Job.php';

// Check if user is logged in and is an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

$job = new Job();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $jobData = [
            'company_id' => $_SESSION['company_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'requirements' => $_POST['requirements'],
            'salary_range' => $_POST['salary_range'],
            'location' => $_POST['location'],
            'type' => $_POST['type']
        ];

        if ($job->createJob($jobData)) {
            $message = "Job posted successfully!";
            header("Location: dashboard.php");
            exit();
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a New Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #e9ecef;
            min-height: 100vh;
        }
        
        .card {
            background-color: #212529;
            border: 1px solid #2c3034;
        }
        
        .form-control, .form-select {
            background-color: #2c3034;
            border: 1px solid #373b3e;
            color: #e9ecef;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #2c3034;
            border-color: #375a7f;
            color: #e9ecef;
            box-shadow: 0 0 0 0.25rem rgba(55, 90, 127, 0.25);
        }
        
        .form-label {
            color: #e9ecef;
        }
        
        .btn-primary {
            background-color: #375a7f;
            border-color: #375a7f;
        }
        
        .btn-primary:hover {
            background-color: #2f4d6f;
            border-color: #2f4d6f;
        }
        
        .btn-secondary {
            background-color: #373b3e;
            border-color: #373b3e;
        }
        
        .btn-secondary:hover {
            background-color: #4d5154;
            border-color: #4d5154;
        }
        
        .alert {
            background-color: #212529;
            border: none;
        }
        
        .alert-info {
            background-color: #1c2a36;
            color: #9aadc7;
        }

        .form-container {
            background-color: #212529;
            border: 1px solid #2c3034;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-top: 2rem;
        }

        select option {
            background-color: #212529;
            color: #e9ecef;
        }

        .emoji-label {
            margin-right: 0.5rem;
        }

        .nav-link {
            color: #e9ecef;
        }
        
        .nav-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">✨ Job Board</a>
            <a href="dashboard.php" class="nav-link">🏠 Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <h2 class="mb-4">📝 Post a New Job</h2>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-info">ℹ️ <?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">💼 Job Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">📋 Job Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">📌 Requirements</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="salary_range" class="form-label">💰 Salary Range</label>
                            <input type="text" class="form-control" id="salary_range" name="salary_range" required>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">📍 Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">⌚ Job Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="full-time">Full Time</option>
                                <option value="part-time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="remote">Remote</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">✨ Post Job</button>
                            <a href="dashboard.php" class="btn btn-secondary ms-2">← Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>