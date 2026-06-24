<?php

namespace App\Controllers;

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Notification.php';

use Notification;

class NotificationController extends BaseController
{
    private $notificationModel;

    public function __construct() {
        parent::__construct();
        \requireLogin();
        $this->notificationModel = new Notification();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        // Lấy tất cả thông báo của user (cả đã đọc và chưa đọc)
        $notifications = $this->notificationModel->findByUserId($userId);
        
        require_once __DIR__ . '/../views/shared/notifications.php';
    }

    public function markAsRead($id) {
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy thông báo.';
            header('Location: ' . BASE_PATH . '/index.php?controller=notification&action=index');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $notification = $this->notificationModel->findById($id);

        if (!$notification || $notification['user_id'] != $userId) {
            // Bảo mật: Không tồn tại hoặc không thuộc về user hiện tại
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền đánh dấu thông báo này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=notification&action=index');
            exit;
        }

        $this->notificationModel->markAsRead($id, $userId);
        
        // Trở về trang trước đó (referrer) hoặc trang notifications
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/index.php?controller=notification&action=index');
        header('Location: ' . $referer);
        exit;
    }

    public function markAllAsRead() {
        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);
        
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/index.php?controller=notification&action=index');
        header('Location: ' . $referer);
        exit;
    }
}
