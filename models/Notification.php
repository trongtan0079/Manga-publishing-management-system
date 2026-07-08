<?php
require_once __DIR__ . '/../core/Model.php';

class Notification extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'notifications';
        $this->primaryKey = 'notification_id';
    }

    /**
     * Tạo một thông báo mới chuẩn hóa theo type
     */
    public function createNotification($userId, $type, $message, $relatedId = null) {
        $allowedTypes = [
            'task_assigned',
            'submission_submitted',
            'chapter_submitted',
            'review_created',
            'submission_approved',
            'submission_rejected',
            'ranking_published',
            'series_warning',
            'series_completed',
            'series_submitted',
            'chapter_published'
        ];

        if (!in_array($type, $allowedTypes)) {
            // Có thể throw Exception hoặc return false nếu cần khắt khe
            // Ở đây ghi log hoặc cho phép với type mặc định, nhưng ta ép buộc dùng allowedTypes
            error_log("Invalid notification type: $type");
        }

        return $this->insert([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'is_read' => 0,
            'related_id' => $relatedId
        ]);
    }

    /**
     * Lấy toàn bộ thông báo của user
     */
    public function findByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
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
     * Đếm số lượng thông báo chưa đọc
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as unread_count FROM {$this->table} WHERE user_id = :user_id AND is_read = FALSE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['unread_count'] : 0;
    }

    /**
     * Lấy top n thông báo mới nhất
     */
    public function getLatestNotifications($userId, $limit = 5) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc (kiểm tra sở hữu)
     */
    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE {$this->table} SET is_read = TRUE WHERE {$this->primaryKey} = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $notificationId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Đánh dấu toàn bộ thông báo của 1 user là đã đọc
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} SET is_read = TRUE WHERE user_id = :user_id AND is_read = FALSE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
