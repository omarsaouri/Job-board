<?php
// src/autoload.php

spl_autoload_register(function ($className) {
    // Convert the class name to a file path
    $file = __DIR__ . '/classes/' . $className . '.php';
    
    // Check if the file exists
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
});

// Include the config file that's needed by all classes
require_once __DIR__ . '/config/supabase.php';
?>