<?php

namespace App\Controllers;

require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Notification.php';

use Review;
use Submission;
use Notification;

class ReviewController
{
    private $reviewModel;
    private $submissionModel;
    private $notificationModel;

    public function __construct() {
        $this->reviewModel = new Review();
        $this->submissionModel = new Submission();
        $this->notificationModel = new Notification();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor' || $role === 'mangaka') {
            $submissions = $this->submissionModel->findPendingSubmissions();
            require_once __DIR__ . '/../views/editor/review_list.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=index');
            exit;
        }
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
            exit;
        }

        if (!isset($_GET['submission_id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        $submissionId = $_GET['submission_id'];
        $submission = $this->submissionModel->findWithMetadataById($submissionId);
        
        if (!$submission) {
            $_SESSION['error'] = 'Không tìm thấy bản thảo.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        require_once __DIR__ . '/../views/editor/review_create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
                exit;
            }

            $submissionId = $_POST['submission_id'] ?? null;
            $comments = trim($_POST['comments'] ?? '');
            $rating = $_POST['rating'] ?? null;
            $decision = $_POST['decision'] ?? '';

            if (empty($comments) || empty($decision)) {
                $_SESSION['error'] = 'Comments và Decision là bắt buộc.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
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

            // Update submission
            $status = ($decision === 'approved') ? 'approved' : 'rejected';
            $this->submissionModel->update($submissionId, ['status' => $status]);

            // Create notification
            $submission = $this->submissionModel->findById($submissionId);
            if ($submission) {
                $statusText = $status === 'approved' ? 'phê duyệt' : 'từ chối';
                $notifData = [
                    'user_id' => $submission['user_id'],
                    'type' => 'review_' . $status,
                    'message' => "Bản thảo của bạn đã bị {$statusText}. Nhận xét: " . mb_substr($comments, 0, 50) . "..."
                ];
                $this->notificationModel->insert($notifData);
            }

            $_SESSION['success'] = 'Đã đánh giá bản thảo thành công.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
    }

    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=auth&action=login');
            exit;
        }

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
        
        require_once __DIR__ . '/../views/editor/review_detail.php';
    }
}
