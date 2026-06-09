<?php

session_start();

// Autoloader for classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', strtolower($relative_class)) . '.php';
    // Fix for capitalization in paths based on PSR-4 like behavior
    $file = str_replace(['/controllers/', '/models/', '/config/'], ['/controllers/', '/models/', '/config/'], $file);
    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\AuthController;
use App\Controllers\UserController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Basic Routing example
switch ($uri) {
    case '/':
    case '/login':
        echo 'Welcome to MangaWorkflowSystem - Login Page';
        break;
    case '/admin/dashboard':
        echo 'Admin Dashboard';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
