<?php
require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
        $this->primaryKey = 'user_id';
    }

    /**
     * Tìm người dùng theo email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tìm người dùng theo username
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }
    /**
     * Lấy tất cả người dùng kèm theo tên role
     */
    public function getAllUsersWithRole() {
        $sql = "SELECT u.*, r.role_name 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.role_id
                ORDER BY u.user_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy 1 người dùng theo ID kèm theo tên role
     */
    public function getUserByIdWithRole($id) {
        $sql = "SELECT u.*, r.role_name 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.role_id
                WHERE u.user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
