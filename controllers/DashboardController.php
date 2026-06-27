<?php

namespace App\Controllers;

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Page.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/SeriesRanking.php';

use User;
use Series;
use Chapter;
use Page;
use Task;
use Submission;
use Review;
use SeriesRanking;

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
        
        $totalUsers = $userModel->countAll();
        $totalSeries = $seriesModel->countAll();
        $totalChapters = $chapterModel->countAll();
        $totalPages = $pageModel->countAll();
        
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
        
        $totalSeries = $seriesModel->countByCondition(['mangaka_id' => $userId]);
        
        // Đếm tổng số chương truyện thuộc về tác giả này
        $stmt = $chapterModel->getConnection()->prepare("SELECT COUNT(*) as total FROM chapters c JOIN series s ON c.series_id = s.series_id WHERE s.mangaka_id = :mangaka_id");
        $stmt->execute(['mangaka_id' => $userId]);
        $totalChapters = (int)$stmt->fetchColumn();
        
        // Đếm tổng số trang truyện thuộc về tác giả này
        $stmt = $pageModel->getConnection()->prepare("SELECT COUNT(*) as total FROM pages p JOIN chapters c ON p.chapter_id = c.chapter_id JOIN series s ON c.series_id = s.series_id WHERE s.mangaka_id = :mangaka_id");
        $stmt->execute(['mangaka_id' => $userId]);
        $totalPages = (int)$stmt->fetchColumn();
        
        // Đếm số task mà tác giả này đã giao
        $totalTasks = $taskModel->countByCondition(['mangaka_id' => $userId]);
        
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
        
        require_once __DIR__ . '/../views/assistant/dashboard.php';
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
        
        require_once __DIR__ . '/../views/board/dashboard.php';
    }
}
