<?php
session_start();
require_once __DIR__ . '/../../src/classes/Job.php';
require_once __DIR__ . '/../../src/classes/Application.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'jobseeker') {
    header('Location: login.php');
    exit();
}

$jobId = $_GET['id'];
$userId = $_SESSION['user_id'];

$job = new Job();
$application = new Application();

try {
    $jobDetails = $job->getJobById($jobId);
    if (!$jobDetails) {
        $_SESSION['error'] = "Job not found.";
        header('Location: dashboard.php');
        exit();
    }

    if (isset($_POST['apply'])) {
        $resumeUrl = $_POST['resume_url'] ?? '';
        $coverLetter = $_POST['cover_letter'] ?? null;

        if (empty($resumeUrl)) {
            throw new Exception("Resume URL is required");
        }

        $applicationData = [
            'job_id' => $jobId,
            'user_id' => $userId,
            'resume_url' => $resumeUrl,
            'cover_letter' => $coverLetter,
            'status' => 'pending'
        ];
        
        if ($application->createApplication($applicationData)) {
            $_SESSION['success'] = "Application submitted successfully!";
            header('Location: dashboard.php');
            exit();
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details - <?php echo htmlspecialchars($jobDetails['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #e9ecef;
            min-height: 100vh;
        }
        
        .navbar {
            background-color: #212529 !important;
            border-bottom: 1px solid #2c3034;
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        .form-text {
            color: #6c757d;
        }
        
        .btn-primary {
            background-color: #375a7f;
            border-color: #375a7f;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: #2f4d6f;
            border-color: #2f4d6f;
        }
        
        .card {
            background-color: #212529;
            border: 1px solid #2c3034;
        }
        
        .alert-danger {
            background-color: #2c0b0e;
            border-color: #842029;
            color: #ea868f;
        }
        
        .alert-info {
            background-color: #1c2127;
            border-color: #375a7f;
            color: #8bb9fe;
        }

        .nav-link {
            color: #e9ecef !important;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        h1 {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">✨ Job Board</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">📊 Dashboard</a>
                <a class="nav-link" href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                ⚠️ <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <h1>💼 <?php echo htmlspecialchars($jobDetails['title']); ?></h1>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">✨ Job Details</h5>
                        <p><span class="detail-label">📍 Location:</span> <?php echo htmlspecialchars($jobDetails['location']); ?></p>
                        <p><span class="detail-label">⌚ Type:</span> <?php echo htmlspecialchars($jobDetails['type']); ?></p>
                        <p><span class="detail-label">💰 Salary Range:</span> <?php echo htmlspecialchars($jobDetails['salary_range']); ?></p>
                        
                        <h5>📋 Description</h5>
                        <p><?php echo nl2br(htmlspecialchars($jobDetails['description'])); ?></p>
                        
                        <h5>📌 Requirements</h5>
                        <p><?php echo nl2br(htmlspecialchars($jobDetails['requirements'])); ?></p>

                        <?php if (!$application->hasUserApplied($userId, $jobId)): ?>
                            <form method="POST" class="mt-4">
                                <div class="mb-3">
                                    <label for="resume_url" class="form-label">📄 Resume URL (PDF)*</label>
                                    <input type="url" class="form-control" id="resume_url" name="resume_url" required>
                                    <div class="form-text">Please provide a link to your resume in PDF format</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cover_letter" class="form-label">✉️ Cover Letter</label>
                                    <textarea class="form-control" id="cover_letter" name="cover_letter" rows="4"></textarea>
                                </div>

                                <button type="submit" name="apply" class="btn btn-primary">✨ Submit Application</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info mt-4">
                                ℹ️ You have already applied for this position.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>