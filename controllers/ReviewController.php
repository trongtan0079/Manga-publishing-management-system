<?php


require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Notification.php';


class ReviewController extends BaseController
{
    private $reviewModel;
    private $submissionModel;
    private $notificationModel;

    public function __construct() {
        parent::__construct();
        \requireLogin();
        $this->reviewModel = new Review();
        $this->submissionModel = new Submission();
        $this->notificationModel = new Notification();
    }

    public function index() {
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor') {
            // Editor sees all chapter submissions (pending + history)
            $submissions = $this->submissionModel->findAllChapterSubmissions();
            require_once __DIR__ . '/../views/editor/review_list.php';
        } elseif ($role === 'mangaka') {
            // Mangaka sees pending task submissions for their own tasks
            $userId = $_SESSION['user_id'];
            $submissions = $this->submissionModel->findPendingSubmissionsByMangakaId($userId);
            require_once __DIR__ . '/../views/editor/review_list.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }
    }

    private function checkReviewPermission($submission) {
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor' && $submission['chapter_id']) {
            return true; // Editor reviews chapters
        }
        if ($role === 'mangaka' && $submission['task_id']) {
            // Check if task belongs to this mangaka
            require_once __DIR__ . '/../models/Task.php';
            $taskModel = new \Task();
            $task = $taskModel->findById($submission['task_id']);
            if ($task && $task['mangaka_id'] == $_SESSION['user_id']) {
                return true;
            }
        }
        return false;
    }

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

            $submission = $this->submissionModel->findById($submissionId);
            if (!$submission || !$this->checkReviewPermission($submission)) {
                $_SESSION['error'] = 'Bạn không có quyền đánh giá bản thảo này.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
                exit;
            }

            // Insert review
            $reviewerId = $_SESSION['user_id'];
            $data = [
                'submission_id' => $submissionId,
                'reviewer_id' => $reviewerId,
                'comments' => $comments,
                'rating' => empty($rating) ? null : $rating
            ];
            $this->reviewModel->insert($data);

            // Update submission status
            $status = ($decision === 'approved') ? 'approved' : 'rejected';
            $this->submissionModel->update($submissionId, ['status' => $status]);

            // Create notifications
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

            // If it's a task submission and it's approved, update the task to completed
            if ($submission['task_id'] && $status === 'approved') {
                require_once __DIR__ . '/../models/Task.php';
                $taskModel = new \Task();
                $taskModel->update($submission['task_id'], ['status' => 'completed']);
            }

            // If it's a chapter submission and it's approved, update chapter to approved
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
        
        // Anyone involved in the submission process can see the review
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
