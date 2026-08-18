<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Notification.php';


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
        $role = $_SESSION['role_name'] ?? '';
        
        // Nếu là Admin, lấy tất cả thông báo hệ thống
        if ($role === 'admin') {
            $notifications = $this->notificationModel->findAllWithUser();
        } else {
            $notifications = $this->notificationModel->findByUserId($userId);
        }
        
        require_once __DIR__ . '/../views/shared/notifications.php';
    }

    public function markAsRead($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không được phép.';
            $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/index.php?controller=notification&action=index');
            header('Location: ' . $referer);
            exit;
        }

        $this->validateCsrf();

        $id = (int)$id;
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không được phép.';
            $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/index.php?controller=notification&action=index');
            header('Location: ' . $referer);
            exit;
        }

        $this->validateCsrf();

        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);
        
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/index.php?controller=notification&action=index');
        header('Location: ' . $referer);
        exit;
    }

    public function readAndRedirect() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role_name'] ?? '';
        $notification = $this->notificationModel->findById($id);

        if (!$notification) {
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        // Nếu là admin thì được xem tất cả, ngược lại kiểm tra sở hữu
        if ($role !== 'admin' && $notification['user_id'] != $userId) {
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền truy cập thông báo này.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        // Chỉ đánh dấu đã đọc nếu là thông báo của chính mình
        if ($notification['user_id'] == $userId) {
            $this->notificationModel->markAsRead($id, $userId);
        }

        // Xác định trang chuyển hướng dựa trên loại thông báo (type) và tài nguyên liên quan (related_id)
        $role = $_SESSION['role_name'] ?? '';
        $redirectUrl = BASE_PATH . '/index.php';
        $relatedId = isset($notification['related_id']) && $notification['related_id'] > 0 ? (int)$notification['related_id'] : 0;

        switch ($notification['type']) {
            case 'task_assigned':
                if ($role === 'assistant') {
                    $redirectUrl = BASE_PATH . '/index.php?controller=task&action=index';
                } elseif ($role === 'mangaka') {
                    if ($relatedId > 0) {
                        require_once __DIR__ . '/../models/Task.php';
                        $taskModel = new \Task();
                        $task = $taskModel->findById($relatedId);
                        if ($task) {
                            $redirectUrl = BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id'];
                        } else {
                            $redirectUrl = BASE_PATH . '/index.php?controller=task&action=index';
                        }
                    } else {
                        $redirectUrl = BASE_PATH . '/index.php?controller=task&action=index';
                    }
                } elseif ($role === 'editor') {
                    $redirectUrl = BASE_PATH . '/index.php?controller=dashboard&action=progress';
                }
                break;
            case 'chapter_submitted':
            case 'submission_submitted':
                if ($relatedId > 0) {
                    $redirectUrl = BASE_PATH . '/index.php?controller=submission&action=show&id=' . $relatedId;
                } elseif ($role === 'editor') {
                    $redirectUrl = BASE_PATH . '/index.php?controller=review&action=index';
                } else {
                    $redirectUrl = BASE_PATH . '/index.php?controller=submission&action=index';
                }
                break;
            case 'review_created':
            case 'submission_approved':
            case 'submission_rejected':
                if ($relatedId > 0) {
                    $redirectUrl = BASE_PATH . '/index.php?controller=submission&action=show&id=' . $relatedId;
                } else {
                    $redirectUrl = BASE_PATH . '/index.php?controller=submission&action=index';
                }
                break;
            case 'ranking_published':
                $redirectUrl = BASE_PATH . '/index.php?controller=seriesRanking&action=index';
                break;
            case 'chapter_published':
                if ($relatedId > 0) {
                    $redirectUrl = BASE_PATH . '/index.php?controller=chapter&action=show&id=' . $relatedId;
                } else {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=index';
                }
                break;
            case 'series_warning':
                if ($relatedId > 0) {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=show&id=' . $relatedId;
                } else {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=index';
                }
                break;
            case 'series_submitted':
            case 'chapter_approved':
                if ($role === 'board') {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=publish';
                } elseif ($relatedId > 0) {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=show&id=' . $relatedId;
                } else {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=index';
                }
                break;
            case 'series_completed':
                if ($relatedId > 0) {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=show&id=' . $relatedId;
                } else {
                    $redirectUrl = BASE_PATH . '/index.php?controller=series&action=index';
                }
                break;
            default:
                $redirectUrl = BASE_PATH . '/index.php?controller=notification&action=index';
                break;
        }

        header('Location: ' . $redirectUrl);
        exit;
    }
}

