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

    /**
     * Lấy toàn bộ thông báo hệ thống kèm thông tin người nhận (dành cho Admin)
     */
    public function findAllWithUser() {
        $sql = "SELECT n.*, u.username, u.full_name 
                FROM {$this->table} n 
                LEFT JOIN users u ON n.user_id = u.user_id 
                ORDER BY n.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết cấu hình hiển thị (icon, màu sắc, label, gradient) của từng loại thông báo.
     * Đảm bảo tính nhất quán giữa danh sách, card và modal popup.
     */
    public static function getTypeDetails($type) {
        $details = [
            'task_assigned' => [
                'icon' => 'fa-clipboard-list',
                'color' => '#6366f1',
                'bg_gradient' => 'linear-gradient(135deg, #818cf8 0%, #6366f1 100%)',
                'bg_subtle' => 'rgba(99, 102, 241, 0.1)',
                'label' => 'Nhiệm vụ mới'
            ],
            'submission_submitted' => [
                'icon' => 'fa-paper-plane',
                'color' => '#0ea5e9',
                'bg_gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)',
                'bg_subtle' => 'rgba(14, 165, 233, 0.1)',
                'label' => 'Bản thảo đề xuất'
            ],
            'chapter_submitted' => [
                'icon' => 'fa-file-medical',
                'color' => '#06b6d4',
                'bg_gradient' => 'linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%)',
                'bg_subtle' => 'rgba(6, 182, 212, 0.1)',
                'label' => 'Nộp chương mới'
            ],
            'review_created' => [
                'icon' => 'fa-comments',
                'color' => '#d946ef',
                'bg_gradient' => 'linear-gradient(135deg, #f0abfc 0%, #d946ef 100%)',
                'bg_subtle' => 'rgba(217, 70, 239, 0.1)',
                'label' => 'Nhận xét mới'
            ],
            'submission_approved' => [
                'icon' => 'fa-award',
                'color' => '#10b981',
                'bg_gradient' => 'linear-gradient(135deg, #34d399 0%, #10b981 100%)',
                'bg_subtle' => 'rgba(16, 185, 129, 0.1)',
                'label' => 'Phê duyệt bản thảo'
            ],
            'submission_rejected' => [
                'icon' => 'fa-ban',
                'color' => '#f43f5e',
                'bg_gradient' => 'linear-gradient(135deg, #fb7185 0%, #f43f5e 100%)',
                'bg_subtle' => 'rgba(244, 63, 94, 0.1)',
                'label' => 'Từ chối bản thảo'
            ],
            'ranking_published' => [
                'icon' => 'fa-trophy',
                'color' => '#eab308',
                'bg_gradient' => 'linear-gradient(135deg, #fde047 0%, #eab308 100%)',
                'bg_subtle' => 'rgba(234, 179, 8, 0.1)',
                'label' => 'Bảng xếp hạng'
            ],
            'series_warning' => [
                'icon' => 'fa-triangle-exclamation',
                'color' => '#f97316',
                'bg_gradient' => 'linear-gradient(135deg, #fdba74 0%, #f97316 100%)',
                'bg_subtle' => 'rgba(249, 115, 22, 0.1)',
                'label' => 'Cảnh báo bộ truyện'
            ],
            'series_completed' => [
                'icon' => 'fa-flag-checkered',
                'color' => '#059669',
                'bg_gradient' => 'linear-gradient(135deg, #34d399 0%, #059669 100%)',
                'bg_subtle' => 'rgba(5, 150, 105, 0.1)',
                'label' => 'Bộ truyện hoàn thành'
            ],
            'series_submitted' => [
                'icon' => 'fa-folder-plus',
                'color' => '#3b82f6',
                'bg_gradient' => 'linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%)',
                'bg_subtle' => 'rgba(59, 130, 246, 0.1)',
                'label' => 'Đề xuất truyện mới'
            ],
            'chapter_published' => [
                'icon' => 'fa-rocket',
                'color' => '#8b5cf6',
                'bg_gradient' => 'linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%)',
                'bg_subtle' => 'rgba(139, 92, 246, 0.1)',
                'label' => 'Chương đã xuất bản'
            ],
            'chapter_approved' => [
                'icon' => 'fa-check-circle',
                'color' => '#10b981',
                'bg_gradient' => 'linear-gradient(135deg, #34d399 0%, #10b981 100%)',
                'bg_subtle' => 'rgba(16, 185, 129, 0.1)',
                'label' => 'Chương được duyệt'
            ],
            'default' => [
                'icon' => 'fa-bell',
                'color' => '#6b7280',
                'bg_gradient' => 'linear-gradient(135deg, #9ca3af 0%, #6b7280 100%)',
                'bg_subtle' => 'rgba(107, 114, 128, 0.1)',
                'label' => 'Thông báo mới'
            ]
        ];

        return $details[$type] ?? $details['default'];
    }
}

