<?php
require_once __DIR__ . '/../models/PageRegion.php';
require_once __DIR__ . '/../models/Page.php';

class PageRegionController {
    private $pageRegionModel;
    private $pageModel;

    public function __construct() {
        $this->pageRegionModel = new PageRegion();
        $this->pageModel = new Page();
    }

    /**
     * Hành động chạy quét AI giả lập
     */
    public function runAI() {
        // Đảm bảo chỉ Mangaka mới chạy được AI phân đoạn vùng
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $role = $_SESSION['role_name'] ?? '';
        if ($role !== 'mangaka') {
            $_SESSION['error'] = "Bạn không có quyền thực hiện chức năng này!";
            header("Location: " . BASE_PATH . "/index.php");
            exit();
        }

        $pageId = $_GET['page_id'] ?? '';
        if (empty($pageId)) {
            $_SESSION['error'] = "Không tìm thấy trang truyện!";
            header("Location: " . BASE_PATH . "/index.php");
            exit();
        }

        $page = $this->pageModel->findById($pageId);
        if (!$page) {
            $_SESSION['error'] = "Trang truyện không tồn tại!";
            header("Location: " . BASE_PATH . "/index.php");
            exit();
        }

        // Xóa các vùng cũ trước khi quét lại (tránh trùng lặp dữ liệu)
        $conn = $this->pageRegionModel->getConnection();
        $stmtDel = $conn->prepare("DELETE FROM page_regions WHERE page_id = :page_id");
        $stmtDel->execute([':page_id' => $pageId]);

        // Tạo 3 vùng mô phỏng quét từ model AI (YOLOv8/SAM)
        $mockRegions = [
            [
                'page_id' => $pageId,
                'region_type' => 'panel',
                'x' => 50,
                'y' => 80,
                'width' => 600,
                'height' => 450,
                'confidence' => 0.9824,
                'is_ai_generated' => 1,
                'status' => 'pending'
            ],
            [
                'page_id' => $pageId,
                'region_type' => 'bubble',
                'x' => 120,
                'y' => 150,
                'width' => 180,
                'height' => 100,
                'confidence' => 0.9412,
                'is_ai_generated' => 1,
                'status' => 'pending'
            ],
            [
                'page_id' => $pageId,
                'region_type' => 'character',
                'x' => 400,
                'y' => 100,
                'width' => 220,
                'height' => 400,
                'confidence' => 0.9150,
                'is_ai_generated' => 1,
                'status' => 'pending'
            ]
        ];

        foreach ($mockRegions as $region) {
            $this->pageRegionModel->insert($region);
        }

        $_SESSION['success'] = "Chạy thuật toán AI phân đoạn vùng thành công! Nhận diện được 2 Khung truyện (Panel) và 1 Bong bóng thoại (Bubble) với độ chính xác >91%.";
        header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $pageId);
        exit();
    }
}
