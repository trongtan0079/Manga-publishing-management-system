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
require_once __DIR__ . '/../models/SystemLog.php';


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
        $notificationModel = new Notification();
        
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

        // Lấy danh sách toàn bộ task để tính tỷ lệ hoàn thành
        $allTasks = $taskModel->findByMangakaId($userId);
        $completedTasksCount = 0;
        foreach ($allTasks as $t) {
            if ($t['status'] === 'completed') {
                $completedTasksCount++;
            }
        }
        $totalTasksCount = count($allTasks);
        $taskCompletionRate = $totalTasksCount > 0 ? round(($completedTasksCount / $totalTasksCount) * 100) : 0;

        // Lấy 5 task gần nhất để hiển thị bảng tiến độ
        $recentTasksList = $taskModel->findByMangakaId($userId, 5);

        // Lấy danh sách series của họa sĩ này để filter
        $mySeriesList = $seriesModel->findByMangakaId($userId);

        // Lấy các hoạt động gần đây (thông báo)
        $recentActivities = $notificationModel->getLatestNotifications($userId, 5);
        
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
        
        // Tính toán thống kê trang đã duyệt và thu nhập theo từng tháng
        $stmtIncome = $taskModel->getConnection()->prepare("
            SELECT 
                DATE_FORMAT(t.updated_at, '%m/%Y') as period,
                COUNT(DISTINCT t.page_id) as approved_pages_count,
                COUNT(t.task_id) as completed_tasks_count,
                COUNT(t.task_id) * 300000 as estimated_income
            FROM tasks t
            WHERE t.assistant_id = :assistant_id AND t.status = 'completed'
            GROUP BY DATE_FORMAT(t.updated_at, '%m/%Y')
            ORDER BY MIN(t.updated_at) DESC
        ");
        $stmtIncome->execute(['assistant_id' => $userId]);
        $monthlyIncomeStats = $stmtIncome->fetchAll(PDO::FETCH_ASSOC);
        
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

        // Đếm số lượng Reviewed cho Editor hiện tại
        $stmtReviewed = $reviewModel->getConnection()->prepare("
            SELECT COUNT(r.review_id) as total 
            FROM reviews r
            JOIN submissions s ON r.submission_id = s.submission_id
            WHERE r.reviewer_id = :reviewer_id AND s.status = 'reviewed'
        ");
        $stmtReviewed->execute(['reviewer_id' => $userId]);
        $reviewedSubmissions = (int)$stmtReviewed->fetchColumn();

        // Lấy danh sách 5 chapter sắp đến hạn deadline mà chưa được duyệt/xuất bản
        $stmtDeadlines = $submissionModel->getConnection()->prepare("
            SELECT c.chapter_number, c.title as chapter_title, c.due_date, s.title as series_title, u.full_name as mangaka_name
            FROM chapters c
            JOIN series s ON c.series_id = s.series_id
            JOIN users u ON s.mangaka_id = u.user_id
            WHERE c.status NOT IN ('approved', 'published') AND c.due_date IS NOT NULL
            ORDER BY c.due_date ASC
            LIMIT 5
        ");
        $stmtDeadlines->execute();
        $upcomingChapters = $stmtDeadlines->fetchAll(\PDO::FETCH_ASSOC);
        
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
        $chartSeriesData = [];
        
        if ($latestPeriod) {
            $evaluatedSeries = $rankingModel->getSeriesCountByPeriod($latestPeriod);
            $top5Series = $rankingModel->getTopSeriesByPeriod($latestPeriod, 5);
            $bottom5Series = $rankingModel->getBottomSeriesByPeriod($latestPeriod, 5);
            // Lấy tối đa 10 truyện để vẽ biểu đồ
            $chartSeriesData = $rankingModel->getTopSeriesByPeriod($latestPeriod, 10);
            
            // Lấy tên truyện đứng hạng 1
            if (!empty($top5Series)) {
                $topRankingSeriesName = $top5Series[0]['series_title'];
            }
        }
        
        $seriesModel = new Series();
        $conn = $seriesModel->getConnection();
        $stmtActive = $conn->prepare("SELECT COUNT(*) FROM series WHERE status IN ('ongoing', 'completed', 'suspended')");
        $stmtActive->execute();
        $totalActiveSeriesCount = (int)$stmtActive->fetchColumn();
        $ungradedSeries = max(0, $totalActiveSeriesCount - $evaluatedSeries);
        
        require_once __DIR__ . '/../views/board/dashboard.php';
    }

    /**
     * Xuất báo cáo xếp hạng dưới dạng file CSV (Dành cho Board)
     */
    public function exportRanking() {
        \requireRole('board');
        
        $rankingModel = new SeriesRanking();
        $latestPeriod = $rankingModel->getLatestPeriod();
        
        if (!$latestPeriod) {
            $_SESSION['error'] = 'Chưa có dữ liệu xếp hạng nào để xuất báo cáo.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=board');
            exit;
        }
        
        $rankings = $rankingModel->getTopSeriesByPeriod($latestPeriod, 100);
        
        $filename = 'bao_cao_xep_hang_' . $latestPeriod . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Thêm UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Thứ hạng', 'Tên truyện', 'Điểm số', 'Kỳ đánh giá (Ngày bắt đầu)']);
        
        foreach ($rankings as $row) {
            fputcsv($output, [
                $row['rank_position'],
                $row['series_title'],
                $row['score'],
                $row['period_start_date']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Nhật ký hoạt động dành cho Admin
     */
    public function logs() {
        \requireRole('admin');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 15; // 15 logs per page
        $offset = ($page - 1) * $limit;
        
        $logModel = new SystemLog();
        $logs = $logModel->getPaginatedLogs($limit, $offset);
        $totalLogs = $logModel->countAll();
        $totalPages = ceil($totalLogs / $limit);
        
        $current_page = 'logs';
        require_once __DIR__ . '/../views/admin/logs.php';
    }

    /**
     * Sao lưu cơ sở dữ liệu (tải SQL dump) dành cho Admin
     */
    public function backupDb() {
        \requireRole('admin');
        
        $userModel = new User();
        $conn = $userModel->getConnection();
        
        // Ghi nhận nhật ký trước khi sao lưu
        SystemLog::logAction($_SESSION['user_id'], 'Sao lưu dữ liệu', 'Admin thực hiện sao lưu toàn bộ cơ sở dữ liệu');
        
        // 1. Khởi tạo đầu ra file sql
        $filename = 'manga_backup_' . date('Ymd_His') . '.sql';
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output SQL header info
        echo "-- ==============================================================================\n";
        echo "-- Manga Publishing Management System Database Backup\n";
        echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
        echo "-- ==============================================================================\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        // 2. Lấy toàn bộ các bảng trong database
        $tables = [];
        $stmt = $conn->query("SHOW TABLES");
        while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        // 3. Với mỗi bảng, tạo câu lệnh CREATE TABLE và INSERT INTO
        foreach ($tables as $table) {
            // Lấy CREATE TABLE
            $stmtCreate = $conn->query("SHOW CREATE TABLE `{$table}`");
            $rowCreate = $stmtCreate->fetch(\PDO::FETCH_NUM);
            
            echo "-- ------------------------------------------------------------------------------\n";
            echo "-- Cấu trúc bảng `{$table}`\n";
            echo "-- ------------------------------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS `{$table}`;\n";
            echo $rowCreate[1] . ";\n\n";
            
            // Lấy dữ liệu
            $stmtData = $conn->query("SELECT * FROM `{$table}`");
            $rowCount = $stmtData->rowCount();
            
            if ($rowCount > 0) {
                echo "-- ------------------------------------------------------------------------------\n";
                echo "-- Dữ liệu bảng `{$table}`\n";
                echo "-- ------------------------------------------------------------------------------\n";
                
                while ($row = $stmtData->fetch(\PDO::FETCH_ASSOC)) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    
                    $escapedValues = array_map(function($val) use ($conn) {
                        if ($val === null) return 'NULL';
                        return $conn->quote($val);
                    }, $values);
                    
                    echo "INSERT INTO `{$table}` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
                }
                echo "\n";
            }
        }
        
        echo "SET FOREIGN_KEY_CHECKS=1;\n";
        exit;
    }
}
