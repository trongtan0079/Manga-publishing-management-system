<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/PageRegion.php';
require_once __DIR__ . '/../models/Page.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Series.php';

class PageRegionController extends BaseController {
    private $pageRegionModel;
    private $pageModel;
    private $chapterModel;
    private $seriesModel;

    public function __construct() {
        parent::__construct();
        \requireLogin();
        \requireRole('mangaka');
        
        $this->pageRegionModel = new PageRegion();
        $this->pageModel = new Page();
        $this->chapterModel = new Chapter();
        $this->seriesModel = new Series();
    }

    /**
     * Hàm dùng chung: Kiểm tra xem trang truyện (Page) có thuộc quyền sở hữu của Mangaka hiện tại hay không.
     */
    private function checkPageOwnership($pageId) {
        $page = $this->pageModel->findById($pageId);
        if (!$page) return false;

        $chapter = $this->chapterModel->findById($page['chapter_id']);
        if (!$chapter) return false;

        // Chặn sửa đổi phân vùng nếu chapter đang chờ duyệt, đã duyệt hoặc đã xuất bản
        if (in_array($chapter['status'], ['reviewing', 'approved', 'published'])) {
            return false;
        }

        $series = $this->seriesModel->findById($chapter['series_id']);
        if (!$series) return false;

        // Chặn sửa đổi phân vùng nếu bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành
        if (in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
            return false;
        }

        return $series['mangaka_id'] == $_SESSION['user_id'];
    }


    /**
     * Lưu phân vùng thủ công do người dùng vẽ
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pageId = $_POST['page_id'] ?? '';
            if (empty($pageId) || !$this->checkPageOwnership($pageId)) {
                $_SESSION['error'] = "Lỗi phân quyền hoặc trang truyện không hợp lệ.";
                header("Location: " . BASE_PATH . "/index.php");
                exit();
            }

            $regionType = trim($_POST['region_type'] ?? '');
            if ($regionType === 'other' && !empty($_POST['custom_region_type'])) {
                $regionType = trim($_POST['custom_region_type']);
            }
            $x = intval($_POST['x'] ?? 0);
            $y = intval($_POST['y'] ?? 0);
            $width = intval($_POST['width'] ?? 0);
            $height = intval($_POST['height'] ?? 0);

            if (empty($regionType) || strlen($regionType) > 50 || $width <= 0 || $height <= 0) {
                $_SESSION['error'] = "Dữ liệu phân vùng không hợp lệ!";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $pageId);
                exit();
            }

            $data = [
                'page_id' => $pageId,
                'region_type' => $regionType,
                'x' => $x,
                'y' => $y,
                'width' => $width,
                'height' => $height,
                'status' => 'pending'
            ];

            try {
                $this->pageRegionModel->insert($data);
                $_SESSION['success'] = "Đã lưu phân vùng thủ công mới thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi lưu phân vùng: " . $e->getMessage();
            }

            header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $pageId);
            exit();
        }
    }

    /**
     * Xóa phân vùng
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $region = $this->pageRegionModel->findById($id);
            if (!$region || !$this->checkPageOwnership($region['page_id'])) {
                $_SESSION['error'] = "Lỗi phân quyền hoặc phân vùng không hợp lệ.";
                header("Location: " . BASE_PATH . "/index.php");
                exit();
            }

            $pageId = $region['page_id'];

            try {
                $this->pageRegionModel->delete($id);
                $_SESSION['success'] = "Đã xóa phân vùng thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi xóa phân vùng: " . $e->getMessage();
            }

            header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $pageId);
            exit();
        } else {
            $_SESSION['error'] = "Phương thức không được hỗ trợ.";
            header("Location: " . BASE_PATH . "/index.php");
            exit();
        }
    }
}

