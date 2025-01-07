<?php
session_start();
require_once __DIR__ . '/../../src/classes/User.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Optional: If you're using a User class method for logout
$user = new User();
$user->logout(); // Make sure this method doesn't try to redirect

// Redirect to index page (root of the project)
header('Location: ../index.php');
exit();
?>