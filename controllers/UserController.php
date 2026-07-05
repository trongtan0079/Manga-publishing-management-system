<?php


// Import các model và core cần thiết
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/SystemLog.php';


class UserController extends BaseController
{
    private $userModel;
    private $roleModel;

    public function __construct() {
        parent::__construct();
        // Chỉ cho phép Admin truy cập toàn bộ các chức năng trong controller này
        requireRole('admin');
        
        // Khởi tạo các Model để tương tác với Database
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * Hiển thị danh sách tất cả người dùng kèm tìm kiếm và phân trang
     */
    public function index() {
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 10; // 10 bản ghi mỗi trang
        $offset = ($page - 1) * $limit;
        
        $result = $this->userModel->getPaginatedUsers($search, $status, $limit, $offset);
        $users = $result['users'];
        $totalUsers = $result['total'];
        $totalPages = ceil($totalUsers / $limit);
        
        // Gọi view hiển thị danh sách
        require_once __DIR__ . '/../views/admin/users.php';
    }

    /**
     * Hiển thị form thêm mới người dùng
     */
    public function create() {
        // Lấy danh sách roles để hiển thị trong thẻ <select>
        $roles = $this->roleModel->findAll();
        
        // Gọi view form thêm mới
        require_once __DIR__ . '/../views/admin/user_create.php';
    }

    /**
     * Xử lý lưu thông tin người dùng mới vào Database
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Kiểm tra trường rỗng
            if (empty($username) || empty($fullName)) {
                $_SESSION['error'] = "Lỗi: Tên đăng nhập và Họ tên không được để trống!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=create');
                exit;
            }

            // 1. Kiểm tra trùng lặp Username
            if ($this->userModel->findByUsername($username)) {
                $_SESSION['error'] = "Lỗi: Username '{$username}' đã tồn tại trong hệ thống!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=create');
                exit;
            }

            // 2. Kiểm tra định dạng Email và trùng lặp
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Lỗi: Email không hợp lệ!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=create');
                exit;
            }
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = "Lỗi: Email '{$email}' đã được đăng ký!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=create');
                exit;
            }

            // 3. Kiểm tra Role hợp lệ
            $role_id = $_POST['role_id'] ?? '';
            if (!$this->roleModel->findById($role_id)) {
                $_SESSION['error'] = "Lỗi: Vai trò (Role) không tồn tại!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=create');
                exit;
            }

            // Thu thập dữ liệu từ form gửi lên
            $data = [
                'username'  => $username,
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email'     => $email,
                'role_id'   => $role_id,
                'status'    => in_array($_POST['status'] ?? '', ['active', 'inactive', 'banned']) ? $_POST['status'] : 'active'
            ];
            
            // Xử lý mật khẩu: nếu có nhập thì kiểm tra độ dài và băm (hash), nếu không thì dùng mật khẩu mặc định
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $_SESSION['error'] = "Lỗi: Mật khẩu phải có ít nhất 6 ký tự!";
                    header('Location: ' . BASE_PATH . '/index.php?controller=user&action=create');
                    exit;
                }
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                $data['password_hash'] = password_hash('password123', PASSWORD_DEFAULT);
            }

            try {
                // Thực hiện thêm mới vào DB
                $this->userModel->insert($data);
                
                // Ghi nhật ký hoạt động
                SystemLog::logAction($_SESSION['user_id'], 'Tạo người dùng', "Đã tạo thành công tài khoản '{$username}' (Họ tên: '{$data['full_name']}', Email: '{$data['email']}')");
                
                $_SESSION['success'] = "Tạo người dùng '{$username}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi tạo người dùng: " . $e->getMessage();
            }
            
            // Quay về trang danh sách
            header('Location: ' . BASE_PATH . '/index.php?controller=user&action=index');
            exit;
        }
    }

    /**
     * Hiển thị form chỉnh sửa thông tin người dùng
     * @param int $id ID của người dùng cần sửa
     */
    public function edit($id) {
        // Lấy thông tin người dùng hiện tại
        $user = $this->userModel->getUserByIdWithRole($id);
        
        // Lấy danh sách roles cho thẻ <select>
        $roles = $this->roleModel->findAll();
        
        // Nếu không tìm thấy user, báo lỗi
        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy người dùng (ID: {$id}).";
            header('Location: ' . BASE_PATH . '/index.php?controller=user&action=index');
            exit;
        }
        
        // Gọi view form chỉnh sửa
        require_once __DIR__ . '/../views/admin/user_edit.php';
    }

    /**
     * Xử lý cập nhật thông tin người dùng vào Database
     * @param int $id ID của người dùng cần cập nhật
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Kiểm tra trường rỗng
            if (empty($username) || empty($fullName)) {
                $_SESSION['error'] = "Lỗi: Tên đăng nhập và Họ tên không được để trống!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                exit;
            }

            // 1. Kiểm tra trùng lặp Username (loại trừ chính user hiện tại đang sửa)
            $existingUserByUsername = $this->userModel->findByUsername($username);
            if ($existingUserByUsername && $existingUserByUsername['user_id'] != $id) {
                $_SESSION['error'] = "Lỗi: Username '{$username}' đã được sử dụng bởi người dùng khác!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                exit;
            }

            // 2. Kiểm tra định dạng Email và trùng lặp (loại trừ chính user hiện tại)
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Lỗi: Email không hợp lệ!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                exit;
            }
            $existingUserByEmail = $this->userModel->findByEmail($email);
            if ($existingUserByEmail && $existingUserByEmail['user_id'] != $id) {
                $_SESSION['error'] = "Lỗi: Email '{$email}' đã được sử dụng bởi người dùng khác!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                exit;
            }

            // 3. Kiểm tra Role hợp lệ
            $role_id = $_POST['role_id'] ?? '';
            if (!$this->roleModel->findById($role_id)) {
                $_SESSION['error'] = "Lỗi: Vai trò (Role) không tồn tại!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                exit;
            }

            // Ngăn chặn admin tự khóa/đình chỉ bản thân hoặc tự đổi vai trò của mình để tránh bị lockout khỏi hệ thống
            if ($id == $_SESSION['user_id']) {
                $selectedRole = $this->roleModel->findById($role_id);
                if (!$selectedRole || strtolower($selectedRole['role_name']) !== 'admin') {
                    $_SESSION['error'] = "Lỗi: Bạn không thể tự thay đổi vai trò Admin của chính mình sang vai trò khác!";
                    header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                    exit;
                }
                if (isset($_POST['status']) && $_POST['status'] !== 'active') {
                    $_SESSION['error'] = "Lỗi: Bạn không thể tự khóa hoặc đình chỉ tài khoản đang đăng nhập của chính mình!";
                    header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                    exit;
                }
            }

            // Thu thập dữ liệu từ form
            $data = [
                'username'  => $username,
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email'     => $email,
                'role_id'   => $role_id,
                'status'    => in_array($_POST['status'] ?? '', ['active', 'inactive', 'banned']) ? $_POST['status'] : 'active'
            ];
            
            // Nếu admin có nhập mật khẩu mới thì mới cập nhật password_hash
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $_SESSION['error'] = "Lỗi: Mật khẩu mới phải có ít nhất 6 ký tự!";
                    header('Location: ' . BASE_PATH . '/index.php?controller=user&action=edit&id=' . $id);
                    exit;
                }
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            try {
                // Thực hiện update trong DB
                $this->userModel->update($id, $data);
                
                // Ghi nhật ký hoạt động
                SystemLog::logAction($_SESSION['user_id'], 'Cập nhật người dùng', "Đã cập nhật thông tin tài khoản '{$username}' (ID: {$id}, Trạng thái: '{$data['status']}')");
                
                $_SESSION['success'] = "Cập nhật người dùng '{$username}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi cập nhật: " . $e->getMessage();
            }
            
            // Quay về trang danh sách
            header('Location: ' . BASE_PATH . '/index.php?controller=user&action=index');
            exit;
        }
    }

    /**
     * Hiển thị chi tiết một người dùng
     * @param int $id ID của người dùng
     */
    public function show($id) {
        $user = $this->userModel->getUserByIdWithRole($id);
        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy người dùng (ID: {$id}).";
            header('Location: ' . BASE_PATH . '/index.php?controller=user&action=index');
            exit;
        }
        
        // Gọi view hiển thị chi tiết
        require_once __DIR__ . '/../views/admin/user_detail.php';
    }

    /**
     * Xử lý xóa một người dùng khỏi Database
     * @param int $id ID của người dùng cần xóa
     */
    public function delete($id) {
        // Chỉ chấp nhận request POST để xóa, đảm bảo an toàn
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không thể xóa chính tài khoản của mình!";
                header('Location: ' . BASE_PATH . '/index.php?controller=user&action=index');
                exit;
            }

            try {
                $user = $this->userModel->findById($id);
                $targetUsername = $user ? $user['username'] : 'ID: ' . $id;
                
                $this->userModel->delete($id);
                
                // Ghi nhật ký hoạt động
                SystemLog::logAction($_SESSION['user_id'], 'Xóa người dùng', "Đã xóa tài khoản '{$targetUsername}' (ID: {$id}) khỏi hệ thống");
                
                $_SESSION['success'] = "Đã xóa người dùng thành công!";
            } catch (PDOException $e) {
                // Bắt lỗi nếu có ràng buộc khóa ngoại (VD: User đang giữ Role hoặc liên quan tới bảng khác)
                $_SESSION['error'] = "Không thể xóa người dùng này vì dữ liệu đang được liên kết. Lỗi: " . $e->getMessage();
            }
            
            // Xóa xong quay về trang danh sách
            header('Location: ' . BASE_PATH . '/index.php?controller=user&action=index');
            exit;
        }
    }
    /**
     * Hiển thị danh sách vai trò (Read Only)
     * Chỉ hiển thị thông tin, không cho phép CRUD.
     */
    public function roles() {
        $roles = $this->roleModel->findAll();
        
        // Đếm số user thuộc mỗi role
        $conn = $this->userModel->getConnection();
        $stmt = $conn->prepare("SELECT r.*, COUNT(u.user_id) as user_count FROM roles r LEFT JOIN users u ON r.role_id = u.role_id GROUP BY r.role_id, r.role_name, r.description ORDER BY r.role_id");
        $stmt->execute();
        $rolesWithCount = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/admin/roles.php';
    }
}
