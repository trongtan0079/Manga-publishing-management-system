<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Page.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Task.php';


class PageController extends BaseController
{
    private $pageModel;
    private $chapterModel;
    private $seriesModel;
    private $taskModel;
    
    // Giới hạn file ảnh 2MB
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;
    
    // Các định dạng ảnh được phép
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    
    // Trạng thái hợp lệ của page
    private $allowedStatuses = ['drafting', 'drawing', 'reviewing', 'approved', 'published'];

    public function __construct() {
        parent::__construct();
        \requireLogin();
        // Chỉ Mangaka mới được thao tác trong module Page
        \requireRole('mangaka');
        
        $this->pageModel = new Page();
        $this->chapterModel = new Chapter();
        $this->seriesModel = new Series();
        $this->taskModel = new Task();
    }

    /**
     * Hàm kiểm tra quyền sở hữu chapter
     * Đảm bảo mangaka chỉ có thể thao tác trên chapter thuộc series của họ
     */
    private function checkChapterOwnership($chapterId) {
        $chapter = $this->chapterModel->findById($chapterId);
        if (!$chapter) {
            $_SESSION['error'] = "Không tìm thấy chapter.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        $series = $this->seriesModel->findById($chapter['series_id']);
        if (!$series || $series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên chapter này.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        return ['chapter' => $chapter, 'series' => $series];
    }

    /**
     * Xử lý file ảnh upload
     * @return string|null Đường dẫn ảnh nếu thành công, null nếu có lỗi
     */
    private function handleImageUpload() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['image'];
        
        // Kiểm tra kích thước file
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $_SESSION['error'] = "File ảnh vượt quá dung lượng cho phép (2MB).";
            return null;
        }

        // Kiểm tra định dạng
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $_SESSION['error'] = "Định dạng file không được hỗ trợ. Chỉ cho phép: " . implode(', ', self::ALLOWED_EXTENSIONS);
            return null;
        }

        // Kiểm tra MIME type thực sự
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $_SESSION['error'] = "File tải lên không phải là định dạng ảnh hợp lệ.";
            return null;
        }

        // Tạo tên file ngẫu nhiên để tránh trùng lặp
        $newFileName = uniqid('page_') . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/pages/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $newFileName;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Trả về đường dẫn tương đối để lưu vào DB
            return '/uploads/pages/' . $newFileName;
        }

        $_SESSION['error'] = "Có lỗi xảy ra khi lưu file ảnh.";
        return null;
    }

    public function index() {
        // Rediect về series
        header('Location: /index.php?controller=series&action=index');
        exit;
    }

    /**
     * Hiển thị form tạo trang mới
     */
    public function create() {
        $chapterId = $_GET['chapter_id'] ?? null;
        if (!$chapterId) {
            $_SESSION['error'] = "Thiếu thông tin chapter.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        $ownership = $this->checkChapterOwnership($chapterId);
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];
        
        require_once __DIR__ . '/../views/mangaka/page_create.php';
    }

    /**
     * Lưu trang mới vào DB
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapterId = $_POST['chapter_id'] ?? null;
            if (!$chapterId) {
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            // Kiểm tra quyền sở hữu
            $this->checkChapterOwnership($chapterId);

            $pageNumber = trim($_POST['page_number'] ?? '');
            $status = $_POST['status'] ?? 'drafting';

            // Validation: page_number bắt buộc và > 0
            if ($pageNumber === '' || !is_numeric($pageNumber) || $pageNumber <= 0) {
                $_SESSION['error'] = "Số trang (Page Number) bắt buộc và phải lớn hơn 0.";
                header("Location: /index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            // Validation: không trùng page_number trong cùng chapter
            if ($this->pageModel->isPageNumberExists($chapterId, $pageNumber)) {
                $_SESSION['error'] = "Số trang {$pageNumber} đã tồn tại trong chapter này.";
                header("Location: /index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            if (!in_array($status, $this->allowedStatuses)) {
                $_SESSION['error'] = "Trạng thái trang không hợp lệ!";
                header("Location: /index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            // Xử lý upload ảnh
            $imageUrl = $this->handleImageUpload();
            if (!$imageUrl) {
                if (!isset($_SESSION['error'])) {
                    $_SESSION['error'] = "Vui lòng chọn ảnh cho trang truyện.";
                }
                header("Location: /index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            $data = [
                'chapter_id' => $chapterId,
                'page_number' => $pageNumber,
                'image_url' => $imageUrl,
                'status' => $status
            ];

            try {
                $this->pageModel->insert($data);
                $_SESSION['success'] = "Tạo trang {$pageNumber} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }
            
            header("Location: /index.php?controller=chapter&action=show&id={$chapterId}");
            exit;
        }
    }

    /**
     * Hiển thị chi tiết một trang
     */
    public function show($id) {
        $page = $this->pageModel->findById($id);
        if (!$page) {
            $_SESSION['error'] = "Không tìm thấy trang.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        // Ủy quyền
        $ownership = $this->checkChapterOwnership($page['chapter_id']);
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];

        $tasks = $this->taskModel->findByPageId($id);

        require_once __DIR__ . '/../views/mangaka/page_detail.php';
    }

    /**
     * Hiển thị form sửa trang
     */
    public function edit($id) {
        $page = $this->pageModel->findById($id);
        if (!$page) {
            $_SESSION['error'] = "Không tìm thấy trang.";
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        // Ủy quyền
        $ownership = $this->checkChapterOwnership($page['chapter_id']);
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];

        require_once __DIR__ . '/../views/mangaka/page_edit.php';
    }

    /**
     * Cập nhật thông tin trang
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page = $this->pageModel->findById($id);
            if (!$page) {
                $_SESSION['error'] = "Không tìm thấy trang.";
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            $chapterId = $page['chapter_id'];
            
            // Ủy quyền
            $this->checkChapterOwnership($chapterId);

            $pageNumber = trim($_POST['page_number'] ?? '');
            $status = $_POST['status'] ?? 'drafting';

            // Validation: page_number bắt buộc và > 0
            if ($pageNumber === '' || !is_numeric($pageNumber) || $pageNumber <= 0) {
                $_SESSION['error'] = "Số trang bắt buộc và phải lớn hơn 0.";
                header("Location: /index.php?controller=page&action=edit&id={$id}");
                exit;
            }

            // Validation: không trùng page_number (ngoại trừ chính nó)
            if ($this->pageModel->isPageNumberExists($chapterId, $pageNumber, $id)) {
                $_SESSION['error'] = "Số trang {$pageNumber} đã tồn tại trong chapter này.";
                header("Location: /index.php?controller=page&action=edit&id={$id}");
                exit;
            }

            if (!in_array($status, $this->allowedStatuses)) {
                $_SESSION['error'] = "Trạng thái trang không hợp lệ!";
                header("Location: /index.php?controller=page&action=edit&id={$id}");
                exit;
            }

            $data = [
                'page_number' => $pageNumber,
                'status' => $status
            ];

            // Nếu có upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload();
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                    // Yêu cầu: Không xóa file cũ.
                } else {
                    // Xảy ra lỗi upload (dung lượng, định dạng)
                    header("Location: /index.php?controller=page&action=edit&id={$id}");
                    exit;
                }
            }

            try {
                $this->pageModel->update($id, $data);
                $_SESSION['success'] = "Cập nhật trang {$pageNumber} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }
            
            header("Location: /index.php?controller=chapter&action=show&id={$chapterId}");
            exit;
        }
    }

    /**
     * Xóa một trang
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page = $this->pageModel->findById($id);
            if (!$page) {
                $_SESSION['error'] = "Không tìm thấy trang.";
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            $chapterId = $page['chapter_id'];
            
            // Ủy quyền
            $this->checkChapterOwnership($chapterId);

            try {
                // Xóa file vật lý trước
                $filePath = __DIR__ . '/../' . ltrim($page['image_url'], '/');
                if (!empty($page['image_url']) && file_exists($filePath)) {
                    unlink($filePath);
                }

                // Xóa record DB
                $this->pageModel->delete($id);
                $_SESSION['success'] = "Đã xóa trang thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi xóa trang: " . $e->getMessage();
            }
            
            header("Location: /index.php?controller=chapter&action=show&id={$chapterId}");
            exit;
        }
    }
}
