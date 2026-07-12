<?php
require_once __DIR__ . '/../core/Model.php';

class SystemLog extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'system_logs';
        $this->primaryKey = 'log_id';
        $this->createTableIfNotExists();
    }

    /**
     * Tự động tạo bảng system_logs nếu chưa tồn tại
     */
    private function createTableIfNotExists() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS system_logs (
                log_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                action VARCHAR(255) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Failed to create system_logs table: " . $e->getMessage());
        }
    }

    /**
     * Ghi lại một hành động hoạt động hệ thống
     */
    public static function logAction($userId, $action, $details = '') {
        try {
            $log = new self();
            
            // Lấy địa chỉ IP
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            
            return $log->insert([
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $ipAddress
            ]);
        } catch (Exception $e) {
            error_log("Failed to write system log: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách log có phân trang kèm theo thông tin User thực hiện
     */
    public function getPaginatedLogs($limit, $offset) {
        $sql = "SELECT l.*, u.full_name, u.username, r.role_name
                FROM {$this->table} l
                LEFT JOIN users u ON l.user_id = u.user_id
                LEFT JOIN roles r ON u.role_id = r.role_id
                ORDER BY l.log_id DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
