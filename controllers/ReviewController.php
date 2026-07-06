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

            // Hỗ trợ lọc trạng thái theo query parameter (chỉ hiện bản thảo 'pending' nếu status=pending)
            $status = $_GET['status'] ?? null;
            if ($status && $status === 'pending') {
                $submissions = array_filter($submissions, function($s) {
                    return $s['status'] === 'pending';
                });
            }
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

        // 1. Xác định chapter_id tương ứng
        $chapterId = null;
        if ($submission['chapter_id']) {
            $chapterId = $submission['chapter_id'];
        } elseif ($submission['task_id']) {
            require_once __DIR__ . '/../models/Task.php';
            $taskModel = new \Task();
            $task = $taskModel->findById($submission['task_id']);
            if ($task) {
                require_once __DIR__ . '/../models/Page.php';
                $pageModel = new \Page();
                $page = $pageModel->findById($task['page_id']);
                if ($page) {
                    $chapterId = $page['chapter_id'];
                }
            }
        }

        // 2. Kiểm tra xem chapter có bị khóa (approved / published) không
        if ($chapterId) {
            require_once __DIR__ . '/../models/Chapter.php';
            $chapterModel = new \Chapter();
            $chapter = $chapterModel->findById($chapterId);
            if ($chapter && ($chapter['status'] === 'approved' || $chapter['status'] === 'published')) {
                return false;
            }
        }

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
                if ($taskDetail) {
                    $pageId = $taskDetail['page_id'];
                    require_once __DIR__ . '/../models/PageRegion.php';
                    $pageRegionModel = new \PageRegion();

                    if (!empty($taskDetail['page_region_id'])) {
                        $pageRegionModel->update($taskDetail['page_region_id'], ['status' => 'completed']);
                    }

                    // Tự động duyệt trang vẽ (Page status = 'approved') nếu tất cả các Task của trang này đã hoàn thành
                    $tasksOnPage = $taskModel->findByPageId($pageId);
                    $allTasksCompleted = true;
                    foreach ($tasksOnPage as $t) {
                        if ($t['status'] !== 'completed') {
                            $allTasksCompleted = false;
                            break;
                        }
                    }

                    // Đồng thời kiểm tra xem tất cả các phân vùng của trang này đã hoàn thành chưa (để đồng bộ)
                    $regions = $pageRegionModel->findByPageId($pageId);
                    $allRegionsCompleted = true;
                    foreach ($regions as $r) {
                        if ($r['status'] !== 'completed') {
                            $allRegionsCompleted = false;
                            break;
                        }
                    }

                    if ($allTasksCompleted && $allRegionsCompleted) {
                        require_once __DIR__ . '/../models/Page.php';
                        $pageModel = new \Page();
                        $pageModel->update($pageId, ['status' => 'approved']);
                    }
                }
            } elseif ($submission['task_id'] && $status === 'rejected') {
                require_once __DIR__ . '/../models/Task.php';
                $taskModel = new \Task();
                $taskModel->update($submission['task_id'], ['status' => 'pending']);

                $taskDetail = $taskModel->findById($submission['task_id']);
                if ($taskDetail && !empty($taskDetail['page_region_id'])) {
                    require_once __DIR__ . '/../models/PageRegion.php';
                    $pageRegionModel = new \PageRegion();
                    $pageRegionModel->update($taskDetail['page_region_id'], ['status' => 'pending']);
                }
            }

            // Nếu là bản thảo chương truyện (Chapter)
            if ($submission['chapter_id']) {
                require_once __DIR__ . '/../models/Chapter.php';
                $chapterModel = new \Chapter();
                if ($status === 'approved') {
                    $chapterModel->update($submission['chapter_id'], ['status' => 'approved']);

                    // Kiểm tra xem chapter có phải là chương cuối không để thông báo cho Board
                    $chapDetail = $chapterModel->findById($submission['chapter_id']);
                    if ($chapDetail && !empty($chapDetail['is_final'])) {
                        // Lấy thông tin bộ truyện
                        require_once __DIR__ . '/../models/Series.php';
                        $seriesModel = new \Series();
                        $seriesDetail = $seriesModel->findById($chapDetail['series_id']);
                        $seriesTitle = $seriesDetail ? $seriesDetail['title'] : 'bộ truyện';
                        
                        // Tìm toàn bộ tài khoản có role là 'board' đang hoạt động để gửi thông báo
                        require_once __DIR__ . '/../models/User.php';
                        $userModel = new \User();
                        $boardMembers = $userModel->findByRoleName('board');
                        
                        if (!empty($boardMembers)) {
                            foreach ($boardMembers as $member) {
                                $this->notificationModel->createNotification(
                                    $member['user_id'],
                                    'series_completed',
                                    "Chương cuối của bộ truyện '{$seriesTitle}' đã được duyệt. Vui lòng xác nhận hoàn thành."
                                );
                            }
                        }
                    }
                } else {
                    // Nếu bị từ chối (rejected), tự động chuyển trạng thái Chapter về 'drawing' để Mangaka chỉnh sửa
                    $chapterModel->update($submission['chapter_id'], ['status' => 'drawing']);
                }
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
