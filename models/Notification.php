<?php
require_once __DIR__ . '/../core/Model.php';

class Notification extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'notifications';
        $this->primaryKey = 'notification_id';
    }

    /**
     * Lấy các thông báo chưa đọc của người dùng
     */
    public function findUnreadByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND is_read = FALSE ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đánh dấu thông báo là đã đọc
     */
    public function markAsRead($notificationId) {
        $sql = "UPDATE {$this->table} SET is_read = TRUE WHERE {$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $notificationId);
        return $stmt->execute();
    }
}
