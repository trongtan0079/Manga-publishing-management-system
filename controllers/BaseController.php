<?php

require_once __DIR__ . '/../models/Notification.php';

/**
 * Class BaseController
 * 
 * Controller cơ sở cho toàn bộ hệ thống. Tất cả các Controller khác sẽ kế thừa từ class này.
 * Đảm nhiệm việc tải dữ liệu dùng chung (như số lượng và danh sách thông báo chưa đọc của người dùng).
 */
class BaseController
{
    // Số lượng thông báo chưa đọc của người dùng hiện tại
    public $unreadCount = 0;

    // Danh sách các thông báo mới nhất của người dùng hiện tại
    public $latestNotifications = [];

    /**
     * Phương thức khởi tạo BaseController.
     * Kiểm tra trạng thái đăng nhập để lấy thông tin thông báo chưa đọc.
     */
    public function __construct() {
        if (isset($_SESSION['user_id'])) {
            $notificationModel = new \Notification();
            // Lấy số lượng thông báo chưa đọc
            $this->unreadCount = $notificationModel->getUnreadCount($_SESSION['user_id']);
            // Lấy danh sách các thông báo mới nhất
            $this->latestNotifications = $notificationModel->getLatestNotifications($_SESSION['user_id']);
        }
    }
}

