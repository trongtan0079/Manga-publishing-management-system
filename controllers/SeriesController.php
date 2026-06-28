<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Chapter.php';


class SeriesController extends BaseController
{
    private $seriesModel;
    private $allowedStatuses = ['planning', 'ongoing', 'completed', 'canceled', 'suspended'];

    public function __construct() {
        parent::__construct();
        // Chỉ yêu cầu đăng nhập ở constructor, phân quyền sẽ xử lý ở từng action
        requireLogin();
        
        // Khởi tạo Model
        $this->seriesModel = new Series();
    }

    /**
     * Hiển thị danh sách các bộ truyện của Mangaka đang đăng nhập
     */
    public function index() {
        $role = $_SESSION['role_name'] ?? '';
        $currentUserId = $_SESSION['user_id'];
        
        if ($role === 'editor' || $role === 'admin') {
            $sql = "SELECT * FROM series ORDER BY series_id DESC";
            $stmt = $this->seriesModel->getConnection()->prepare($sql);
            $stmt->execute();
            $seriesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($role === 'mangaka') {
            $seriesList = $this->seriesModel->findByMangakaId($currentUserId);
        } elseif ($role === 'board') {
            // Board will use the 'publish' action instead, but we allow them here if needed
            $sql = "SELECT * FROM series ORDER BY series_id DESC";
            $stmt = $this->seriesModel->getConnection()->prepare($sql);
            $stmt->execute();
            $seriesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền truy cập.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }
        
        require_once __DIR__ . '/../views/mangaka/series.php';
    }

    /**
     * Hiển thị form tạo bộ truyện mới
     */
    public function create() {
        requireRole('mangaka');
        require_once __DIR__ . '/../views/mangaka/series_create.php';
    }

    /**
     * Xử lý lưu bộ truyện mới vào DB
     */
    public function store() {
        requireRole('mangaka');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'planning';

            // Validation cơ bản
            if (empty($title)) {
                $_SESSION['error'] = "Tiêu đề truyện không được để trống!";
                header('Location: /index.php?controller=series&action=create');
                exit;
            }

            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = "Tiêu đề truyện không được vượt quá 255 ký tự!";
                header('Location: /index.php?controller=series&action=create');
                exit;
            }

            if (!in_array($status, $this->allowedStatuses)) {
                $_SESSION['error'] = "Trạng thái truyện không hợp lệ!";
                header('Location: /index.php?controller=series&action=create');
                exit;
            }

            $data = [
                'mangaka_id'  => $_SESSION['user_id'],
                'title'       => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status'      => $status,
                'cover_image' => trim($_POST['cover_image'] ?? '')
            ];

            try {
                $this->seriesModel->insert($data);
                $_SESSION['success'] = "Tạo bộ truyện '{$title}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi tạo bộ truyện: " . $e->getMessage();
            }
            
            header('Location: /index.php?controller=series&action=index');
            exit;
        }
    }

    /**
     * Kiểm tra quyền sở hữu truyện của Mangaka
     */
    private function checkOwnership($series, $id) {
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện (ID: {$id}).";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor' || $role === 'admin') {
            return; // Editor và Admin có quyền xem
        }

        if ($series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện của người khác.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }
    }

    /**
     * Hiển thị form chỉnh sửa bộ truyện
     */
    public function edit($id) {
        requireRole('mangaka');
        $series = $this->seriesModel->findById($id);
        $this->checkOwnership($series, $id);
        
        require_once __DIR__ . '/../views/mangaka/series_edit.php';
    }

    /**
     * Cập nhật thông tin bộ truyện
     */
    public function update($id) {
        requireRole('mangaka');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $series = $this->seriesModel->findById($id);
            $this->checkOwnership($series, $id);

            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'planning';

            // Validation
            if (empty($title)) {
                $_SESSION['error'] = "Tiêu đề truyện không được để trống!";
                header('Location: /index.php?controller=series&action=edit&id=' . $id);
                exit;
            }

            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = "Tiêu đề truyện không được vượt quá 255 ký tự!";
                header('Location: /index.php?controller=series&action=edit&id=' . $id);
                exit;
            }

            if (!in_array($status, $this->allowedStatuses)) {
                $_SESSION['error'] = "Trạng thái truyện không hợp lệ!";
                header('Location: /index.php?controller=series&action=edit&id=' . $id);
                exit;
            }

            $data = [
                'title'       => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status'      => $status,
                'cover_image' => trim($_POST['cover_image'] ?? '')
            ];

            try {
                $this->seriesModel->update($id, $data);
                $_SESSION['success'] = "Cập nhật bộ truyện '{$title}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi cập nhật: " . $e->getMessage();
            }
            
            header('Location: /index.php?controller=series&action=index');
            exit;
        }
    }

    /**
     * Xem chi tiết bộ truyện
     */
    public function show($id) {
        $series = $this->seriesModel->findById($id);
        $this->checkOwnership($series, $id);
        
        $chapterModel = new Chapter();
        $chapters = $chapterModel->findBySeriesId($id);
        
        require_once __DIR__ . '/../views/mangaka/series_detail.php';
    }

    /**
     * Xóa bộ truyện
     */
    public function delete($id) {
        requireRole('mangaka');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $series = $this->seriesModel->findById($id);
            $this->checkOwnership($series, $id);

            try {
                $this->seriesModel->delete($id);
                $_SESSION['success'] = "Đã xóa bộ truyện thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Không thể xóa bộ truyện vì dữ liệu đang liên kết (Ví dụ: Các Chapter hoặc trang truyện).";
            }
            
            header('Location: /index.php?controller=series&action=index');
            exit;
        }
    }
    /**
     * Xem danh sách Series để xuất bản (Dành cho Editorial Board)
     */
    public function publish() {
        requireRole('board');
        
        // Lấy danh sách truyện đang chờ duyệt (planning) và đang xuất bản (ongoing)
        $sql = "SELECT s.*, u.full_name as mangaka_name 
                FROM series s 
                JOIN users u ON s.mangaka_id = u.user_id 
                WHERE s.status IN ('planning', 'ongoing') 
                ORDER BY s.created_at DESC";
        $stmt = $this->seriesModel->getConnection()->prepare($sql);
        $stmt->execute();
        $seriesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/board/publish_series.php';
    }

    /**
     * Cập nhật trạng thái Series (Dành cho Editorial Board duyệt xuất bản)
     */
    public function updateStatus($id) {
        requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $series = $this->seriesModel->findById($id);
            if (!$series) {
                $_SESSION['error'] = "Không tìm thấy bộ truyện.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                exit;
            }

            $status = $_POST['status'] ?? '';
            if (in_array($status, $this->allowedStatuses)) {
                try {
                    $this->seriesModel->update($id, ['status' => $status]);
                    $_SESSION['success'] = "Cập nhật trạng thái bộ truyện thành công.";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Trạng thái không hợp lệ.";
            }

            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
            exit;
        }
    }
}
