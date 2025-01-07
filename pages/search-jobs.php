<?php
session_start();
require_once '../classes/Job.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$job = new Job();
$searchQuery = $_GET['q'] ?? '';
$locationFilter = $_GET['location'] ?? '';
$typeFilter = $_GET['type'] ?? '';

$jobs = $job->searchJobs($searchQuery, $locationFilter, $typeFilter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Jobs - Job Board</title>
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
        
        .btn-primary {
            background-color: #375a7f;
            border-color: #375a7f;
        }
        
        .btn-primary:hover {
            background-color: #2f4d6f;
            border-color: #2f4d6f;
        }
        
        .card {
            background-color: #212529;
            border: 1px solid #2c3034;
        }
        
        .card-title {
            color: #e9ecef;
        }
        
        .card-subtitle {
            color: #adb5bd !important;
        }
        
        .text-muted {
            color: #6c757d !important;
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

        select option {
            background-color: #212529;
            color: #e9ecef;
        }

        h1 {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
        <h1>🔍 Search Jobs</h1>
        
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control" name="q" placeholder="🔍 Search jobs..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="location" placeholder="📍 Location" 
                       value="<?php echo htmlspecialchars($locationFilter); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="type">
                    <option value="">⌚ Job Type</option>
                    <option value="full-time" <?php echo $typeFilter === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                    <option value="part-time" <?php echo $typeFilter === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                    <option value="contract" <?php echo $typeFilter === 'contract' ? 'selected' : ''; ?>>Contract</option>
                    <option value="remote" <?php echo $typeFilter === 'remote' ? 'selected' : ''; ?>>Remote</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
            </div>
        </form>

        <?php if (empty($jobs)): ?>
            <div class="alert alert-info">📭 No jobs found matching your criteria.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($jobs as $job): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">💼 <?php echo htmlspecialchars($job['title']); ?></h5>
                                <h6 class="card-subtitle mb-2">
                                    🏢 <?php echo htmlspecialchars($job['companies']['name']); ?>
                                </h6>
                                <p class="card-text">
                                    <small class="text-muted">
                                        📍 <?php echo htmlspecialchars($job['location']); ?> | 
                                        💼 <?php echo htmlspecialchars($job['type']); ?> |
                                        💰 <?php echo htmlspecialchars($job['salary_range']); ?>
                                    </small>
                                </p>
                                <p class="card-text"><?php echo substr(htmlspecialchars($job['description']), 0, 150) . '...'; ?></p>
                                <a href="view-job.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">👀 View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>