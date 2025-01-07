<?php
require_once '../classes/User.php';
require_once '../classes/Company.php';

session_start();
$user = new User();
$company = new Company();
$message = '';
$isError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $result = $user->login(
            $_POST['email'],
            $_POST['password']
        );
        
        if ($result) {
            // After successful login, check for company if user is an employer
            if ($_SESSION['role'] === 'employer') {
                $companyData = $company->getCompanyByUserId($_SESSION['user_id']);
                if ($companyData) {
                    $_SESSION['company_id'] = $companyData['id'];
                }
            }

            if (isset($_SESSION['role'])) {
                header('Location: dashboard.php');
                exit();
            } else {
                $isError = true;
                $message = "Login successful but role not set.";
            }
        } else {
            $isError = true;
            $message = "Login failed. Invalid credentials.";
        }
    } catch (Exception $e) {
        $isError = true; 
        $errorMessage = $e->getMessage();
            
        if (strpos($errorMessage, 'Supabase API Error') !== false) {
            $jsonPart = trim(substr($errorMessage, strrpos($errorMessage, '-') + 1));
            $errorData = json_decode($jsonPart, true);
            $message = $errorData['msg'] ?? $errorMessage;
        } else {
            $message = $errorMessage;
        }
        error_log("Login error: " . $errorMessage);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #e9ecef;
            min-height: 90vh;
            display: flex;
            align-items: center;
        }
        
        .card {
            background-color: #212529;
            border: 1px solid #2c3034;
        }
        
        .form-control {
            background-color: #2c3034;
            border: 1px solid #373b3e;
            color: #e9ecef;
        }
        
        .form-control:focus {
            background-color: #2c3034;
            border-color: #375a7f;
            color: #e9ecef;
            box-shadow: 0 0 0 0.25rem rgba(55, 90, 127, 0.25);
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
        
        .register-prompt {
            color: #6c757d;
            margin-top: 1rem;
            text-align: center;
        }
        
        .register-prompt a {
            color: #6ea8fe;
            text-decoration: none;
        }
        
        .register-prompt a:hover {
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

        .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }

        .forgot-password a {
            color: #6c757d;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .forgot-password a:hover {
            color: #8bb9fe;
            text-decoration: underline;
        }
        
        h2 {
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
                <h2 class="mb-2">✨ Welcome Back</h2>
                <div class="description-card p-2">
                    <p class="big mb-0">
                        🚀 Access your account to manage listings, track applications, 
                        or continue your job search journey.
                    </p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">🔐 Sign In</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert <?php echo $isError ? 'alert-danger' : 'alert-success'; ?>">
                                <?php echo $isError ? '⚠️ ' : '✅ '; echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">📧 Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">🔒 Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <!-- 
                            <div class="forgot-password">
                                <a href="forgot-password.php">Forgot your password?</a>
                            </div>
                            -->
                            <button type="submit" class="btn btn-primary w-100 mt-3">🚀 Login</button>
                        </form>
                        
                        <div class="register-prompt">
                            Don't have an account? <a href="register.php">📝 Sign up here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>