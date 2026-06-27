<?php
// controllers/AuthController.php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/BaseController.php';

use App\Controllers\BaseController;


class AuthController extends BaseController {
    private $userModel;
    private $roleModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
        // Đảm bảo session đã được khởi tạo
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Action: Hiển thị trang đăng nhập (Login Page)
     * Nếu người dùng đã đăng nhập (Session tồn tại), tự động điều hướng (Redirect)
     * tới Dashboard tương ứng với Role của họ, ngăn việc truy cập lại trang login.
     */
    public function login() {
        // Kiểm tra và chuyển hướng nếu đã đăng nhập
        if ($this->checkAuth()) {
            $this->redirectBasedOnRole($_SESSION['role_name']);
        }
        
        // Lấy thông báo lỗi từ Session (ví dụ: Sai mật khẩu) để hiển thị ra View
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);
        
        // Gọi view
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Action: Xử lý logic Đăng nhập khi người dùng Submit Form
     * Hỗ trợ đăng nhập bằng cả Email hoặc Username.
     * Sử dụng password_verify() để đối chiếu mật khẩu đã băm (Hash).
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginId = trim($_POST['login_id'] ?? ''); // Người dùng có thể nhập Username hoặc Email
            $password = $_POST['password'] ?? '';

            // 1. Kiểm tra đầu vào rỗng
            if (empty($loginId) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ tên đăng nhập/email và mật khẩu.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
                exit;
            }

            $user = null;
            // 2. Phân loại định dạng: Nếu có chứa '@' thì query theo Email, ngược lại query theo Username
            if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
                $user = $this->userModel->findByEmail($loginId);
            } else {
                $user = $this->userModel->findByUsername($loginId);
            }

            // 3. Đối chiếu mật khẩu an toàn (Bcrypt verification)
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
     * Action: Xử lý đăng xuất (Logout)
     * Xóa toàn bộ dữ liệu Session và gỡ bỏ Cookie lưu vết của người dùng.
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Dọn dẹp toàn bộ mảng Session hiện tại
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
     * Helper: Hàm hỗ trợ tự động định tuyến (Routing) sau khi đăng nhập thành công.
     * Dựa vào Tên Role của user để chuyển họ về đúng màn hình Dashboard riêng biệt.
     */
    private function redirectBasedOnRole($roleName) {
        $url = '';
        switch ($roleName) {
            case 'admin':
                $url = '/index.php?controller=dashboard&action=admin';
                break;
            case 'mangaka':
                $url = '/index.php?controller=dashboard&action=mangaka';
                break;
            case 'assistant':
                $url = '/index.php?controller=dashboard&action=assistant';
                break;
            case 'editor':
                $url = '/index.php?controller=dashboard&action=editor';
                break;
            case 'board':
                $url = '/index.php?controller=dashboard&action=board';
                break;
            default:
                $url = '/index.php'; // Mặc định nếu không xác định được
                break;
        }
        header('Location: ' . BASE_PATH . $url);
        exit;
    }
}
