<?php
/**
 * Entry point for the Student Hub application
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

// Start session
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Error handling (development)
if (defined('DEBUG') && DEBUG === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Autoloader for classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/app/';

    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Initialize and run application
use App\Core\App;

try {
    $app = new App();
    $app->run();
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
    if (defined('DEBUG') && DEBUG === true) {
        echo "<br/>File: " . $e->getFile();
        echo "<br/>Line: " . $e->getLine();
    }
}
