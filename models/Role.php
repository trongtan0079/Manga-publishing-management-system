<?php
require_once __DIR__ . '/../core/Model.php';

class Role extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'roles';
        $this->primaryKey = 'role_id';
    }

    /**
     * Lấy thông tin Role bằng tên role (ví dụ: 'admin', 'mangaka')
     */
    public function findByRoleName($roleName) {
        $sql = "SELECT * FROM {$this->table} WHERE role_name = :role_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':role_name', $roleName);
        $stmt->execute();
        return $stmt->fetch();
    }
}
