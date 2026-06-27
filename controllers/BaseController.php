<?php



require_once __DIR__ . '/../models/Notification.php';

class BaseController
{
    public $unreadCount = 0;
    public $latestNotifications = [];

    public function __construct() {
        if (isset($_SESSION['user_id'])) {
            $notificationModel = new \Notification();
            $this->unreadCount = $notificationModel->getUnreadCount($_SESSION['user_id']);
            $this->latestNotifications = $notificationModel->getLatestNotifications($_SESSION['user_id']);
        }
    }
}
