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
        // Chỉ cho phép Mangaka truy cập toàn bộ các chức năng trong controller này
        requireRole('mangaka');
        
        // Khởi tạo Model
        $this->seriesModel = new Series();
    }

    /**
     * Hiển thị danh sách các bộ truyện của Mangaka đang đăng nhập
     */
    public function index() {
        $currentUserId = $_SESSION['user_id'];
        $seriesList = $this->seriesModel->findByMangakaId($currentUserId);
        
        require_once __DIR__ . '/../views/mangaka/series.php';
    }

    /**
     * Hiển thị form tạo bộ truyện mới
     */
    public function create() {
        require_once __DIR__ . '/../views/mangaka/series_create.php';
    }

    /**
     * Xử lý lưu bộ truyện mới vào DB
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'planning';

            // Validation cơ bản
            if (empty($title)) {
                $_SESSION['error'] = "Tiêu đề truyện không được để trống!";
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
        $series = $this->seriesModel->findById($id);
        $this->checkOwnership($series, $id);
        
        require_once __DIR__ . '/../views/mangaka/series_edit.php';
    }

    /**
     * Cập nhật thông tin bộ truyện
     */
    public function update($id) {
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
}
