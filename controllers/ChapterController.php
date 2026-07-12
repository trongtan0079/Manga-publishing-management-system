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
    private $allowedStatuses = ['drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published'];

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
        } elseif ($action === 'publish') {
            requireRole('board');
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
        if (!$this->hasSeriesAccess($series)) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện này.";
            if ($role === 'editor') {
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            } else {
                header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            }
            exit;
        }

        // Chỉ cho phép tạo chapter mới khi bộ truyện đã được duyệt hoạt động chính thức (ongoing)
        if (($action === 'create' || $action === 'store') && !in_array($series['status'], ['ongoing'])) {
            $_SESSION['error'] = "Bộ truyện chưa được phê duyệt hoặc đang tạm ngưng, đã hủy. Không thể tạo thêm chapter mới.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $seriesId);
            exit;
        }

        // Chặn sửa/xóa/nộp chapter nếu bộ truyện không hoạt động chính thức (ongoing)
        if (in_array($action, ['edit', 'update', 'delete', 'submit']) && $series['status'] !== 'ongoing') {
            $_SESSION['error'] = "Bộ truyện chưa được phê duyệt hoặc đã kết thúc, tạm ngưng, đã hủy. Không thể chỉnh sửa hoặc thao tác trên các chương.";
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
            $chapters = $this->chapterModel->findByMangakaIdWithStats($_SESSION['user_id']);
            require_once __DIR__ . '/../views/mangaka/chapter_list.php';
            exit;
        }

        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
        exit;
    }

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
            $status = 'drafting'; // Mặc định luôn là Bản nháp khi tạo mới

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

            $formattedDueDate = null;
            if (!empty($_POST['due_date'])) {
                $dueTimestamp = strtotime($_POST['due_date']);
                if ($dueTimestamp === false) {
                    $_SESSION['error'] = 'Hạn chót (Due Date) không đúng định dạng.';
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                    exit;
                }
                if ($dueTimestamp < time()) {
                    $_SESSION['error'] = 'Hạn chót của chương truyện không thể ở quá khứ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=create&series_id={$seriesId}");
                    exit;
                }
                $formattedDueDate = date('Y-m-d H:i:s', $dueTimestamp);
            }

            $isFinal = isset($_POST['is_final']) ? 1 : 0;
            if ($isFinal) {
                // Kiểm tra xem bộ truyện đã có chapter cuối nào khác chưa
                if ($this->chapterModel->hasFinalChapter($seriesId)) {
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

    public function edit($id) {
        $chapter = $this->chapterModel->findById($id);
        if (!$chapter) {
            $_SESSION['error'] = "Không tìm thấy chapter.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $series = $this->checkSeriesOwnership($chapter['series_id']);

        // Khóa truy cập trang sửa nếu chương đang bị khóa
        if ($this->isChapterLocked($chapter)) {
            $_SESSION['error'] = "Chương truyện đang trong quá trình duyệt, đã phê duyệt hoặc đã xuất bản, không thể chỉnh sửa.";
            header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id=" . $chapter['series_id']);
            exit;
        }

        require_once __DIR__ . '/../views/mangaka/chapter_edit.php';
    }

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

            // Khóa cập nhật nếu chương đang bị khóa
            if ($this->isChapterLocked($chapter)) {
                $_SESSION['error'] = "Chương truyện đang trong quá trình duyệt, đã phê duyệt hoặc đã xuất bản, không thể chỉnh sửa.";
                header("Location: " . BASE_PATH . "/index.php?controller=series&action=show&id={$seriesId}");
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
                
                // Chặn hạn chót ở quá khứ nếu Mangaka thực sự thay đổi giá trị của nó
                if ($dueTimestamp < time() && $formattedDueDate !== $chapter['due_date']) {
                    $_SESSION['error'] = 'Hạn chót của chương truyện không thể ở quá khứ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                    exit;
                }
            }

            $isFinal = isset($_POST['is_final']) ? 1 : 0;
            if ($isFinal) {
                // Kiểm tra xem bộ truyện đã có chapter cuối nào khác chưa (ngoại trừ chính chapter này)
                if ($this->chapterModel->hasFinalChapter($seriesId, $id)) {
                    $_SESSION['error'] = "Bộ truyện này đã có một chapter khác được đánh dấu là chương cuối rồi!";
                    header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=edit&id={$id}");
                    exit;
                }
            }

            $data = [
                'chapter_number' => $chapterNumber,
                'title' => $title,
                'due_date' => $formattedDueDate,
                'is_final' => $isFinal
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
        
        // Lấy danh sách các trang của chapter này, kèm theo số lượng ghi chú (annotations) của Editor
        $pages = $this->pageModel->findByChapterIdWithAnnotationCount($id);

        // Lọc bỏ các trang bản nháp (drafting) nếu người xem là editor hoặc board
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor' || $role === 'board') {
            $pages = array_filter($pages, function($p) {
                return $p['status'] !== 'drafting';
            });
        }

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

            // Khóa xóa nếu chương đang bị khóa
            if ($this->isChapterLocked($chapter)) {
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

    /**
     * Thực hiện xuất bản Chapter (Dành cho Editorial Board)
     * 
     * @param int $id ID của Chapter cần xuất bản
     */
    public function publish($id) {
        requireRole('board');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapter = $this->chapterModel->findById($id);
            if (!$chapter) {
                $_SESSION['error'] = "Không tìm thấy chapter.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                exit;
            }

            if ($chapter['status'] !== 'approved') {
                $_SESSION['error'] = "Chương truyện chưa được phê duyệt, không thể xuất bản.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                exit;
            }

            try {
                $this->chapterModel->update($id, ['status' => 'published']);
                $this->pageModel->updateStatusByChapterId($id, 'published');
                
                // Gửi thông báo đến Mangaka khi chapter của họ được xuất bản
                $series = $this->seriesModel->findById($chapter['series_id']);
                $mangakaId = $series ? $series['mangaka_id'] : null;
                if ($mangakaId) {
                    require_once __DIR__ . '/../models/Notification.php';
                    $notificationModel = new \Notification();
                    $notificationModel->createNotification(
                        $mangakaId,
                        'chapter_published',
                        "Chương {$chapter['chapter_number']}: '{$chapter['title']}' thuộc bộ truyện '{$series['title']}' của bạn đã được xuất bản chính thức!",
                        $id
                    );
                }

                $_SESSION['success'] = "Xuất bản chương {$chapter['chapter_number']} thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi xuất bản chapter: " . $e->getMessage();
            }

            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
            exit;
        }
    }
}
