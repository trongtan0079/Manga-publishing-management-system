<?php
// controllers/AuthController.php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class AuthController {
    private $userModel;
    private $roleModel;

    public function __construct() {
        $this->userModel = new User();
        $this->roleModel = new Role();
        // Đảm bảo session đã được khởi tạo
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function login() {
        // Nếu đã đăng nhập, chuyển hướng theo role
        if ($this->checkAuth()) {
            $this->redirectBasedOnRole($_SESSION['role_name']);
        }
        
        // Lấy thông báo lỗi nếu có
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);
        
        // Gọi view
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Xử lý đăng nhập
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginId = trim($_POST['login_id'] ?? ''); // Có thể là username hoặc email
            $password = $_POST['password'] ?? '';

            if (empty($loginId) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ tên đăng nhập/email và mật khẩu.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
                exit;
            }

            $user = null;
            // Kiểm tra xem input là email hay username
            if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
                $user = $this->userModel->findByEmail($loginId);
            } else {
                $user = $this->userModel->findByUsername($loginId);
            }

            // Kiểm tra mật khẩu
            if ($user && password_verify($password, $user['password_hash'])) {
                // Lấy thông tin role
                $role = $this->roleModel->findById($user['role_id']);
                $roleName = $role ? $role['role_name'] : 'unknown';

                // Lưu thông tin vào session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $roleName;

                // Chuyển hướng
                $this->redirectBasedOnRole($roleName);
            } else {
                $_SESSION['error'] = 'Tên đăng nhập/email hoặc mật khẩu không chính xác.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
                exit;
            }
        } else {
            // Nếu không phải POST, quay về trang đăng nhập
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
            exit;
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Xóa tất cả các biến session
        $_SESSION = array();

        // Xóa cookie của session nếu có
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Hủy session
        session_destroy();
        
        // Chuyển hướng về trang đăng nhập
        header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
        exit;
    }

    /**
     * Kiểm tra trạng thái đăng nhập
     */
    public function checkAuth() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function getCurrentUser() {
        if ($this->checkAuth()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role_id' => $_SESSION['role_id'],
                'role_name' => $_SESSION['role_name']
            ];
        }
        return null;
    }

    /**
     * Hàm hỗ trợ chuyển hướng theo role
     */
    private function redirectBasedOnRole($roleName) {
        $url = '';
        switch ($roleName) {
            case 'admin':
                $url = '/views/admin/dashboard.php';
                break;
            case 'mangaka':
                $url = '/views/mangaka/dashboard.php';
                break;
            case 'assistant':
                $url = '/views/assistant/dashboard.php';
                break;
            case 'editor':
                $url = '/views/editor/dashboard.php';
                break;
            case 'board':
                $url = '/views/board/dashboard.php';
                break;
            default:
                $url = '/index.php'; // Mặc định nếu không xác định được
                break;
        }
        header('Location: ' . BASE_PATH . $url);
        exit;
    }
}
