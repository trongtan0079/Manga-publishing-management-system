<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Page.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/SeriesRanking.php';


class DashboardController extends BaseController {

    /**
     * Hàm khởi tạo: Tự động chạy khi gọi DashboardController.
     * Yêu cầu người dùng phải đăng nhập trước khi xem các bảng điều khiển.
     */
    public function __construct() {
        parent::__construct();
        \requireLogin();
    }

    /**
     * Dashboard dành cho Admin
     * Hiển thị tổng quan toàn bộ hệ thống: Số lượng user, series, chapter, page.
     */
    public function admin() {
        \requireRole('admin');
        
        $userModel = new User();
        $seriesModel = new Series();
        $chapterModel = new Chapter();
        $pageModel = new Page();
        $taskModel = new Task();
        $submissionModel = new Submission();
        $reviewModel = new Review();
        $notificationModel = new Notification();
        $rankingModel = new SeriesRanking();
        
        $totalUsers = $userModel->countAll();
        $totalSeries = $seriesModel->countAll();
        $totalChapters = $chapterModel->countAll();
        $totalPages = $pageModel->countAll();
        $totalTasks = $taskModel->countAll();
        $totalSubmissions = $submissionModel->countAll();
        $totalReviews = $reviewModel->countAll();
        $totalNotifications = $notificationModel->countAll();
        $totalRankings = $rankingModel->countAll();
        
        // Thống kê User theo trạng thái
        $activeUsers = $userModel->countByCondition(['status' => 'active']);
        $inactiveUsers = $userModel->countByCondition(['status' => 'inactive']);
        $bannedUsers = $userModel->countByCondition(['status' => 'banned']);
        
        // Thống kê User theo Role (cho biểu đồ)
        $conn = $userModel->getConnection();
        $stmtRoles = $conn->prepare("SELECT r.role_name, COUNT(u.user_id) as user_count FROM roles r LEFT JOIN users u ON r.role_id = u.role_id GROUP BY r.role_id, r.role_name ORDER BY r.role_id");
        $stmtRoles->execute();
        $usersByRole = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC);
        
        // Thống kê Task theo Status (cho biểu đồ)
        $stmtTaskStatus = $conn->prepare("SELECT status, COUNT(*) as task_count FROM tasks GROUP BY status");
        $stmtTaskStatus->execute();
        $tasksByStatusRaw = $stmtTaskStatus->fetchAll(\PDO::FETCH_ASSOC);
        $tasksByStatus = [];
        foreach ($tasksByStatusRaw as $row) {
            $tasksByStatus[$row['status']] = (int)$row['task_count'];
        }
        
        // Thống kê Submission theo Status (cho biểu đồ)
        $stmtSubStatus = $conn->prepare("SELECT status, COUNT(*) as sub_count FROM submissions GROUP BY status");
        $stmtSubStatus->execute();
        $subsByStatusRaw = $stmtSubStatus->fetchAll(\PDO::FETCH_ASSOC);
        $subsByStatus = [];
        foreach ($subsByStatusRaw as $row) {
            $subsByStatus[$row['status']] = (int)$row['sub_count'];
        }
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Dashboard dành cho Mangaka (Tác giả)
     * Thống kê số lượng tác phẩm, chương truyện, trang truyện thuộc sở hữu của tác giả này.
     * Cũng như lấy ra bảng xếp hạng mới nhất của các tác phẩm.
     */
    public function mangaka() {
        \requireRole('mangaka');
        $userId = $_SESSION['user_id'];
        
        $seriesModel = new Series();
        $chapterModel = new Chapter();
        $pageModel = new Page();
        $taskModel = new Task();
        $rankingModel = new SeriesRanking();
        
        $submissionModel = new Submission();
        
        $totalSeries = $seriesModel->countByCondition(['mangaka_id' => $userId]);
        
        // Đếm tổng số chương truyện thuộc về tác giả này
        $stmt = $chapterModel->getConnection()->prepare("SELECT COUNT(*) as total FROM chapters c JOIN series s ON c.series_id = s.series_id WHERE s.mangaka_id = :mangaka_id");
        $stmt->execute(['mangaka_id' => $userId]);
        $totalChapters = (int)$stmt->fetchColumn();
        
        // Đếm tổng số trang truyện thuộc về tác giả này
        $stmtPages = $pageModel->getConnection()->prepare("SELECT COUNT(*) as total FROM pages p JOIN chapters c ON p.chapter_id = c.chapter_id JOIN series s ON c.series_id = s.series_id WHERE s.mangaka_id = :mangaka_id");
        $stmtPages->execute(['mangaka_id' => $userId]);
        $totalPages = (int)$stmtPages->fetchColumn();
        
        // Đếm tổng số tasks thuộc về tác giả này
        $totalTasks = $taskModel->countByCondition(['mangaka_id' => $userId]);
        
        // Đếm tổng số submissions thuộc về tác giả này
        $stmtSub = $submissionModel->getConnection()->prepare("
            SELECT COUNT(s.submission_id) as total 
            FROM submissions s
            LEFT JOIN tasks t ON s.task_id = t.task_id
            LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
            LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
            WHERE t.mangaka_id = :mangaka_id1 OR ser_chap.mangaka_id = :mangaka_id2
        ");
        $stmtSub->execute(['mangaka_id1' => $userId, 'mangaka_id2' => $userId]);
        $totalSubmissions = (int)$stmtSub->fetchColumn();
        
        // Đếm số submissions pending
        $stmtPending = $submissionModel->getConnection()->prepare("
            SELECT COUNT(s.submission_id) as total 
            FROM submissions s
            LEFT JOIN tasks t ON s.task_id = t.task_id
            LEFT JOIN chapters c ON s.chapter_id = c.chapter_id
            LEFT JOIN series ser_chap ON c.series_id = ser_chap.series_id
            WHERE s.status = 'pending' AND (t.mangaka_id = :mangaka_id1 OR ser_chap.mangaka_id = :mangaka_id2)
        ");
        $stmtPending->execute(['mangaka_id1' => $userId, 'mangaka_id2' => $userId]);
        $pendingReviews = (int)$stmtPending->fetchColumn();
        
        // Lấy lịch sử xếp hạng để hiển thị biến động
        $mangakaRankings = $rankingModel->findByMangakaId($userId);
        $latestRankings = [];
        $seenSeries = [];
        foreach ($mangakaRankings as $r) {
            if (!isset($seenSeries[$r['series_id']])) {
                $latestRankings[] = $r;
                $seenSeries[$r['series_id']] = true;
            }
        }
        
        require_once __DIR__ . '/../views/mangaka/dashboard.php';
    }

    /**
     * Dashboard dành cho Assistant (Trợ lý)
     * Thống kê các công việc (Task) được giao: Tổng số, đang làm, đã hoàn thành.
     */
    public function assistant() {
        \requireRole('assistant');
        $userId = $_SESSION['user_id'];
        
        $taskModel = new Task();
        $assignedTasks = $taskModel->countByCondition(['assistant_id' => $userId]);
        $inProgressTasks = $taskModel->countByCondition(['assistant_id' => $userId, 'status' => 'in_progress']);
        $completedTasks = $taskModel->countByCondition(['assistant_id' => $userId, 'status' => 'completed']);
        
        $activeTasks = $taskModel->findActiveByAssistantId($userId);
        
        require_once __DIR__ . '/../views/assistant/dashboard.php';
    }

    /**
     * Theo dõi Tiến độ & Deadline dành cho Editor
     */
    public function progress() {
        \requireRole('editor');
        $userId = $_SESSION['user_id'];
        
        $seriesModel = new Series();
        $taskModel = new Task();
        $chapterModel = new Chapter();
        
        // Fetch all active series
        $seriesList = $seriesModel->findAll();
        $progressData = [];
        
        foreach ($seriesList as $series) {
            $chapters = $chapterModel->findBySeriesId($series['series_id']);
            // count completed chapters
            $completedChapters = 0;
            foreach ($chapters as $ch) {
                if ($ch['status'] === 'approved' || $ch['status'] === 'published') {
                    $completedChapters++;
                }
            }
            
            // fetch tasks for this series to get deadlines
            $sql = "SELECT t.*, c.chapter_number, c.title as chapter_title
                    FROM tasks t
                    JOIN pages p ON t.page_id = p.page_id
                    JOIN chapters c ON p.chapter_id = c.chapter_id
                    WHERE c.series_id = :series_id AND t.status != 'completed'
                    ORDER BY t.due_date ASC LIMIT 5";
            $stmt = $taskModel->getConnection()->prepare($sql);
            $stmt->execute(['series_id' => $series['series_id']]);
            $pendingTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $progressData[] = [
                'series' => $series,
                'total_chapters' => count($chapters),
                'completed_chapters' => $completedChapters,
                'pending_tasks' => $pendingTasks
            ];
        }
        
        require_once __DIR__ . '/../views/editor/progress.php';
    }

    /**
     * Dashboard dành cho Editor (Biên tập viên)
     * Hiển thị số bản thảo đang chờ duyệt và số đánh giá đã thực hiện.
     */
    public function editor() {
        \requireRole('editor');
        $userId = $_SESSION['user_id'];
        
        $submissionModel = new Submission();
        $reviewModel = new Review();
        
        // Lấy số lượng bản thảo chương đang chờ duyệt
        $stmt = $submissionModel->getConnection()->prepare("SELECT COUNT(*) as total FROM submissions WHERE status = 'pending' AND chapter_id IS NOT NULL");
        $stmt->execute();
        $pendingSubmissions = (int)$stmt->fetchColumn();
        
        // Lấy số lượng đánh giá mà Editor này đã làm
        $recentReviews = $reviewModel->countByCondition(['reviewer_id' => $userId]);

        // Đếm Approved và Rejected cho Editor hiện tại
        $stmtApprove = $reviewModel->getConnection()->prepare("
            SELECT COUNT(r.review_id) as total 
            FROM reviews r
            JOIN submissions s ON r.submission_id = s.submission_id
            WHERE r.reviewer_id = :reviewer_id AND s.status = 'approved'
        ");
        $stmtApprove->execute(['reviewer_id' => $userId]);
        $approvedSubmissions = (int)$stmtApprove->fetchColumn();

        $stmtReject = $reviewModel->getConnection()->prepare("
            SELECT COUNT(r.review_id) as total 
            FROM reviews r
            JOIN submissions s ON r.submission_id = s.submission_id
            WHERE r.reviewer_id = :reviewer_id AND s.status = 'rejected'
        ");
        $stmtReject->execute(['reviewer_id' => $userId]);
        $rejectedSubmissions = (int)$stmtReject->fetchColumn();

        // Lấy 5 pending submissions
        $pendingList = array_slice($submissionModel->findPendingSubmissions(), 0, 5);

        // Lấy 5 recent reviews
        $recentReviewList = array_slice($reviewModel->findByReviewerId($userId), 0, 5);

        
        require_once __DIR__ . '/../views/editor/dashboard.php';
    }

    /**
     * Dashboard dành cho Board (Ban Giám đốc)
     * Hiển thị báo cáo xếp hạng truyện: Truyện đứng đầu, top 5, bottom 5 trong kỳ đánh giá gần nhất.
     */
    public function board() {
        \requireRole('board');
        
        $rankingModel = new SeriesRanking();
        // Lấy kỳ đánh giá gần nhất
        $latestPeriod = $rankingModel->getLatestPeriod();
        
        $totalRankings = $rankingModel->countAll();
        $evaluatedSeries = 0;
        $topRankingSeriesName = "Chưa có dữ liệu";
        $top5Series = [];
        $bottom5Series = [];
        
        if ($latestPeriod) {
            $evaluatedSeries = $rankingModel->getSeriesCountByPeriod($latestPeriod);
            $top5Series = $rankingModel->getTopSeriesByPeriod($latestPeriod, 5);
            $bottom5Series = $rankingModel->getBottomSeriesByPeriod($latestPeriod, 5);
            // Lấy tên truyện đứng hạng 1
            if (!empty($top5Series)) {
                $topRankingSeriesName = $top5Series[0]['series_title'];
            }
        }
        
        $seriesModel = new Series();
        $totalSeriesCount = $seriesModel->countAll();
        $ungradedSeries = max(0, $totalSeriesCount - $evaluatedSeries);
        
        require_once __DIR__ . '/../views/board/dashboard.php';
    }
}
