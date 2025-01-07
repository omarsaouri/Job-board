<?php
session_start();
require_once '../classes/Job.php';
require_once '../classes/Application.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

$jobId = $_GET['job_id'] ?? null;
if (!$jobId) {
    header('Location: manage-jobs.php');
    exit();
}

$application = new Application();
$applications = $application->getApplicationsByJobId($jobId);
$job = (new Job())->getJobById($jobId);

if (!$job || $job['company_id'] != $_SESSION['company_id']) {
    header('Location: manage-jobs.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #e9ecef;
        }
        
        .card {
            background-color: #212529;
            border: 1px solid #2c3034;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .card-header {
            background-color: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid #2c3034;
            color: #e9ecef;
        }
        
        .card-body {
            color: #e9ecef;
        }
        
        .card-footer {
            background-color: rgba(0, 0, 0, 0.2);
            border-top: 1px solid #2c3034;
            color: #6c757d;
        }
        
        .form-select {
            background-color: #2c3034;
            border: 1px solid #373b3e;
            color: #e9ecef;
        }
        
        .form-select:focus {
            background-color: #2c3034;
            border-color: #375a7f;
            color: #e9ecef;
            box-shadow: 0 0 0 0.25rem rgba(55, 90, 127, 0.25);
        }
        
        .form-select option {
            background-color: #2c3034;
            color: #e9ecef;
        }
        
        .alert-info {
            background-color: #1c2a36;
            border-color: #235d93;
            color: #9aadc7;
        }
        
        .btn-outline-light:hover {
            background-color: #e9ecef;
            color: #212529;
        }
        
        strong {
            color: #adb5bd;
        }
        
        a {
            color: #6ea8fe;
            text-decoration: none;
        }
        
        a:hover {
            color: #8bb9fe;
            text-decoration: underline;
        }
        
        .badge {
            padding: 0.5em 0.8em;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        /* Add gap utility for older Bootstrap versions */
        .gap-2 {
            gap: 0.5rem !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="manage-jobs.php">Job Board</a>
            <a href="manage-jobs.php" class="btn btn-outline-light">Back to Jobs</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Applications for <?php echo htmlspecialchars($job['title']); ?></h2>
        
        <?php if (empty($applications)): ?>
            <div class="alert alert-info">No applications received yet.</div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($app['profiles']['username']); ?></h5>
                        <span class="badge bg-<?php echo getStatusColor($app['status']); ?>">
                            <?php echo ucfirst($app['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Role:</strong> <span class="text-light"><?php echo htmlspecialchars($app['profiles']['role']); ?></span></p>
                                <p><strong>Cover Letter:</strong><br>
                                <span class="text-light"><?php echo nl2br(htmlspecialchars($app['cover_letter'] ?? 'No cover letter provided')); ?></span></p>
                                <?php if ($app['resume_url']): ?>
                                    <p><strong>Resume:</strong> <a href="<?php echo htmlspecialchars($app['resume_url']); ?>" target="_blank">View Resume</a></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <form action="update-application-status.php" method="POST" class="d-flex flex-column gap-2">
                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                    <select name="status" class="form-select mb-2">
                                        <option value="pending" <?php echo $app['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="reviewed" <?php echo $app['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                        <option value="accepted" <?php echo $app['status'] === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                        <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        Applied: <?php echo date('M d, Y', strtotime($app['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function getStatusColor($status) {
    return match($status) {
        'pending' => 'secondary',
        'reviewed' => 'info',
        'accepted' => 'success',
        'rejected' => 'danger',
        default => 'secondary'
    };
}
?>