<?php
session_start();
require_once __DIR__ . '/../../src/classes/Job.php';

// Check if user is logged in and is an employer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

$job = new Job();
$jobId = isset($_GET['id']) ? $_GET['id'] : 0;
$companyId = $_SESSION['company_id'];

try {
    // Verify that this job belongs to the logged-in employer's company
    $jobDetails = $job->getJobById($jobId);
    if (!$jobDetails || $jobDetails['company_id'] !== $companyId) {
        header('Location: dashboard.php');
        exit();
    }

    // Delete the job
    $job->deleteJob($jobId);
    $_SESSION['success'] = "Job deleted successfully!";
} catch (Exception $e) {
    $_SESSION['error'] = "Error deleting job: " . $e->getMessage();
}

header('Location: dashboard.php');
exit();