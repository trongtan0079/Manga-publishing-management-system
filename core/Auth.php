<?php
// core/Auth.php

/**
 * Bắt đầu session nếu chưa được khởi tạo
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';

/**
 * Kiểm tra xem người dùng đã đăng nhập chưa và tài khoản có đang hoạt động hay không
 * Nếu chưa hoặc bị khóa/banned, chuyển hướng về trang đăng nhập
 */
function requireLogin() {
    $base = defined('BASE_PATH') ? BASE_PATH : '';
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $base . '/index.php?controller=auth&action=login');
        exit;
    }
    
    // Kiểm tra trực tiếp trong Database để đảm bảo tài khoản không bị cấm/khóa hoặc đổi vai trò giữa chừng
    $userModel = new User();
    $user = $userModel->getUserByIdWithRole($_SESSION['user_id']);
    
    if (!$user || $user['status'] !== 'active') {
        // Xóa sạch session và cookie
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        // Tạo session mới để lưu thông báo lỗi
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa hoặc bị đình chỉ hoạt động.';
        header('Location: ' . $base . '/index.php?controller=auth&action=login');
        exit;
    }

    // Nếu vai trò của người dùng đã thay đổi (ví dụ: được nâng cấp hoặc hạ cấp bởi Admin)
    $dbRoleName = $user['role_name'] ?? '';
    if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== $dbRoleName) {
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $dbRoleName;
        
        // Chuyển hướng người dùng về trang chủ để định tuyến lại vào Dashboard mới phù hợp
        header('Location: ' . $base . '/index.php');
        exit;
    }
}

/**
 * Kiểm tra xem người dùng có quyền (role) cụ thể hay không
 * Nếu không, hiển thị thông báo lỗi hoặc chuyển hướng
 * 
 * @param string $roleName Tên của role yêu cầu (ví dụ: 'admin', 'mangaka')
 */
function requireRole($roleName) {
    requireLogin();
    if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== $roleName) {
        http_response_code(403);
        echo "Access Denied: You do not have the required role to access this page.";
        exit;
    }
}
