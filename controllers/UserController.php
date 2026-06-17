<?php

namespace App\Controllers;

// Import các model và core cần thiết
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

use User;
use Role;

class UserController
{
    private $userModel;
    private $roleModel;

    public function __construct() {
        // Chỉ cho phép Admin truy cập toàn bộ các chức năng trong controller này
        requireRole('admin');
        
        // Khởi tạo các Model để tương tác với Database
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * Hiển thị danh sách tất cả người dùng
     */
    public function index() {
        // Lấy toàn bộ người dùng (kèm theo tên role từ bảng roles)
        $users = $this->userModel->getAllUsersWithRole();
        
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
            // Thu thập dữ liệu từ form gửi lên
            $data = [
                'username'  => $_POST['username'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'role_id'   => $_POST['role_id'] ?? '',
                'status'    => $_POST['status'] ?? 'active'
            ];
            
            // Xử lý mật khẩu: nếu có nhập thì băm (hash), nếu không thì dùng mật khẩu mặc định
            if (!empty($_POST['password'])) {
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                $data['password_hash'] = password_hash('password123', PASSWORD_DEFAULT);
            }

            // Thực hiện thêm mới vào DB
            $this->userModel->insert($data);
            
            // Quay về trang danh sách
            header('Location: /index.php?controller=user&action=index');
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
            die("User not found");
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
            // Thu thập dữ liệu từ form
            $data = [
                'username'  => $_POST['username'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'role_id'   => $_POST['role_id'] ?? '',
                'status'    => $_POST['status'] ?? 'active'
            ];
            
            // Nếu admin có nhập mật khẩu mới thì mới cập nhật password_hash
            if (!empty($_POST['password'])) {
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Thực hiện update trong DB
            $this->userModel->update($id, $data);
            
            // Quay về trang danh sách
            header('Location: /index.php?controller=user&action=index');
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
            die("User not found");
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
            $this->userModel->delete($id);
            
            // Xóa xong quay về trang danh sách
            header('Location: /index.php?controller=user&action=index');
            exit;
        }
    }
}
