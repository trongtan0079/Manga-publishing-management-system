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
}
