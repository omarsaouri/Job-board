<?php
// update-application-status.php
session_start();
require_once '../classes/Application.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id'] ?? null;
    $newStatus = $_POST['status'] ?? null;
    
    if ($applicationId && $newStatus) {
        $application = new Application();
        try {
            $application->updateStatus($applicationId, $newStatus);
            $_SESSION['success'] = 'Application status updated successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update application status.';
        }
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();