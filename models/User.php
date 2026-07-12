<?php

namespace App\Models;

class User
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $role_id;
    public $created_at;
    public $updated_at;

    public function __construct() {
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
}
