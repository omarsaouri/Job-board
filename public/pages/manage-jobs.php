<?php
   session_start();
   require_once __DIR__ . '/../../src/classes/Job.php';
   require_once __DIR__ . '/../../src/classes/Application.php';

   $job = new Job();
   $role = $_SESSION['role'];
   $userId = $_SESSION['user_id'];

   if (!isset($userId) || !isset($role)) {
       header('Location: login.php');
       exit();
   }

   $companyId = $_SESSION['company_id'];

   $jobs = [];
   if ($role === 'employer' && $companyId) {
       $jobs = $job->getJobsByEmployer($companyId);
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Employer Dashboard</title>
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
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        
        .btn-dark {
            background-color: #343a40;
            border-color: #343a40;
        }
        
        .btn-dark:hover {
            background-color: #23272b;
            border-color: #1d2124;
        }
        
        .btn-secondary {
            background-color: #373b3e;
            border-color: #373b3e;
        }
        
        .btn-secondary:hover {
            background-color: #4d5154;
            border-color: #4d5154;
        }

        .btn-success {
            background-color: #2fb344;
            border-color: #2fb344;
        }

        .btn-success:hover {
            background-color: #27963a;
            border-color: #27963a;
        }
        
        .badge {
            padding: 0.5em 0.8em;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        
        strong {
            color: #adb5bd;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .card {
            transition: transform 0.2s ease-in-out;
        }
        
        .card:hover {
            transform: translateY(-2px);            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .emoji-label {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
   </style>
</head>
<body>
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
       <div class="container">
            <a class="navbar-brand" href="dashboard.php">✨ Job Board</a>
           <a href="post_job.php" class="btn btn-success">📝 Post New Job</a>
       </div>
   </nav>

   <div class="container mt-4">
       <h2 class="mb-4">📋 My Job Listings</h2>
       
       <div class="row">
           <?php foreach ($jobs as $job): ?>
               <div class="col-lg-6 mb-4">
                   <div class="card h-100">
                       <div class="card-header d-flex justify-content-between align-items-center">
                           <h5 class="mb-0">💼 <?php echo htmlspecialchars($job['title']); ?></h5>
                           <span class="badge bg-<?php echo $job['status'] === 'active' ? 'success' : 'secondary'; ?>">
                               🔄 <?php echo ucfirst($job['status']); ?>
                           </span>
                       </div> 
                       <div class="card-body">
                           <div class="mb-3">
                               <div class="mb-2">
                                   <strong>📍 Location:</strong> 
                                   <span class="text-light"><?php echo htmlspecialchars($job['location']); ?></span>
                               </div>
                               <div class="mb-2">
                                   <strong>⌚ Type:</strong> 
                                   <span class="text-light"><?php echo ucfirst($job['type']); ?></span>
                               </div>
                               <div class="mb-2">
                                   <strong>💰 Salary:</strong> 
                                   <span class="text-light"><?php echo htmlspecialchars($job['salary_range']); ?></span>
                               </div>
                           </div>
                           <div class="mb-3">
                               <p><strong>📋 Description:</strong><br>
                               <span class="text-light"><?php echo htmlspecialchars($job['description']); ?></span></p>
                           </div>
                           <div class="mb-3">
                               <p><strong>📌 Requirements:</strong><br>
                               <span class="text-light"><?php echo htmlspecialchars($job['requirements']); ?></span></p>
                           </div>
                           
                           <div class="d-flex justify-content-between align-items-center mt-4">
                               <div>
                                   <a href="view-applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-dark">
                                        👥 Applications (<?php echo (new Application())->getApplicationCount($job['id']); ?>)
                                   </a>
                               </div>
                               <div>
                                   <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn btn-primary me-2">
                                       ✏️ Edit
                                   </a>
                                   <a href="delete-job.php?id=<?php echo $job['id']; ?>" 
                                      class="btn btn-danger" 
                                      onclick="return confirm('Are you sure you want to delete this job?')">
                                       🗑️ Delete
                                   </a>
                               </div>
                           </div>
                       </div>
                       <div class="card-footer text-muted">
                           📅 Posted: <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                       </div>
                   </div>
               </div>
           <?php endforeach; ?>
       </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>