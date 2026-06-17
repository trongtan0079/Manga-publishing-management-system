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

// Lấy tham số controller và action từ URL (ví dụ: ?controller=user&action=index)
$controllerName = $_GET['controller'] ?? null;
$actionName = $_GET['action'] ?? null;

// Basic Routing based on $_GET parameters (Cơ chế định tuyến dựa trên $_GET)
if ($controllerName && $actionName) {
    // Tự động tạo tên class Controller (VD: 'user' -> 'App\Controllers\UserController')
    $className = 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';
    
    if (class_exists($className)) {
        $controller = new $className();
        
        // Kiểm tra xem action (hàm) có tồn tại trong class Controller không
        if (method_exists($controller, $actionName)) {
            // Lấy ID nếu có truyền trên URL
            $id = $_GET['id'] ?? null;
            if ($id !== null) {
                // Gọi action và truyền ID vào hàm
                $controller->$actionName($id);
            } else {
                // Gọi action không có tham số
                $controller->$actionName();
            }
            exit; // Dừng thực thi sau khi action xử lý xong
        }
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Fallback Basic Routing
switch ($uri) {
    case '/':
    case '/index.php':
        header('Location: /index.php?controller=auth&action=login');
        break;
    case '/login':
        header('Location: /index.php?controller=auth&action=login');
        break;
    case '/admin/dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
