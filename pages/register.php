<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Company.php';

$user = new User();
$company = new Company();
$message = '';
$isError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $result = $user->register(
            $_POST['email'],
            $_POST['password'],
            $_POST['username'],
            $_POST['role']
        );
        
        if ($result) {
            // On successful registration, you might want to auto-login the user
            // and check for company if they're an employer
            if ($_POST['role'] === 'employer') {
                $loginResult = $user->login($_POST['email'], $_POST['password']);
                if ($loginResult && isset($_SESSION['user_id'])) {
                    $companyData = $company->getCompanyByUserId($_SESSION['user_id']);
                    if ($companyData) {
                        $_SESSION['company_id'] = $companyData['id'];
                    }
                }
            }
            
            $message = "✨ Success! Please check your email for verification.";
            header('Location: login.php');
            exit();
        } else {
            $isError = true;
            $message = "⚠️ Registration process incomplete. Please try logging in or contact support.";
        }
    } catch (Exception $e) {
        $isError = true; 
        $errorMessage = $e->getMessage();
            
        if (strpos($errorMessage, 'Supabase API Error') !== false) {
            $jsonPart = trim(substr($errorMessage, strrpos($errorMessage, '-') + 1));
            $errorData = json_decode($jsonPart, true);
            $message = "⚠️ " . ($errorData['msg'] ?? $errorMessage);
        } else {
            $message = "⚠️ " . $errorMessage;
        }
        error_log("Registration error: " . $errorMessage);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - Job Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #e9ecef;
            min-height: 100vh;
            display: flex;
            align-items: center;
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
        
        .form-control::placeholder {
            color: #6c757d;
        }
        
        .form-label {
            color: #e9ecef;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .description-card {
            background-color: #2c3034;
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .login-prompt {
            color: #6c757d;
            margin-top: 1rem;
            text-align: center;
        }
        
        .login-prompt a {
            color: #6ea8fe;
            text-decoration: none;
        }
        
        .login-prompt a:hover {
            color: #8bb9fe;
            text-decoration: underline;
        }
        
        .alert {
            background-color: #212529;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-success {
            background-color: #051b11;
            color: #75b798;
        }
        
        .alert-danger {
            background-color: #2c0b0e;
            color: #ea868f;
        }
        
        .btn-primary {
            background-color: #375a7f;
            border-color: #375a7f;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .btn-primary:hover {
            background-color: #2f4d6f;
            border-color: #2f4d6f;
        }
        
        .form-select option {
            background-color: #212529;
            color: #e9ecef;
        }
        
        h2, h4 {
            color: #e9ecef;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .small {
            color: #adb5bd !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-3">
                <h4 class="mb-2">✨ Welcome to Job Board</h4>
                <div class="description-card p-2">
                    <p class="small mb-0">
                        🌟 Connect with opportunities and talent on our professional platform. 
                        Find your next career move or exceptional talent today.
                    </p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">📝 Create an Account</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert <?php echo $isError ? 'alert-danger' : 'alert-success'; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">📧 Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">👤 Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">🔒 Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">👥 I am a...</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="jobseeker">👨‍💼 Job Seeker</option>
                                    <option value="employer">🏢 Employer</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-3">✨ Create Account</button>
                        </form>
                        
                        <div class="login-prompt">
                            Already have an account? <a href="login.php">🔑 Log in here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>