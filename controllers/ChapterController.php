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
        $action = $_GET['action'] ?? '';
        $series = $this->seriesModel->findById($seriesId);
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        // Admin, Board có quyền xem chi tiết bộ truyện/chapter
        if ($role === 'admin' || $role === 'board') {
            return $series;
        }

        if ($role === 'editor') {
            // Editor chỉ được xem nếu được gán phụ trách và bộ truyện đã được duyệt (status !== 'planning')
            if ($series['editor_id'] == $_SESSION['user_id'] && $series['status'] !== 'planning') {
                return $series;
            }
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không được phân công quản lý bộ truyện này.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        if ($series['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện này.";
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }

        // Chỉ cho phép tạo chapter mới khi bộ truyện đã được duyệt hoạt động (ongoing)
        if (($action === 'create' || $action === 'store') && $series['status'] !== 'ongoing') {
            $_SESSION['error'] = "Bộ truyện chưa được phê duyệt (Đang ở giai đoạn Kế hoạch/Nháp), hoặc đang tạm ngưng, đã hủy. Không thể tạo thêm chapter mới.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $seriesId);
            exit;
        }

        // Chặn sửa/xóa/nộp chapter nếu bộ truyện đang tạm ngưng, đã hủy hoặc đã hoàn thành
        if (in_array($action, ['edit', 'update', 'delete', 'submit']) && in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
            $_SESSION['error'] = "Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể chỉnh sửa hoặc thao tác trên các chương.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $seriesId);
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

        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'mangaka') {
            $chapters = $this->chapterModel->findByMangakaId($_SESSION['user_id']);
            require_once __DIR__ . '/../views/mangaka/chapter_list.php';
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

            if (!in_array($status, ['drafting', 'drawing'])) {
                $_SESSION['error'] = "Trạng thái chapter khởi tạo không hợp lệ! Chỉ cho phép Bản nháp hoặc Đang vẽ.";
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

            $isFinal = isset($_POST['is_final']) ? 1 : 0;
            if ($isFinal) {
                // Kiểm tra xem bộ truyện đã có chapter cuối nào khác chưa
                $sql = "SELECT COUNT(*) FROM chapters WHERE series_id = :series_id AND is_final = 1";
                $stmt = $this->chapterModel->getConnection()->prepare($sql);
                $stmt->bindParam(':series_id', $seriesId);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['error'] = "Bộ truyện này đã có một chapter được đánh dấu là chương cuối rồi!";
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                    exit;
                }
            }

            $data = [
                'series_id' => $seriesId,
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'due_date' => $formattedDueDate,
                'status' => $status,
                'is_final' => $isFinal
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

        // Khóa truy cập trang sửa nếu chương đang chờ duyệt, đã duyệt hoặc đã xuất bản
        if (in_array($chapter['status'], ['reviewing', 'approved', 'published'])) {
            $_SESSION['error'] = "Chương truyện đang trong quá trình duyệt, đã phê duyệt hoặc đã xuất bản, không thể chỉnh sửa.";
            header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id=" . $chapter['series_id']);
            exit;
        }

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

            // Khóa cập nhật nếu chương đang chờ duyệt, đã duyệt hoặc đã xuất bản
            if (in_array($chapter['status'], ['reviewing', 'approved', 'published'])) {
                $_SESSION['error'] = "Chương truyện đang trong quá trình duyệt, đã phê duyệt hoặc đã xuất bản, không thể chỉnh sửa.";
                header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id={$seriesId}");
                exit;
            }

            $allowed = ['drafting', 'drawing', 'reviewing'];
            if (!in_array($status, $allowed)) {
                $_SESSION['error'] = "Trạng thái chapter không hợp lệ!";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                exit;
            }

            // Ngăn nộp chương rỗng chưa có bất kỳ trang nào hoặc vẫn còn task chưa xong
            if ($status === 'reviewing') {
                $pages = $this->pageModel->findByChapterId($id);
                if (empty($pages)) {
                    $_SESSION['error'] = "Chương truyện phải có ít nhất 1 trang vẽ mới có thể nộp duyệt.";
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                    exit;
                }

                require_once __DIR__ . '/../models/Task.php';
                $taskModel = new \Task();
                $tasks = $taskModel->findTasksByChapterId($id);
                if (!empty($tasks)) {
                    foreach ($tasks as $task) {
                        if ($task['status'] !== 'completed') {
                            $_SESSION['error'] = "Không thể nộp duyệt chương truyện khi vẫn còn công việc (Task) chưa hoàn thành của các Trợ lý.";
                            header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                            exit;
                        }
                    }
                }
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

            $isFinal = isset($_POST['is_final']) ? 1 : 0;
            if ($isFinal) {
                // Kiểm tra xem bộ truyện đã có chapter cuối nào khác chưa (ngoại trừ chính chapter này)
                $sql = "SELECT COUNT(*) FROM chapters WHERE series_id = :series_id AND is_final = 1 AND chapter_id != :chapter_id";
                $stmt = $this->chapterModel->getConnection()->prepare($sql);
                $stmt->bindParam(':series_id', $seriesId);
                $stmt->bindParam(':chapter_id', $id);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['error'] = "Bộ truyện này đã có một chapter khác được đánh dấu là chương cuối rồi!";
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                    exit;
                }
            }

            $data = [
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'due_date' => $formattedDueDate,
                'status' => $status,
                'is_final' => $isFinal
            ];

            $oldStatus = $chapter['status'];
            $newStatus = $status;
            $shouldNotify = ($oldStatus === 'drafting' && ($newStatus === 'drawing' || $newStatus === 'reviewing'));

            try {
                $this->chapterModel->update($id, $data);
                
                // Nếu kích hoạt chương từ Bản nháp sang Đang vẽ/Chờ duyệt, gửi thông báo cho Trợ lý
                if ($shouldNotify) {
                    require_once __DIR__ . '/../models/Task.php';
                    require_once __DIR__ . '/../models/Notification.php';
                    $taskModel = new \Task();
                    $notificationModel = new \Notification();
                    
                    $tasks = $taskModel->findTasksByChapterId($id);
                    if (!empty($tasks)) {
                        foreach ($tasks as $task) {
                            $notificationModel->createNotification(
                                $task['assistant_id'],
                                'task_assigned',
                                "Bạn được giao công việc mới: '{$task['title']}' thuộc bộ truyện '{$task['series_title']}' (Chương {$chapterNumber})."
                            );
                        }
                    }
                }
                
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

            // Khóa xóa nếu chương đang chờ duyệt, đã duyệt hoặc đã xuất bản
            if (in_array($chapter['status'], ['reviewing', 'approved', 'published'])) {
                $_SESSION['error'] = "Chương truyện đang trong quá trình duyệt, đã phê duyệt hoặc đã xuất bản, không thể xóa.";
                header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id={$seriesId}");
                exit;
            }

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
