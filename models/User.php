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

    /**
     * Lấy danh sách người dùng theo role (ví dụ: 'assistant') đang active
     */
    public function findByRoleName($roleName) {
        $sql = "SELECT u.* 
                FROM {$this->table} u 
                JOIN roles r ON u.role_id = r.role_id
                WHERE r.role_name = :role_name AND u.status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':role_name', $roleName);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách người dùng phân trang và tìm kiếm kèm theo đếm tổng số bản ghi khớp điều kiện
     */
    public function getPaginatedUsers($search, $status, $limit, $offset) {
        $whereClauses = [];
        $params = [];

        if (!empty($search)) {
            $whereClauses[] = "(u.username LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search OR r.role_name LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($status)) {
            $whereClauses[] = "u.status = :status";
            $params['status'] = $status;
        }

        $whereSql = "";
        if (!empty($whereClauses)) {
            $whereSql = "WHERE " . implode(" AND ", $whereClauses);
        }

        // 1. Đếm tổng số bản ghi
        $countSql = "SELECT COUNT(*) FROM {$this->table} u LEFT JOIN roles r ON u.role_id = r.role_id {$whereSql}";
        $countStmt = $this->conn->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue(':' . $key, $val);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // 2. Lấy dữ liệu phân trang
        $dataSql = "SELECT u.*, r.role_name 
                    FROM {$this->table} u 
                    LEFT JOIN roles r ON u.role_id = r.role_id
                    {$whereSql}
                    ORDER BY u.user_id DESC 
                    LIMIT :limit OFFSET :offset";
        $dataStmt = $this->conn->prepare($dataSql);
        foreach ($params as $key => $val) {
            $dataStmt->bindValue(':' . $key, $val);
        }
        $dataStmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $users = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'users' => $users,
            'total' => $total
        ];
    }

    public function getRoleStatistics() {
        $sql = "SELECT r.role_name, COUNT(u.user_id) as user_count FROM roles r LEFT JOIN users u ON r.role_id = u.role_id GROUP BY r.role_id, r.role_name ORDER BY r.role_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Hạ chức các Trưởng ban khác để đảm bảo chỉ có tối đa 1 Trưởng ban duy nhất
     */
    public function demoteOtherHeads($excludeUserId) {
        $sql = "UPDATE {$this->table} SET is_head_board = 0 WHERE user_id != :exclude_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':exclude_id', $excludeUserId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
