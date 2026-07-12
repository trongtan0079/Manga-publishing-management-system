<?php

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Thiết lập đường dẫn gốc của project
// Giúp fix lỗi đường dẫn khi chạy project trong thư mục con (VD: localhost/Manga-publishing-management-system)
$scriptName = $_SERVER['SCRIPT_NAME'];
$baseDir = dirname($scriptName);
$basePath = ($baseDir === '/' || $baseDir === '\\') ? '' : str_replace('\\', '/', $baseDir);
define('BASE_PATH', $basePath);

// Autoloader cũ cho namespace đã bị lược bỏ do các Controller hiện tại không dùng namespace.
// Nếu cần load Model/Core tự động, có thể bổ sung require ở đây.
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/controllers/BaseController.php';

// Lấy tham số controller và action từ URL (ví dụ: ?controller=auth&action=login)
$controllerName = $_GET['controller'] ?? null;
$actionName = $_GET['action'] ?? null;

// Basic Routing based on $_GET parameters (Cơ chế định tuyến dựa trên $_GET)
if ($controllerName && $actionName) {
    // Tự động tạo tên class Controller (VD: 'auth' -> 'AuthController')
    $className = ucfirst($controllerName) . 'Controller';
    $controllerFile = __DIR__ . '/controllers/' . $className . '.php';
    
    // Bổ sung cơ chế tự động require controller từ thư mục controllers/
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
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
}

// Nếu không tìm thấy controller hoặc action → hiển thị trang đăng nhập
header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=showLoginForm');
exit;
