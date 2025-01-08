<?php
session_start();
require_once __DIR__ . '/../../src/classes/Job.php';
require_once __DIR__ . '/../../src/classes/Company.php';


$job = new Job();
$company = new Company();


// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

// Get user role
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$companyId = $_SESSION['company_id'];

// For employers, check company association
if ($role === 'employer') {
    // Check if we already have company_id in session
    if (!isset($_SESSION['company_id'])) {
        // Try to fetch company data
        $companyData = $company->getCompanyByUserId($userId);
        if ($companyData) {
            $_SESSION['company_id'] = $companyData['id'];
        } else {
            header('Location: create-company.php');
            exit();
        }
    }
}

$jobs = [];
if ($role === 'employer' && $companyId) {
    $jobs = $job->getJobsByEmployer($companyId);
}

// Additional checks can be added here
if (!in_array($role, ['employer', 'jobseeker'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Job Board</title>
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
            transition: transform 0.2s ease-in-out;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-title {
            color: #e9ecef;
        }
        
        .table {
            color: #e9ecef;
        }
        
        .table-dark {
            background-color: #212529;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.075);
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
        
        .alert {
            background-color: #212529;
            border: none;
        }
        
        .alert-success {
            background-color: #051b11;
            color: #75b798;
        }
        
        .alert-danger {
            background-color: #2c0b0e;
            color: #ea868f;
        }
        
        .table-responsive {
            background-color: #212529;
            border-radius: 0.5rem;
            padding: 1rem;
            border: 1px solid #2c3034;
        }
        
        .nav-link {
            color: #e9ecef;
        }
        
        .nav-link:hover {
            color: #fff;
        }
        
        .navbar {
            border-bottom: 1px solid #2c3034;
        }

        .emoji-icon {
            margin-right: 0.25rem;
        }

        h2 {
            margin-bottom: 1.5rem;
        }

        .empty-state {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">✨ Job Board</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link">👋 Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a class="nav-link" href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✅ <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                ⚠️ <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($role === 'employer'): ?>
            <!-- Employer Dashboard -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">⚡ Quick Actions</h5>
                            <a href="post-job.php" class="btn btn-primary mb-2 w-100">📝 Post New Job</a>
                            <a href="manage-jobs.php" class="btn btn-secondary w-100">📊 Manage Jobs</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <h2>📌 Your Posted Jobs</h2>
                    <?php if (empty($jobs)): ?>
                        <p class="empty-state">📭 No jobs posted yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-dark mb-0">
                                <thead>
                                    <tr>
                                        <th>💼 Job Title</th>
                                        <th>📅 Date Posted</th>
                                        <th>🔄 Status</th>
                                        <th>⚙️ Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jobs as $job): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($job['status']); ?></td>
                                            <td>
                                                <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary me-2">✏️ Edit</a>
                                                <a href="delete-job.php?id=<?php echo $job['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this job?')">🗑️ Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Job Seeker Dashboard -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">⚡ Quick Actions</h5>
                            <a href="search-jobs.php" class="btn btn-primary mb-2 w-100">🔍 Search Jobs</a>
                            <a href="my-applications.php" class="btn btn-secondary w-100">📋 My Applications</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <h2>🌟 Recent Job Listings</h2>
                    <?php
                    $recentJobs = $job->getRecentJobs(10);
                    if (empty($recentJobs)): ?>
                        <p class="empty-state">📭 No jobs available at the moment.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-dark mb-0">
                                <thead>
                                    <tr>
                                        <th>💼 Title</th>
                                        <th>🏢 Company</th>
                                        <th>📍 Location</th>
                                        <th>📅 Posted</th>
                                        <th>👀 Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentJobs as $job): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                                            <td>
                                                <a href="view-job.php?id=<?php echo $job['id']; ?>" 
                                                   class="btn btn-sm btn-primary">👉 View Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>