<?php
session_start();
require_once '../classes/Application.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'jobseeker') {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$application = new Application();
$applications = $application->getUserApplications($userId);

function getStatusBadgeColor($status) {
    return match($status) {
        'pending' => 'warning',
        'reviewed' => 'info',
        'accepted' => 'success',
        'rejected' => 'danger',
        default => 'secondary'
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications - Job Board</title>
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

        h1 {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table {
            color: #e9ecef;
            border-color: #2c3034;
        }
        
        .table-hover tbody tr:hover {
            background-color: #2c3034;
            color: #fff;
        }
        
        .table thead th {
            background-color: #212529;
            border-bottom: 2px solid #2c3034;
            color: #adb5bd;
        }
        
        .table td {
            border-color: #2c3034;
        }
        
        .btn-secondary {
            background-color: #373b3e;
            border-color: #373b3e;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary:hover {
            background-color: #4d5154;
            border-color: #4d5154;
        }
        
        .alert-info {
            background-color: #1c2127;
            border-color: #375a7f;
            color: #8bb9fe;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        a {
            color: #5e9eff;
            text-decoration: none;
        }

        a:hover {
            color: #7eb2ff;
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
        <h1>📋 My Applications</h1>
        
        <?php if (empty($applications)): ?>
            <div class="alert alert-info">
                📭 You haven't applied to any jobs yet.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>💼 Job Title</th>
                            <th>🏢 Company</th>
                            <th>📅 Applied On</th>
                            <th>🔄 Status</th>
                            <th>📄 Resume</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>
                                    <a href="view-job.php?id=<?php echo htmlspecialchars($app['job_id']); ?>">
                                        <?php echo htmlspecialchars($app['jobs']['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($app['jobs']['companies']['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($app['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($app['resume_url']); ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-secondary">
                                        📄 View Resume
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>