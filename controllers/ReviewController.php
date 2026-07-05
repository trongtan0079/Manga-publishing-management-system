<?php


require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Notification.php';


/**
 * Class ReviewController
 * 
 * Quản lý hoạt động đánh giá (Review) các bản thảo (Submission).
 * Cho phép Editor đánh giá bản thảo chương truyện (Chapter) và Mangaka đánh giá sản phẩm của Assistant.
 */
class ReviewController extends BaseController
{
    private $reviewModel;
    private $submissionModel;
    private $notificationModel;

    /**
     * Khởi tạo ReviewController.
     * Xác thực đăng nhập và khởi tạo các Model cần thiết.
     */
    public function __construct() {
        parent::__construct();
        \requireLogin();
        $this->reviewModel = new Review();
        $this->submissionModel = new Submission();
        $this->notificationModel = new Notification();
    }

    /**
     * Hiển thị danh sách các bản thảo cần đánh giá dựa trên vai trò của người dùng.
     */
    public function index() {
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor') {
            // Editor (Biên tập viên) xem tất cả các bản thảo chương truyện (chờ duyệt + lịch sử)
            $submissions = $this->submissionModel->findAllChapterSubmissions();
            require_once __DIR__ . '/../views/editor/review_list.php';
        } elseif ($role === 'mangaka') {
            // Mangaka xem các bản thảo của Task đang chờ duyệt thuộc về các Task do họ giao
            $userId = $_SESSION['user_id'];
            $submissions = $this->submissionModel->findPendingSubmissionsByMangakaId($userId);
            require_once __DIR__ . '/../views/editor/review_list.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }
    }

    /**
     * Kiểm tra quyền đánh giá bản thảo của người dùng hiện tại.
     * 
     * @param array $submission Thông tin bản thảo cần kiểm tra
     * @return bool Trả về true nếu có quyền, ngược lại trả về false
     */
    private function checkReviewPermission($submission) {
        $role = $_SESSION['role_name'] ?? '';
        // Editor có quyền đánh giá bản thảo của Chương truyện (chapter_id không rỗng)
        if ($role === 'editor' && $submission['chapter_id']) {
            return true;
        }
        // Mangaka có quyền đánh giá bản thảo của Nhiệm vụ (task_id không rỗng)
        if ($role === 'mangaka' && $submission['task_id']) {
            // Kiểm tra xem nhiệm vụ này có phải do Mangaka này giao hay không
            require_once __DIR__ . '/../models/Task.php';
            $taskModel = new \Task();
            $task = $taskModel->findById($submission['task_id']);
            if ($task && $task['mangaka_id'] == $_SESSION['user_id']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hiển thị giao diện tạo đánh giá mới cho bản thảo.
     */
    public function create() {
        if (!isset($_GET['submission_id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        $submissionId = $_GET['submission_id'];
        $submission = $this->submissionModel->findWithMetadataById($submissionId);
        
        if (!$submission || !$this->checkReviewPermission($submission)) {
            $_SESSION['error'] = 'Không tìm thấy bản thảo hoặc bạn không có quyền đánh giá.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        require_once __DIR__ . '/../views/editor/review_create.php';
    }

    /**
     * Lưu thông tin đánh giá mới vào cơ sở dữ liệu và cập nhật trạng thái bản thảo.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submissionId = $_POST['submission_id'] ?? null;
            $comments = trim($_POST['comments'] ?? '');
            $rating = $_POST['rating'] ?? null;
            $decision = $_POST['decision'] ?? '';

            if (empty($comments) || empty($decision)) {
                $_SESSION['error'] = 'Comments và Decision là bắt buộc.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
                exit;
            }

            if ($decision !== 'approved' && $decision !== 'rejected') {
                $_SESSION['error'] = 'Quyết định không hợp lệ.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
                exit;
            }

            if (!empty($rating)) {
                $ratingVal = intval($rating);
                if ($ratingVal < 1 || $ratingVal > 10) {
                    $_SESSION['error'] = 'Điểm số phải nằm trong khoảng từ 1 đến 10.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
                    exit;
                }
                $rating = $ratingVal;
            } else {
                $rating = null;
            }

            $submission = $this->submissionModel->findById($submissionId);
            if (!$submission || !$this->checkReviewPermission($submission)) {
                $_SESSION['error'] = 'Bạn không có quyền đánh giá bản thảo này.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
                exit;
            }

            // Thêm mới thông tin đánh giá
            $reviewerId = $_SESSION['user_id'];
            $data = [
                'submission_id' => $submissionId,
                'reviewer_id' => $reviewerId,
                'comments' => $comments,
                'rating' => empty($rating) ? null : $rating
            ];
            $this->reviewModel->insert($data);

            // Cập nhật trạng thái của bản thảo (Phê duyệt hoặc Từ chối)
            $status = ($decision === 'approved') ? 'approved' : 'rejected';
            $this->submissionModel->update($submissionId, ['status' => $status]);

            // Gửi thông báo đến người nộp bản thảo
            $this->notificationModel->createNotification(
                $submission['user_id'],
                'review_created',
                "Có một nhận xét mới cho bản thảo của bạn."
            );

            $statusText = $status === 'approved' ? 'phê duyệt' : 'từ chối';
            $notifType = $status === 'approved' ? 'submission_approved' : 'submission_rejected';
            
            $this->notificationModel->createNotification(
                $submission['user_id'],
                $notifType,
                "Bản thảo của bạn đã bị {$statusText}. Nhận xét: " . mb_substr($comments, 0, 50) . "..."
            );

            // Nếu là bản thảo nhiệm vụ (Task) và được duyệt, cập nhật trạng thái Task thành 'completed'
            if ($submission['task_id'] && $status === 'approved') {
                require_once __DIR__ . '/../models/Task.php';
                $taskModel = new \Task();
                $taskModel->update($submission['task_id'], ['status' => 'completed']);

                // Đồng thời cập nhật trạng thái PageRegion liên kết thành 'completed'
                $taskDetail = $taskModel->findById($submission['task_id']);
                if ($taskDetail && !empty($taskDetail['page_region_id'])) {
                    require_once __DIR__ . '/../models/PageRegion.php';
                    $pageRegionModel = new \PageRegion();
                    $pageRegionModel->update($taskDetail['page_region_id'], ['status' => 'completed']);

                    // Kiểm tra xem tất cả các phân vùng của trang này đã Completed hết chưa
                    $pageId = $taskDetail['page_id'];
                    $regions = $pageRegionModel->findByPageId($pageId);
                    $allCompleted = true;
                    if (!empty($regions)) {
                        foreach ($regions as $r) {
                            if ($r['status'] !== 'completed') {
                                $allCompleted = false;
                                break;
                            }
                        }
                    } else {
                        $allCompleted = false;
                    }

                    // Nếu tất cả phân vùng đã xong, tự động cập nhật trạng thái trang truyện thành 'approved'
                    if ($allCompleted) {
                        require_once __DIR__ . '/../models/Page.php';
                        $pageModel = new \Page();
                        $pageModel->update($pageId, ['status' => 'approved']);
                    }
                }
            }

            // Nếu là bản thảo chương truyện (Chapter) và được duyệt, cập nhật trạng thái Chapter thành 'approved'
            if ($submission['chapter_id'] && $status === 'approved') {
                require_once __DIR__ . '/../models/Chapter.php';
                $chapterModel = new \Chapter();
                $chapterModel->update($submission['chapter_id'], ['status' => 'approved']);
            }

            $_SESSION['success'] = 'Đã đánh giá bản thảo thành công.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
    }

    /**
     * Hiển thị chi tiết một đánh giá cụ thể.
     */
    public function show() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        $reviewId = $_GET['id'];
        $review = $this->reviewModel->findById($reviewId);
        
        if (!$review) {
            $_SESSION['error'] = 'Không tìm thấy đánh giá.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }

        $submission = $this->submissionModel->findWithMetadataById($review['submission_id']);
        
        // Kiểm tra quyền xem đánh giá: Chỉ những người liên quan trực tiếp mới được xem
        $role = $_SESSION['role_name'] ?? '';
        $userId = $_SESSION['user_id'];
        $hasAccess = false;
        if ($role === 'editor') $hasAccess = true;
        if ($submission['user_id'] == $userId) $hasAccess = true;
        if ($review['reviewer_id'] == $userId) $hasAccess = true;
        
        if (!$hasAccess) {
            $_SESSION['error'] = 'Bạn không có quyền xem đánh giá này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }

        require_once __DIR__ . '/../views/editor/review_detail.php';
    }
}
