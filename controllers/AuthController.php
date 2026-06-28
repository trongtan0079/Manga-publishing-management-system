<?php
// controllers/AuthController.php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';



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
                // Kiểm tra trạng thái tài khoản
                if ($user['status'] !== 'active') {
                    $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa hoặc chưa được kích hoạt.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
                    exit;
                }

                // Bảo mật Session: Chống Session Fixation
                session_regenerate_id(true);

                // Lấy thông tin role
                $role = $this->roleModel->findById($user['role_id']);
                $roleName = $role ? $role['role_name'] : 'unknown';

                // Lưu thông tin vào session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
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

    /**
     * Action: Hiển thị trang Hồ sơ cá nhân
     * Cho phép tất cả user đã đăng nhập xem và chỉnh sửa thông tin cá nhân.
     */
    public function profile() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserByIdWithRole($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'Không tìm thấy thông tin tài khoản.';
            $this->redirectBasedOnRole($_SESSION['role_name']);
        }
        
        $pageTitle = 'Hồ sơ cá nhân';
        require_once __DIR__ . '/../views/shared/profile.php';
    }

    /**
     * Action: Xử lý cập nhật Hồ sơ cá nhân
     * Cho phép user tự cập nhật: Họ tên, Email, Đổi mật khẩu.
     * Không cho phép tự đổi username, role, hoặc status.
     */
    public function updateProfile() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // 1. Validate họ tên
        if (empty($fullName)) {
            $_SESSION['error'] = 'Họ và tên không được để trống.';
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
            exit;
        }

        if (mb_strlen($fullName) > 100) {
            $_SESSION['error'] = 'Họ và tên không được vượt quá 100 ký tự.';
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
            exit;
        }

        // 2. Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ.';
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
            exit;
        }

        // Kiểm tra email trùng lặp (loại trừ chính mình)
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['user_id'] != $userId) {
            $_SESSION['error'] = 'Email này đã được sử dụng bởi tài khoản khác.';
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
            exit;
        }

        // 3. Xử lý đổi mật khẩu (nếu có)
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $data = [
            'full_name' => $fullName,
            'email'     => $email,
        ];

        if (!empty($newPassword)) {
            // Phải nhập mật khẩu hiện tại để xác thực
            if (empty($currentPassword)) {
                $_SESSION['error'] = 'Vui lòng nhập mật khẩu hiện tại để xác nhận đổi mật khẩu.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
                exit;
            }

            // Kiểm tra mật khẩu hiện tại có đúng không
            $currentUser = $this->userModel->findById($userId);
            if (!password_verify($currentPassword, $currentUser['password_hash'])) {
                $_SESSION['error'] = 'Mật khẩu hiện tại không chính xác.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
                exit;
            }

            if (strlen($newPassword) < 6) {
                $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
                exit;
            }

            $data['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        try {
            $this->userModel->update($userId, $data);
            $_SESSION['success'] = 'Cập nhật hồ sơ cá nhân thành công!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi hệ thống khi cập nhật hồ sơ: ' . $e->getMessage();
        }

        header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=profile');
        exit;
    }
}
