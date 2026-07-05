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

        // Chặn sửa đổi phân vùng nếu chapter đã khóa (approved / published)
        if ($chapter['status'] === 'approved' || $chapter['status'] === 'published') {
            return false;
        }

        $series = $this->seriesModel->findById($chapter['series_id']);
        if (!$series) return false;

        return $series['mangaka_id'] == $_SESSION['user_id'];
    }

    /**
     * Hành động chạy quét AI giả lập
     */
    public function runAI() {
        $pageId = $_GET['page_id'] ?? '';
        if (empty($pageId) || !$this->checkPageOwnership($pageId)) {
            $_SESSION['error'] = "Lỗi phân quyền hoặc trang truyện không hợp lệ.";
            header("Location: " . BASE_PATH . "/index.php");
            exit();
        }

        // Xóa các vùng cũ trước khi quét lại (tránh trùng lặp dữ liệu)
        $conn = $this->pageRegionModel->getConnection();
        $stmtDel = $conn->prepare("DELETE FROM page_regions WHERE page_id = :page_id");
        $stmtDel->execute([':page_id' => $pageId]);

        // Gieo hạt giống ngẫu nhiên theo page_id để tạo layout cố định và riêng biệt cho từng trang
        srand(intval($pageId));

        $generatedRegions = [];

        // Định nghĩa 3 hàng dọc (Tiers) trên khung chuẩn 800x1000
        $tiers = [
            ['y' => 50,  'height' => 260], // Tier 1
            ['y' => 360, 'height' => 280], // Tier 2
            ['y' => 690, 'height' => 260]  // Tier 3
        ];

        $panelCount = 0;
        $bubbleCount = 0;
        $characterCount = 0;
        $backgroundCount = 0;
        $sfxCount = 0;

        foreach ($tiers as $tierIndex => $tier) {
            $ty = $tier['y'];
            $th = $tier['height'];

            // Tỷ lệ ngẫu nhiên chia làm 1 panel lớn hoặc 2 panel nhỏ
            $splitTier = (rand(1, 10) > 4); // 60% cơ hội chia đôi cột

            $panels = [];
            if ($splitTier) {
                // Chia đôi cột: Panel trái và Panel phải
                $w1 = rand(300, 360);
                $w2 = 700 - $w1; // Tổng chiều rộng panel là 700px (lề trái 50px, lề phải 50px)
                
                $panels[] = ['x' => 50, 'width' => $w1 - 10]; // Panel trái
                $panels[] = ['x' => 50 + $w1 + 10, 'width' => $w2 - 10]; // Panel phải
            } else {
                // 1 Panel duy nhất chiếm trọn chiều ngang
                $panels[] = ['x' => 50, 'width' => 700];
            }

            foreach ($panels as $p) {
                $px = $p['x'];
                $pw = $p['width'];
                
                // 1. Thêm Panel (Khung truyện)
                $generatedRegions[] = [
                    'page_id' => $pageId,
                    'region_type' => 'panel',
                    'x' => $px,
                    'y' => $ty,
                    'width' => $pw,
                    'height' => $th,
                    'confidence' => number_format(0.95 + (rand(1, 40) / 1000), 4),
                    'is_ai_generated' => 1,
                    'status' => 'pending'
                ];
                $panelCount++;

                // 2. Thêm Nhân vật (Character) - 70% cơ hội
                $hasCharacter = (rand(1, 10) <= 7);
                $cx = 0; $cw = 0;
                if ($hasCharacter) {
                    $cw = round($pw * (rand(45, 65) / 100));
                    $cx = $px + rand(5, $pw - $cw - 5);
                    $ch = round($th * (rand(70, 90) / 100));
                    $cy = $ty + ($th - $ch) - 5; // Sát mép dưới của Panel

                    $generatedRegions[] = [
                        'page_id' => $pageId,
                        'region_type' => 'character',
                        'x' => (int)$cx,
                        'y' => (int)$cy,
                        'width' => (int)$cw,
                        'height' => (int)$ch,
                        'confidence' => number_format(0.88 + (rand(1, 100) / 1000), 4),
                        'is_ai_generated' => 1,
                        'status' => 'pending'
                    ];
                    $characterCount++;
                }

                // 3. Thêm Bong bóng thoại (Bubble) - 80% cơ hội
                $hasBubble = (rand(1, 10) <= 8);
                if ($hasBubble) {
                    $bw = rand(100, 140);
                    $bh = rand(80, 110);
                    
                    // Đặt bong bóng ở góc trên bên trái hoặc góc trên bên phải của Panel để tránh đè nhân vật
                    $placeOnLeft = (rand(1, 2) === 1);
                    if ($placeOnLeft) {
                        $bx = $px + rand(10, 30);
                    } else {
                        $bx = $px + $pw - $bw - rand(10, 30);
                    }
                    $by = $ty + rand(10, 30);

                    $generatedRegions[] = [
                        'page_id' => $pageId,
                        'region_type' => 'bubble',
                        'x' => (int)$bx,
                        'y' => (int)$by,
                        'width' => (int)$bw,
                        'height' => (int)$bh,
                        'confidence' => number_format(0.92 + (rand(1, 70) / 1000), 4),
                        'is_ai_generated' => 1,
                        'status' => 'pending'
                    ];
                    $bubbleCount++;
                }

                // 4. Thêm Bối cảnh/Nền (Background) - 50% cơ hội
                $hasBg = (rand(1, 10) <= 5);
                if ($hasBg) {
                    // Nền thường ở phía sau nhân vật, chiếm một góc trống bên cạnh nhân vật
                    $bgw = round($pw * 0.45);
                    $bgh = round($th * 0.7);
                    
                    // Đặt lệch hướng với nhân vật
                    if ($hasCharacter && $cx > ($px + $pw/2)) {
                        $bgx = $px + rand(5, 20);
                    } else {
                        $bgx = $px + $pw - $bgw - rand(5, 20);
                    }
                    $bgy = $ty + rand(20, 45);

                    $generatedRegions[] = [
                        'page_id' => $pageId,
                        'region_type' => 'background',
                        'x' => (int)$bgx,
                        'y' => (int)$bgy,
                        'width' => (int)$bgw,
                        'height' => (int)$bgh,
                        'confidence' => number_format(0.85 + (rand(1, 100) / 1000), 4),
                        'is_ai_generated' => 1,
                        'status' => 'pending'
                    ];
                    $backgroundCount++;
                }

                // 5. Thêm Hiệu ứng SFX (Hiệu ứng chữ vẽ tay) - 40% cơ hội
                $hasSfx = (rand(1, 10) <= 4);
                if ($hasSfx) {
                    $sw = rand(70, 95);
                    $sh = rand(60, 85);
                    
                    // SFX thường xuất hiện ở giữa Panel hoặc gần nhân vật
                    $sx = $px + ($pw - $sw) / 2 + rand(-30, 30);
                    $sy = $ty + ($th - $sh) / 2 + rand(-20, 20);

                    $generatedRegions[] = [
                        'page_id' => $pageId,
                        'region_type' => 'sfx',
                        'x' => (int)$sx,
                        'y' => (int)$sy,
                        'width' => (int)$sw,
                        'height' => (int)$sh,
                        'confidence' => number_format(0.80 + (rand(1, 150) / 1000), 4),
                        'is_ai_generated' => 1,
                        'status' => 'pending'
                    ];
                    $sfxCount++;
                }
            }
        }

        // Thực hiện chèn toàn bộ vùng ngẫu nhiên vào DB
        foreach ($generatedRegions as $region) {
            $this->pageRegionModel->insert($region);
        }

        // Khôi phục hạt giống ngẫu nhiên hệ thống
        srand();

        $_SESSION['success'] = "Chạy thuật toán AI phân đoạn thành công! Nhận diện được {$panelCount} Khung truyện (Panel), {$bubbleCount} Ô thoại (Bubble), {$characterCount} Nhân vật, {$backgroundCount} Bối cảnh/Nền và {$sfxCount} Hiệu ứng SFX.";
        header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $pageId);
        exit();
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

            $regionType = $_POST['region_type'] ?? '';
            $x = intval($_POST['x'] ?? 0);
            $y = intval($_POST['y'] ?? 0);
            $width = intval($_POST['width'] ?? 0);
            $height = intval($_POST['height'] ?? 0);

            if (!in_array($regionType, ['panel', 'bubble', 'character', 'background', 'sfx']) || $width <= 0 || $height <= 0) {
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
                'confidence' => 1.0000,
                'is_ai_generated' => 0,
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

