<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Page.php';


class ChapterController extends BaseController
{
    private $chapterModel;
    private $seriesModel;
    private $pageModel;
    
    // Các trạng thái (status) hợp lệ của một chapter tương ứng với CSDL
    private $allowedStatuses = ['drafting', 'drawing', 'reviewing', 'approved', 'published'];

    public function __construct() {
        parent::__construct();
        requireLogin();
        
        $action = $_GET['action'] ?? 'index';
        $allowedViewRoles = ['mangaka', 'editor', 'board', 'admin'];
        
        if (in_array($action, ['show', 'index'])) {
            if (!in_array($_SESSION['role_name'], $allowedViewRoles)) {
                http_response_code(403);
                echo "Access Denied: You do not have the required role to access this page.";
                exit;
            }
        } else {
            requireRole('mangaka');
        }
        
        $this->chapterModel = new Chapter();
        $this->seriesModel = new Series();
        $this->pageModel = new Page();
    }

    /**
     * Hàm dùng chung: Kiểm tra xem bộ truyện (Series) có thuộc quyền sở hữu của Mangaka hiện tại hay không.
     * Tránh việc Mangaka này sửa/xóa chapter của Mangaka khác.
     * 
     * @param int $seriesId ID của bộ truyện
     * @return array Trả về thông tin của Series nếu hợp lệ
     */
    private function checkSeriesOwnership($seriesId) {
        $series = $this->seriesModel->findById($seriesId);
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        // Admin, Editor, Board có quyền xem chi tiết bộ truyện/chapter
        if ($role === 'admin' || $role === 'editor' || $role === 'board') {
            return $series;
        }

        if ($series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện này.";
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }
        return $series;
    }

    /**
     * Mặc định khi truy cập /index.php?controller=chapter
     * Sẽ chuyển hướng (redirect) về trang chi tiết của bộ truyện nếu có series_id
     */
    public function index() {
        $seriesId = $_GET['series_id'] ?? null;
        if ($seriesId) {
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $seriesId);
            exit;
        }
        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
        exit;
    }

    /**
     * Hiển thị Form tạo Chapter mới
     */
    public function create() {
        $seriesId = $_GET['series_id'] ?? null;
        if (!$seriesId) {
            $_SESSION['error'] = "Thiếu thông tin bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($seriesId);
        require_once __DIR__ . '/../views/mangaka/chapter_create.php';
    }

    /**
     * Xử lý dữ liệu POST khi submit Form tạo Chapter
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seriesId = $_POST['series_id'] ?? null;
            if (!$seriesId) {
                $_SESSION['error'] = "Thiếu thông tin bộ truyện.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            $series = $this->checkSeriesOwnership($seriesId);

            $chapterNumber = trim($_POST['chapter_number'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'drafting';

            // Validation
            if ($chapterNumber === '' || !is_numeric($chapterNumber) || $chapterNumber <= 0) {
                $_SESSION['error'] = "Chapter Number bắt buộc và phải lớn hơn 0.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = "Tiêu đề chapter không được vượt quá 255 ký tự!";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            if ($this->chapterModel->isChapterNumberExists($seriesId, $chapterNumber)) {
                $_SESSION['error'] = "Chapter Number {$chapterNumber} đã tồn tại trong bộ truyện này.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            if (!in_array($status, ['drafting', 'drawing', 'reviewing'])) {
                $_SESSION['error'] = "Trạng thái chapter không hợp lệ!";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                exit;
            }

            $formattedDueDate = null;
            if (!empty($_POST['due_date'])) {
                $dueTimestamp = strtotime($_POST['due_date']);
                if ($dueTimestamp === false) {
                    $_SESSION['error'] = 'Hạn chót (Due Date) không đúng định dạng.';
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                    exit;
                }
                $formattedDueDate = date('Y-m-d H:i:s', $dueTimestamp);
            }

            $data = [
                'series_id' => $seriesId,
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'due_date' => $formattedDueDate,
                'status' => $status
            ];

            try {
                $this->chapterModel->insert($data);
                $_SESSION['success'] = "Tạo chapter {$chapterNumber} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi tạo chapter: " . $e->getMessage();
            }
            
            header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id={$seriesId}");
            exit;
        }
    }

    /**
     * Hiển thị Form chỉnh sửa Chapter
     * 
     * @param int $id ID của Chapter cần sửa
     */
    public function edit($id) {
        $chapter = $this->chapterModel->findById($id);
        if (!$chapter) {
            $_SESSION['error'] = "Không tìm thấy chapter.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($chapter['series_id']);
        require_once __DIR__ . '/../views/mangaka/chapter_edit.php';
    }

    /**
     * Xử lý dữ liệu POST khi submit Form chỉnh sửa Chapter
     * 
     * @param int $id ID của Chapter cần sửa
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapter = $this->chapterModel->findById($id);
            if (!$chapter) {
                $_SESSION['error'] = "Không tìm thấy chapter.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            $seriesId = $chapter['series_id'];
            $series = $this->checkSeriesOwnership($seriesId);

            $chapterNumber = trim($_POST['chapter_number'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $status = $_POST['status'] ?? 'drafting';

            // Validation
            if ($chapterNumber === '' || !is_numeric($chapterNumber) || $chapterNumber <= 0) {
                $_SESSION['error'] = "Chapter Number bắt buộc và phải lớn hơn 0.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = "Tiêu đề chapter không được vượt quá 255 ký tự!";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            if ($this->chapterModel->isChapterNumberExists($seriesId, $chapterNumber, $id)) {
                $_SESSION['error'] = "Chapter Number {$chapterNumber} đã tồn tại trong bộ truyện này.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            $allowed = ['drafting', 'drawing', 'reviewing'];
            if ($chapter['status'] === 'approved' || $chapter['status'] === 'published') {
                $allowed[] = $chapter['status'];
            }
            if (!in_array($status, $allowed)) {
                $_SESSION['error'] = "Trạng thái chapter không hợp lệ!";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            $formattedDueDate = null;
            if (!empty($_POST['due_date'])) {
                $dueTimestamp = strtotime($_POST['due_date']);
                if ($dueTimestamp === false) {
                    $_SESSION['error'] = 'Hạn chót (Due Date) không đúng định dạng.';
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                    exit;
                }
                $formattedDueDate = date('Y-m-d H:i:s', $dueTimestamp);
            }

            $data = [
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'due_date' => $formattedDueDate,
                'status' => $status
            ];

            try {
                $this->chapterModel->update($id, $data);
                $_SESSION['success'] = "Cập nhật chapter {$chapterNumber} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi cập nhật chapter: " . $e->getMessage();
            }
            
            header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id={$seriesId}");
            exit;
        }
    }

    /**
     * Hiển thị chi tiết của một Chapter (sẽ dùng để quản lý trang truyện/ảnh sau này)
     * 
     * @param int $id ID của Chapter
     */
    public function show($id) {
        $chapter = $this->chapterModel->findById($id);
        if (!$chapter) {
            $_SESSION['error'] = "Không tìm thấy chapter.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($chapter['series_id']);
        
        // Lấy danh sách các trang của chapter này
        $pages = $this->pageModel->findByChapterId($id);

        require_once __DIR__ . '/../views/mangaka/chapter_detail.php';
    }

    /**
     * Xử lý xóa một Chapter
     * 
     * @param int $id ID của Chapter cần xóa
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapter = $this->chapterModel->findById($id);
            if (!$chapter) {
                $_SESSION['error'] = "Không tìm thấy chapter.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            $seriesId = $chapter['series_id'];
            $this->checkSeriesOwnership($seriesId);

            try {
                // Đăng nhập các model cần thiết để dọn dẹp file
                require_once __DIR__ . '/../models/Task.php';
                require_once __DIR__ . '/../models/Submission.php';
                $taskModel = new \Task();
                $submissionModel = new \Submission();

                // 1. Lấy danh sách các trang vẽ thuộc chapter này và xóa file ảnh + file nộp của task
                $pages = $this->pageModel->findByChapterId($id);
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

                // 2. Lấy danh sách bản thảo nộp nguyên chương của chapter này và xóa file zip/pdf
                $chapterSubs = $submissionModel->findByChapterId($id);
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

                $this->chapterModel->delete($id);
                $_SESSION['success'] = "Đã xóa chapter thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi xóa chapter: " . $e->getMessage();
            }
            
            header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id={$seriesId}");
            exit;
        }
    }
}
