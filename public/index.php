<?php
session_start();

//  If user is already logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit(); // Add exit after redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Job Board</title>
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

        .hero-section {
            padding: 100px 0;
            text-align: center;
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

        .btn-outline-light {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-card {
            background-color: #212529;
            border: 1px solid #2c3034;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">✨ Job Board</a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-primary me-2" href="pages/login.php">🔑 Login</a>
                <a class="btn btn-outline-light" href="pages/register.php">📝 Register</a>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">🚀 Find Your Dream Job</h1>
            <p class="lead mb-4">Connect with top employers and discover exciting career opportunities</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="pages/register.php?type=jobseeker" class="btn btn-primary btn-lg">
                    👤 Job Seeker Sign Up
                </a>
                <a href="pages/register.php?type=employer" class="btn btn-outline-light btn-lg">
                    🏢 Employer Sign Up
                </a>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <h3>🎯 For Job Seekers</h3>
                    <ul class="list-unstyled">
                        <li>✓ Easy job search</li>
                        <li>✓ Track applications</li>
                        <li>✓ Profile management</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <h3>💼 For Employers</h3>
                    <ul class="list-unstyled">
                        <li>✓ Post job listings</li>
                        <li>✓ Manage applications</li>
                        <li>✓ Company profile</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <h3>⚡ Features</h3>
                    <ul class="list-unstyled">
                        <li>✓ Real-time updates</li>
                        <li>✓ Secure platform</li>
                        <li>✓ Easy to use</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>