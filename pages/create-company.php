<?php
require_once '../classes/Company.php';

$company = new Company();

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = '';
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $website = $_POST['website'];

    if (empty($name)) {
        $error = "Company name is required.";
    } elseif (empty($description)) {
        $error = "Description is required.";
    } elseif (empty($location)) {
        $error = "Location is required.";
    } elseif (empty($website) || filter_var($website, FILTER_VALIDATE_URL) === false) {
        $error = "Invalid website URL.";
    } else {
        $result = $company->createCompany($userId, $name, $description, $location, $website);

        if ($result) {
            $_SESSION['company_id'] = $result[0]['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Failed to create company. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Company - Job Board</title>
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
        
        .form-control {
            background-color: #212529;
            border: 1px solid #2c3034;
            color: #e9ecef;
        }
        
        .form-control:focus {
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

        .alert-danger {
            background-color: #2c0b0e;
            border-color: #842029;
            color: #ea868f;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        h2 {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-card {
            background-color: #212529;
            border: 1px solid #2c3034;
            border-radius: 8px;
            padding: 2rem;
            margin-top: 2rem;
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

    <div class="container">
        <div class="form-card">
            <h2>🏢 Create Your Company</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">🏢 Company Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Enter company name" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">📝 Description</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="4" placeholder="Tell us about your company"></textarea>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">📍 Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           placeholder="Company location">
                </div>

                <div class="mb-3">
                    <label for="website" class="form-label">🌐 Website</label>
                    <input type="url" class="form-control" id="website" name="website" 
                           placeholder="https://example.com">
                </div>

                <button type="submit" class="btn btn-primary">✨ Create Company</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>