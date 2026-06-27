<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/SeriesRanking.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Notification.php';


class SeriesRankingController extends BaseController
{
    private $rankingModel;
    private $seriesModel;
    private $notificationModel;

    /**
     * Hàm khởi tạo (Constructor)
     * Đảm bảo người dùng đã đăng nhập.
     * Chặn hoàn toàn quyền truy cập của Assistant vào module Xếp hạng.
     */
    public function __construct() {
        parent::__construct();
        
        \requireLogin();

        $role = $_SESSION['role_name'];
        if ($role === 'assistant') {
            $_SESSION['error'] = 'Truy cập bị từ chối! Bạn không có quyền xem bảng xếp hạng.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        $this->rankingModel = new SeriesRanking();
        $this->seriesModel = new Series();
        $this->notificationModel = new Notification();
    }

    /**
     * Action: Hiển thị danh sách Xếp hạng
     * - Mangaka: Chỉ nhìn thấy xếp hạng của các tác phẩm do mình sáng tác.
     * - Board: Nhìn thấy toàn bộ xếp hạng (được quyền Thêm/Sửa/Xóa).
     * - Admin, Editor: Nhìn thấy toàn bộ xếp hạng (chỉ xem, Read-only).
     */
    public function index() {
        $role = $_SESSION['role_name'];
        if ($role === 'mangaka') {
            $rankings = $this->rankingModel->findByMangakaId($_SESSION['user_id']);
            require_once __DIR__ . '/../views/mangaka/rankings.php';
        } elseif ($role === 'board') {
            $rankings = $this->rankingModel->findAllWithSeries();
            require_once __DIR__ . '/../views/board/rankings.php';
        } else {
            // Admin, Editor
            $rankings = $this->rankingModel->findAllWithSeries();
            // Reusing a common view or board view for read-only
            // Since Board view has edit/delete buttons, we should pass role to hide them or create a specific view.
            require_once __DIR__ . '/../views/board/rankings.php'; 
        }
    }

    /**
     * Action: Hiển thị form tạo Xếp hạng mới
     * Quyền: Chỉ dành cho Board (Ban Giám đốc)
     */
    public function create() {
        \requireRole('board');
        // Need to get series for dropdown. Should get 'ongoing' or 'completed' maybe, but let's get all except planning/canceled
        $seriesList = $this->seriesModel->findAll(); 
        require_once __DIR__ . '/../views/board/ranking_create.php';
    }

    /**
     * Action: Xử lý lưu dữ liệu Xếp hạng mới vào Database
     * Nếu thành công, tự động gửi thông báo (Notification) cho Mangaka chủ sở hữu truyện.
     */
    public function store() {
        \requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seriesId = $_POST['series_id'] ?? '';
            $rankPosition = $_POST['rank_position'] ?? '';
            $score = $_POST['score'] ?? '';
            $periodStartDate = $_POST['period_start_date'] ?? '';

            if (!$seriesId || !$rankPosition || !$score || !$periodStartDate) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }

            if ($this->rankingModel->checkDuplicateRanking($seriesId, $periodStartDate)) {
                $_SESSION['error'] = 'Series này đã có đánh giá trong kỳ này (trùng lặp Rank Period).';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }

            $data = [
                'series_id' => $seriesId,
                'board_member_id' => $_SESSION['user_id'],
                'rank_position' => $rankPosition,
                'score' => $score,
                'period_start_date' => $periodStartDate
            ];

            try {
                $this->rankingModel->insert($data);
                
                // Lấy thông tin Mangaka để gửi thông báo chúc mừng / cập nhật
                $series = $this->seriesModel->findById($seriesId);
                if ($series) {
                    $mangakaId = $series['mangaka_id'];
                    $message = "Bộ truyện {$series['title']} của bạn đã được xếp hạng {$rankPosition} với điểm số {$score}.";
                    $this->notificationModel->createNotification($mangakaId, 'ranking_published', $message);
                }

                $_SESSION['success'] = 'Tạo Ranking mới thành công!';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=create');
                exit;
            }
        }
    }

    /**
     * Action: Xem chi tiết một Xếp hạng cụ thể
     * Nếu người dùng là Mangaka, chặn xem xếp hạng của truyện không thuộc quyền sở hữu.
     */
    public function show($id) {
        $ranking = $this->rankingModel->findById($id);
        if (!$ranking) {
            $_SESSION['error'] = 'Không tìm thấy Ranking.';
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }

        $series = $this->seriesModel->findById($ranking['series_id']);
        
        $role = $_SESSION['role_name'];
        if ($role === 'mangaka' && $series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Truy cập bị từ chối.';
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }

        require_once __DIR__ . '/../views/board/ranking_detail.php';
    }

    /**
     * Action: Hiển thị form chỉnh sửa Xếp hạng
     * Quyền: Chỉ dành cho Board
     */
    public function edit($id) {
        \requireRole('board');
        $ranking = $this->rankingModel->findById($id);
        if (!$ranking) {
            $_SESSION['error'] = 'Không tìm thấy Ranking.';
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }
        $seriesList = $this->seriesModel->findAll(); 
        require_once __DIR__ . '/../views/board/ranking_edit.php';
    }

    /**
     * Action: Xử lý lưu cập nhật Xếp hạng vào Database
     * Có kiểm tra trùng lặp nếu người dùng đổi Series hoặc Kỳ đánh giá.
     */
    public function update($id) {
        \requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ranking = $this->rankingModel->findById($id);
            if (!$ranking) {
                $_SESSION['error'] = 'Không tìm thấy Ranking.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
                exit;
            }

            $seriesId = $_POST['series_id'] ?? '';
            $rankPosition = $_POST['rank_position'] ?? '';
            $score = $_POST['score'] ?? '';
            $periodStartDate = $_POST['period_start_date'] ?? '';

            if (!$seriesId || !$rankPosition || !$score || !$periodStartDate) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }

            // Chỉ kiểm tra trùng lặp dữ liệu (Duplicate) nếu Series hoặc Kỳ đánh giá bị thay đổi
            if (($seriesId != $ranking['series_id'] || $periodStartDate != $ranking['period_start_date']) && 
                $this->rankingModel->checkDuplicateRanking($seriesId, $periodStartDate)) {
                $_SESSION['error'] = 'Series này đã có đánh giá trong kỳ này.';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }

            $data = [
                'series_id' => $seriesId,
                'rank_position' => $rankPosition,
                'score' => $score,
                'period_start_date' => $periodStartDate
            ];

            try {
                $this->rankingModel->update($id, $data);
                $_SESSION['success'] = 'Cập nhật Ranking thành công!';
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
                header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=edit&id=' . $id);
                exit;
            }
        }
    }

    /**
     * Action: Xóa một Xếp hạng
     * Quyền: Chỉ dành cho Board
     */
    public function delete($id) {
        \requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ranking = $this->rankingModel->findById($id);
            if (!$ranking) {
                $_SESSION['error'] = 'Không tìm thấy Ranking.';
            } else {
                try {
                    $this->rankingModel->delete($id);
                    $_SESSION['success'] = 'Xóa Ranking thành công!';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Không thể xóa do ràng buộc dữ liệu.';
                }
            }
            header('Location: ' . BASE_PATH . '/index.php?controller=seriesRanking&action=index');
            exit;
        }
    }
}
