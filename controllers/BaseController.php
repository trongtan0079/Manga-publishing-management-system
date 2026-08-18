<?php

require_once __DIR__ . '/../core/Csrf.php';
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

    /**
     * Helper to render HTML badge for Chapter/Page status
     */
    public function getStatusBadge($status) {
        $badgeClass = 'bg-secondary';
        $statusLabel = $status;
        switch ($status) {
            case 'drafting':
                $badgeClass = 'bg-secondary';
                $statusLabel = 'Phác thảo Kịch bản (Storyboard)';
                break;
            case 'drawing':
                $badgeClass = 'bg-primary';
                $statusLabel = 'Đang vẽ Chi tiết';
                break;
            case 'reviewing_draft':
                $badgeClass = 'bg-warning text-dark';
                $statusLabel = 'Chờ duyệt Kịch bản';
                break;
            case 'reviewing_final':
                $badgeClass = 'bg-warning text-dark';
                $statusLabel = 'Chờ duyệt Bản vẽ';
                break;
            case 'approved':
                $badgeClass = 'bg-info text-dark';
                $statusLabel = 'Đã duyệt phát hành';
                break;
            case 'published':
                $badgeClass = 'bg-success';
                $statusLabel = 'Đã xuất bản';
                break;
        }
        return "<span class=\"badge {$badgeClass}\">{$statusLabel}</span>";
    }

    /**
     * Helper to render HTML badge for Page status
     */
    public function getPageStatusBadge($page, $chapterStatus = null) {
        $status = is_array($page) ? $page['status'] : $page;
        $pageId = is_array($page) ? ($page['page_id'] ?? null) : null;
        $oldImageUrl = is_array($page) ? ($page['old_image_url'] ?? '') : '';
        $badgeClass = 'bg-secondary';
        $statusLabel = $status;
        switch ($status) {
            case 'drafting':
                $badgeClass = 'bg-secondary';
                $statusLabel = 'Bản nháp (Drafting)';
                break;
            case 'drawing':
                // Kiểm tra xem tất cả các task đã hoàn thành chưa (để gợi ý nộp bản hoàn chỉnh)
                $isPendingFinalSubmit = false;
                if ($pageId && $oldImageUrl === 'no_genko') {
                    require_once __DIR__ . '/../models/Task.php';
                    $taskModel = new \Task();
                    $tasks = $taskModel->findByPageId($pageId);
                    if (!empty($tasks)) {
                        $allTasksDone = true;
                        foreach ($tasks as $t) {
                            if ($t['status'] !== 'completed') {
                                $allTasksDone = false;
                                break;
                            }
                        }
                        if ($allTasksDone) {
                            $isPendingFinalSubmit = true;
                        }
                    }
                }
                
                if ($isPendingFinalSubmit) {
                    $badgeClass = 'bg-warning text-dark';
                    $statusLabel = 'Chờ nộp bản hoàn chỉnh';
                } else {
                    $badgeClass = 'bg-primary';
                    $statusLabel = 'Đang vẽ (Drawing)';
                }
                break;
            case 'reviewing_draft':
                $badgeClass = 'bg-warning text-dark';
                $statusLabel = 'Chờ duyệt Kịch bản';
                break;
            case 'reviewing_final':
                $badgeClass = 'bg-warning text-dark';
                $statusLabel = 'Chờ duyệt Bản vẽ';
                break;
            case 'approved':
                if ($chapterStatus && in_array($chapterStatus, ['drafting', 'reviewing_draft'])) {
                    $badgeClass = 'bg-success text-white';
                    $statusLabel = 'Kịch bản xong';
                } else {
                    if ($chapterStatus === 'approved' || $chapterStatus === 'published') {
                        $badgeClass = 'bg-info text-dark';
                        $statusLabel = 'Đã duyệt phát hành';
                    } elseif ($chapterStatus === 'drawing') {
                        $badgeClass = 'bg-success text-white';
                        $statusLabel = 'Hoàn thành (Chờ gửi Editor)';
                    } elseif ($chapterStatus === 'reviewing_final' || $chapterStatus === 'reviewing') {
                        $badgeClass = 'bg-warning text-dark';
                        $statusLabel = 'Đã gửi (Chờ Editor duyệt)';
                    } else {
                        $badgeClass = 'bg-success text-white';
                        $statusLabel = 'Bản vẽ hoàn thiện';
                    }
                }
                break;
            case 'published':
                $badgeClass = 'bg-success';
                $statusLabel = 'Đã xuất bản';
                break;
        }
        return "<span class=\"badge {$badgeClass}\">{$statusLabel}</span>";
    }

    /**
     * Helper to render HTML badge for Series status
     */
    public function getSeriesStatusBadge($series) {
        $status = is_array($series) ? $series['status'] : $series;
        $publishType = is_array($series) ? ($series['publish_type'] ?? '') : '';
        $editorId = is_array($series) ? ($series['editor_id'] ?? null) : null;
        
        $badgeClass = 'bg-secondary';
        $statusLabel = $status;
        switch ($status) {
            case 'planning':
                if ($publishType === 'draft') {
                    $badgeClass = 'bg-secondary';
                    $statusLabel = 'Nháp (Chưa nộp)';
                } else {
                    $badgeClass = 'bg-info text-dark';
                    $statusLabel = 'Chờ phê duyệt';
                }
                break;
            case 'ongoing':
                $badgeClass = 'bg-primary';
                $statusLabel = 'Đang phát hành';
                break;
            case 'completed':
                $badgeClass = 'bg-success';
                $statusLabel = 'Hoàn thành';
                break;
            case 'canceled':
                $badgeClass = 'bg-danger';
                if (is_array($series) && empty($editorId)) {
                    $statusLabel = 'Từ chối';
                } else {
                    $statusLabel = 'Đã hủy';
                }
                break;
            case 'suspended':
                $badgeClass = 'bg-warning text-dark';
                $statusLabel = 'Tạm ngưng';
                break;
        }
        return "<span class=\"badge {$badgeClass}\">{$statusLabel}</span>";
    }

    public function isSeriesLocked($series) {
        if (!$series) return false;
        if (in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
            return true;
        }
        if ($series['status'] === 'planning' && ($series['publish_type'] ?? '') === 'submitted') {
            return true;
        }
        return false;
    }

    /**
     * Kiểm tra xem Chapter có bị khóa thao tác (reviewing_draft, reviewing_final, approved, published) hay không
     */
    public function isChapterLocked($chapter) {
        if (!$chapter) return false;
        return in_array($chapter['status'], ['reviewing', 'reviewing_draft', 'reviewing_final', 'approved', 'published']);
    }

    /**
     * Kiểm tra xem người dùng hiện tại có quyền truy cập bộ truyện hay không
     */
    public function hasSeriesAccess($series) {
        if (!$series) return false;
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'admin' || $role === 'board') {
            return true;
        }
        if ($role === 'editor') {
            return $series['editor_id'] == $_SESSION['user_id'] && $series['status'] !== 'planning';
        }
        if ($role === 'mangaka') {
            return $series['mangaka_id'] == $_SESSION['user_id'];
        }
        return false;
    }

    /**
     * Reusable Helper: Giải quyết và trả về đường dẫn đầy đủ của file ảnh vẽ trang truyện
     */
    public function resolvePageImageUrl($imageUrl) {
        if (empty($imageUrl) || $imageUrl === 'no_genko') {
            return '';
        }
        return (strpos($imageUrl, 'http') === 0) ? $imageUrl : BASE_PATH . '/' . ltrim($imageUrl, '/');
    }

    public function isPageUpdatedAfterAnnotation($page) {
        if (empty($page)) {
            return false;
        }
        if (empty($page['annotation_count']) || $page['annotation_count'] <= 0) {
            return false;
        }
        return !empty($page['old_image_url']);
    }

    /**
     * Reusable Helper: Kiểm tra xem trang truyện có bản vẽ mới cập nhật đè lên sau khi Editor báo lỗi hay không (khi dùng mảng annotations truyền vào)
     */
    public function isPageUpdatedAfterLatestAnnotation($page, $annotations) {
        if (empty($page) || empty($annotations)) {
            return false;
        }
        return !empty($page['old_image_url']);
    }

    /**
     * Đồng bộ trạng thái của Trang truyện dựa trên tiến độ các công việc (Tasks)
     */
    public function syncPageStatus($pageId) {
        require_once __DIR__ . '/../models/Task.php';
        $taskModel = new \Task();
        $tasksOnPage = $taskModel->findByPageId($pageId);
        
        $allTasksCompleted = true;
        foreach ($tasksOnPage as $t) {
            if ($t['status'] !== 'completed') {
                $allTasksCompleted = false;
                break;
            }
        }
        
        require_once __DIR__ . '/../models/Page.php';
        $pageModel = new \Page();
        $page = $pageModel->findById($pageId);
        
        if ($page && !in_array($page['status'], ['drafting', 'reviewing_draft', 'reviewing_final', 'published'])) {
            $hasGenko = (!empty($page['image_url']) && $page['old_image_url'] !== 'no_genko');
            $newStatus = ($allTasksCompleted && $hasGenko) ? 'approved' : 'drawing';
            if ($page['status'] !== $newStatus) {
                $pageModel->update($pageId, ['status' => $newStatus]);
            }
        }
    }

    /**
     * Xác thực CSRF Token cho các thao tác thay đổi trạng thái (POST).
     * Nếu token không hợp lệ hoặc bị thiếu, lập tức hủy request và trả về HTTP 403 Forbidden.
     */
    protected function validateCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = Csrf::getTokenFromRequest();
            if (!$token || !Csrf::validate($token)) {
                $this->handleCsrfError();
            }
        }
    }

    /**
     * Xử lý phản hồi khi xác thực CSRF thất bại.
     */
    protected function handleCsrfError() {
        http_response_code(403);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
            || (!empty($_SERVER['HTTP_X_CSRF_TOKEN']));

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => 'CSRF token mismatch'
            ]);
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error'] = 'Phiên làm việc hết hạn hoặc yêu cầu không hợp lệ (CSRF Error)!';

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (!empty($referer) && parse_url($referer, PHP_URL_HOST) === $host) {
            header('Location: ' . $referer);
        } else {
            $base = defined('BASE_PATH') ? BASE_PATH : '';
            header('Location: ' . $base . '/index.php');
        }
        exit;
    }
}

