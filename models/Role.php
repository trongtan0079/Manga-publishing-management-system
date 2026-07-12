<?php

namespace App\Models;

class Role
{
    public $id;
    public $name;
    public $description;

    public function __construct() {
    }

    public function getRolesWithUserCount() {
        $sql = "SELECT r.*, COUNT(u.user_id) as user_count FROM roles r LEFT JOIN users u ON r.role_id = u.role_id GROUP BY r.role_id, r.role_name, r.description ORDER BY r.role_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
