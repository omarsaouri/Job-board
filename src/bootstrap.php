<?php
// src/bootstrap.php

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Include autoloader
require_once __DIR__ . '/autoload.php';

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone if needed
date_default_timezone_set('UTC');
?>