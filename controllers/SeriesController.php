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
        
        if ($role === 'editor') {
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
     * Xử lý file ảnh bìa upload
     * @return string|null Đường dẫn ảnh bìa nếu thành công, null nếu có lỗi
     */
    private function handleCoverUpload() {
        if (!isset($_FILES['cover_file']) || $_FILES['cover_file']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['cover_file'];
        
        // Kiểm tra kích thước file (tối đa 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "File ảnh bìa vượt quá dung lượng cho phép (2MB).";
            return null;
        }

        // Kiểm tra định dạng
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            $_SESSION['error'] = "Định dạng file không được hỗ trợ. Chỉ cho phép: jpg, jpeg, png, webp";
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
        $newFileName = uniqid('cover_') . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/covers/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $newFileName;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Trả về đường dẫn tương đối để lưu vào DB
            return '/uploads/covers/' . $newFileName;
        }

        $_SESSION['error'] = "Có lỗi xảy ra khi lưu file ảnh bìa.";
        return null;
    }

    /**
     * Xử lý lưu bộ truyện mới vào DB
     */
    public function store() {
        requireRole('mangaka');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            // Validation cơ bản
            if (empty($title)) {
                $_SESSION['error'] = "Tiêu đề truyện không được để trống!";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=create');
                exit;
            }

            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = "Tiêu đề truyện không được vượt quá 255 ký tự!";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=create');
                exit;
            }

            $coverImage = '';
            // Kiểm tra có tải file lên không
            if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedCover = $this->handleCoverUpload();
                if ($uploadedCover) {
                    $coverImage = $uploadedCover;
                } else {
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=create');
                    exit;
                }
            } else {
                $coverImage = trim($_POST['cover_image'] ?? '');
            }

            $data = [
                'mangaka_id'  => $_SESSION['user_id'],
                'title'       => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status'      => 'planning',
                'publish_type'=> 'weekly', // Quy định bởi Editorial Board khi duyệt
                'cover_image' => $coverImage
            ];

            try {
                $this->seriesModel->insert($data);
                $_SESSION['success'] = "Tạo bộ truyện '{$title}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi tạo bộ truyện: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }
    }

    /**
     * Kiểm tra quyền sở hữu truyện của Mangaka
     */
    private function checkOwnership($series, $id) {
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện (ID: {$id}).";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        // Admin, Editor, Board có quyền xem thông tin chi tiết bộ truyện
        if ($role === 'admin' || $role === 'editor' || $role === 'board') {
            return;
        }

        if ($series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện của người khác.";
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
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

            // Validation
            if (empty($title)) {
                $_SESSION['error'] = "Tiêu đề truyện không được để trống!";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=edit&id=' . $id);
                exit;
            }

            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = "Tiêu đề truyện không được vượt quá 255 ký tự!";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=edit&id=' . $id);
                exit;
            }

            $coverImage = $series['cover_image'] ?? '';
            // Kiểm tra có tải file lên không
            if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedCover = $this->handleCoverUpload();
                if ($uploadedCover) {
                    // Xóa file ảnh cũ nếu là file local
                    if (!empty($series['cover_image']) && strpos($series['cover_image'], 'http') !== 0) {
                        $oldFilePath = __DIR__ . '/../' . ltrim($series['cover_image'], '/');
                        if (file_exists($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                    }
                    $coverImage = $uploadedCover;
                } else {
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=edit&id=' . $id);
                    exit;
                }
            } elseif (isset($_POST['cover_image'])) {
                $coverImage = trim($_POST['cover_image']);
            }

            $data = [
                'title'       => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status'      => $series['status'], // Trạng thái giữ nguyên, thuộc quyền Board thay đổi
                'publish_type'=> $series['publish_type'] ?? 'weekly', // Lịch xuất bản giữ nguyên
                'cover_image' => $coverImage
            ];

            try {
                $this->seriesModel->update($id, $data);
                $_SESSION['success'] = "Cập nhật bộ truyện '{$title}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi cập nhật: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
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
                // Đăng nhập các model cần thiết để dọn dẹp file
                require_once __DIR__ . '/../models/Chapter.php';
                require_once __DIR__ . '/../models/Page.php';
                require_once __DIR__ . '/../models/Task.php';
                require_once __DIR__ . '/../models/Submission.php';
                
                $chapterModel = new \Chapter();
                $pageModel = new \Page();
                $taskModel = new \Task();
                $submissionModel = new \Submission();

                // Lấy tất cả chapter thuộc series
                $chapters = $chapterModel->findBySeriesId($id);
                if (!empty($chapters)) {
                    foreach ($chapters as $chapter) {
                        $chapterId = $chapter['chapter_id'];
                        // Lấy tất cả các trang vẽ thuộc chapter và xóa file ảnh + file nộp của task
                        $pages = $pageModel->findByChapterId($chapterId);
                        if (!empty($pages)) {
                            foreach ($pages as $page) {
                                if (!empty($page['image_url'])) {
                                    $filePath = __DIR__ . '/../' . ltrim($page['image_url'], '/');
                                    if (file_exists($filePath)) {
                                        @unlink($filePath);
                                    }
                                }
                                $tasks = $taskModel->findByPageId($page['page_id']);
                                if (!empty($tasks)) {
                                    foreach ($tasks as $task) {
                                        $subs = $submissionModel->findByTaskId($task['task_id']);
                                        if (!empty($subs)) {
                                            foreach ($subs as $sub) {
                                                if (!empty($sub['file_url'])) {
                                                    $subFilePath = __DIR__ . '/../' . ltrim($sub['file_url'], '/');
                                                    if (file_exists($subFilePath)) {
                                                        @unlink($subFilePath);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // Lấy danh sách bản thảo nộp nguyên chương của chapter này và xóa file zip/pdf
                        $chapterSubs = $submissionModel->findByChapterId($chapterId);
                        if (!empty($chapterSubs)) {
                            foreach ($chapterSubs as $cSub) {
                                if (!empty($cSub['file_url'])) {
                                    $cSubFilePath = __DIR__ . '/../' . ltrim($cSub['file_url'], '/');
                                    if (file_exists($cSubFilePath)) {
                                        @unlink($cSubFilePath);
                                    }
                                }
                            }
                        }
                    }
                }

                // Xóa ảnh bìa của bộ truyện
                if (!empty($series['cover_image']) && strpos($series['cover_image'], 'http') !== 0) {
                    $coverPath = __DIR__ . '/../' . ltrim($series['cover_image'], '/');
                    if (file_exists($coverPath)) {
                        @unlink($coverPath);
                    }
                }

                $this->seriesModel->delete($id);
                $_SESSION['success'] = "Đã xóa bộ truyện thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi xóa bộ truyện: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
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
            $publishType = $_POST['publish_type'] ?? 'weekly';
            if (in_array($status, $this->allowedStatuses)) {
                try {
                    $this->seriesModel->update($id, [
                        'status' => $status,
                        'publish_type' => $publishType
                    ]);
                    $_SESSION['success'] = "Cập nhật trạng thái và lịch xuất bản bộ truyện thành công.";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Lỗi khi cập nhật: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Trạng thái không hợp lệ.";
            }

            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
            exit;
        }
    }
}
