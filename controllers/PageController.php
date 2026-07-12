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
    
    // Giới hạn file ảnh 10MB
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;
    
    // Các định dạng ảnh được phép
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    
    // Trạng thái hợp lệ của page
    private $allowedStatuses = ['drafting', 'drawing', 'reviewing_draft', 'reviewing_final', 'approved', 'published'];

    public function __construct() {
        parent::__construct();
        \requireLogin();
        
        $action = $_GET['action'] ?? 'index';
        $allowedViewRoles = ['mangaka', 'editor', 'board', 'admin', 'assistant'];
        
        if (in_array($action, ['show', 'index'])) {
            if (!in_array($_SESSION['role_name'], $allowedViewRoles)) {
                http_response_code(403);
                echo "Access Denied: You do not have the required role to access this page.";
                exit;
            }
        } else {
            \requireRole('mangaka');
        }
        
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
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $series = $this->seriesModel->findById($chapter['series_id']);
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'assistant') {
            // Kiểm tra xem assistant có được giao task nào thuộc page này không
            $pageId = $_GET['id'] ?? null;
            if ($pageId) {
                // Chặn Assistant truy cập nếu trang truyện vẫn đang ở dạng Bản nháp (Drafting)
                $pageObj = $this->pageModel->findById($pageId);
                if ($pageObj && $pageObj['status'] === 'drafting') {
                    $_SESSION['error'] = "Truy cập bị từ chối! Trang truyện này hiện đang ở trạng thái Bản nháp.";
                    header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=assistant');
                    exit;
                }

                if ($this->taskModel->countByPageAndAssistant($pageId, $_SESSION['user_id']) > 0) {
                    return ['chapter' => $chapter, 'series' => $series];
                }
            }
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có công việc được giao trên trang truyện này.";
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=assistant');
            exit;
        }

        if (!$this->hasSeriesAccess($series)) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên chapter này.";
            if ($role === 'editor') {
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            } else {
                header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            }
            exit;
        }

        // Chặn sửa/xóa/nộp trang nếu bộ truyện không hoạt động chính thức (ongoing)
        $action = $_GET['action'] ?? '';
        if (in_array($action, ['create', 'store', 'edit', 'update', 'delete']) && $series['status'] !== 'ongoing') {
            $_SESSION['error'] = "Bộ truyện chưa được phê duyệt hoặc đã kết thúc, tạm ngưng, đã hủy. Không thể chỉnh sửa trang truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=chapter&action=show&id=' . $chapterId);
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
            $_SESSION['error'] = "File ảnh vượt quá dung lượng cho phép (10MB).";
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
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'mangaka') {
            $pages = $this->pageModel->findByMangakaId($_SESSION['user_id']);
            require_once __DIR__ . '/../views/mangaka/page_list.php';
            exit;
        }

        // Chuyển hướng về trang chi tiết series
        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
        exit;
    }

    /**
     * Hiển thị form tạo trang mới
     */
    public function create() {
        $chapterId = $_GET['chapter_id'] ?? null;
        if (!$chapterId) {
            $_SESSION['error'] = "Thiếu thông tin chapter.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $ownership = $this->checkChapterOwnership($chapterId);
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];

        // Khóa tạo trang nếu chapter đang bị khóa
        if ($this->isChapterLocked($chapter)) {
            $_SESSION['error'] = "Chương truyện đang chờ duyệt, đã phê duyệt hoặc đã xuất bản, không thể thêm trang vẽ mới.";
            header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
            exit;
        }
        
        // Lấy danh sách số trang đã tồn tại trong chapter để phục vụ validate thời gian thực
        $existingPageNumbers = $this->pageModel->getPageNumbersByChapterId($chapterId);
        
        require_once __DIR__ . '/../views/mangaka/page_create.php';
    }

    /**
     * Lưu trang mới vào DB
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapterId = $_POST['chapter_id'] ?? null;
            if (!$chapterId) {
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            // Kiểm tra quyền sở hữu
            $ownership = $this->checkChapterOwnership($chapterId);
            $chapter = $ownership['chapter'];

            // Khóa tạo trang nếu chapter đang bị khóa
            if ($this->isChapterLocked($chapter)) {
                $_SESSION['error'] = "Chương truyện đang chờ duyệt, đã phê duyệt hoặc đã xuất bản, không thể thêm trang vẽ mới.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
                exit;
            }

            $pageNumber = trim($_POST['page_number'] ?? '');
            $status = $_POST['status'] ?? 'drafting';

            // Validation: page_number bắt buộc và > 0
            if ($pageNumber === '' || !is_numeric($pageNumber) || $pageNumber <= 0) {
                $_SESSION['error'] = "Số trang (Page Number) bắt buộc và phải lớn hơn 0.";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            // Validation: không trùng page_number trong cùng chapter
            if ($this->pageModel->isPageNumberExists($chapterId, $pageNumber)) {
                $_SESSION['error'] = "Số trang {$pageNumber} đã tồn tại trong chapter này.";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            if (!in_array($status, ['drafting', 'drawing'])) {
                $_SESSION['error'] = "Trạng thái trang khởi tạo không hợp lệ! Chỉ cho phép Bản nháp hoặc Đang vẽ.";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=create&chapter_id={$chapterId}");
                exit;
            }

            // Xử lý upload ảnh
            $imageUrl = $this->handleImageUpload();
            if (!$imageUrl) {
                if (!isset($_SESSION['error'])) {
                    $_SESSION['error'] = "Vui lòng chọn ảnh cho trang truyện.";
                }
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=create&chapter_id={$chapterId}");
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
            
            header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
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
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        // Ủy quyền
        $ownership = $this->checkChapterOwnership($page['chapter_id']);
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];

        // Chặn Editor, Board, Assistant xem chi tiết trang nếu Chapter đang ở trạng thái Nháp (drafting)
        $role = $_SESSION['role_name'] ?? '';
        if ($role !== 'mangaka' && $role !== 'admin') {
            if ($chapter['status'] === 'drafting') {
                $_SESSION['error'] = "Truy cập bị từ chối! Chương truyện này hiện đang ở trạng thái Nháp.";
                if ($role === 'assistant') {
                    header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=assistant');
                } else {
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                }
                exit;
            }
        }

        $tasks = $this->taskModel->findByPageId($id);

        require_once __DIR__ . '/../models/PageRegion.php';
        $pageRegionModel = new PageRegion();
        $regions = $pageRegionModel->findByPageId($id);

        require_once __DIR__ . '/../models/EditorAnnotation.php';
        $editorAnnotationModel = new EditorAnnotation();
        $editorAnnotations = $editorAnnotationModel->findByPageId($id);

        require_once __DIR__ . '/../views/mangaka/page_detail.php';
    }

    /**
     * Hiển thị form sửa trang
     */
    public function edit($id) {
        $page = $this->pageModel->findById($id);
        if (!$page) {
            $_SESSION['error'] = "Không tìm thấy trang.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        // Ủy quyền
        $ownership = $this->checkChapterOwnership($page['chapter_id']);
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];

        // Khóa sửa trang nếu chapter đang bị khóa hoặc trang đã xuất bản
        if ($this->isChapterLocked($chapter) || $page['status'] === 'published') {
            $_SESSION['error'] = "Trang truyện hoặc chương truyện đang chờ duyệt, đã phê duyệt hoặc đã xuất bản, không thể chỉnh sửa.";
            header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$page['chapter_id']}");
            exit;
        }
        
        // Lấy danh sách số trang đã tồn tại trong chapter (ngoại trừ trang hiện tại) để phục vụ validate thời gian thực
        $existingPageNumbers = $this->pageModel->getOtherPageNumbers($page['chapter_id'], $id);
        
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
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            $chapterId = $page['chapter_id'];
            
            // Ủy quyền
            $ownership = $this->checkChapterOwnership($chapterId);
            $chapter = $ownership['chapter'];

            // Khóa sửa trang nếu chapter đang bị khóa hoặc trang đã xuất bản
            if ($this->isChapterLocked($chapter) || $page['status'] === 'published') {
                $_SESSION['error'] = "Trang truyện hoặc chương truyện đang chờ duyệt, đã phê duyệt hoặc đã xuất bản, không thể chỉnh sửa.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
                exit;
            }

            $pageNumber = trim($_POST['page_number'] ?? '');
            $status = $_POST['status'] ?? $page['status'];

            // Validation: page_number bắt buộc và > 0
            if ($pageNumber === '' || !is_numeric($pageNumber) || $pageNumber <= 0) {
                $_SESSION['error'] = "Số trang bắt buộc và phải lớn hơn 0.";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=edit&id={$id}");
                exit;
            }

            // Validation: không trùng page_number (ngoại trừ chính nó)
            if ($this->pageModel->isPageNumberExists($chapterId, $pageNumber, $id)) {
                $_SESSION['error'] = "Số trang {$pageNumber} đã tồn tại trong chapter này.";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=edit&id={$id}");
                exit;
            }

            $allowedUpdateStatuses = ['drafting', 'drawing', 'reviewing_draft', 'reviewing_final'];
            if ($page['status'] === 'approved' || $page['status'] === 'published') {
                $allowedUpdateStatuses[] = $page['status'];
            }
            if (!in_array($status, $allowedUpdateStatuses)) {
                $_SESSION['error'] = "Trạng thái trang cập nhật không hợp lệ!";
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=edit&id={$id}");
                exit;
            }

            $data = [
                'page_number' => $pageNumber,
                'status' => $status
            ];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload();
                if ($imageUrl) {
                    // Nếu đây là lần tải lên Genko đầu tiên sau khi duyệt Storyboard (được đánh dấu bằng 'no_genko')
                    if ($page['old_image_url'] === 'no_genko') {
                        $data['old_image_url'] = null; // Reset để bản vẽ Genko tiếp theo sẽ được lưu làm bản vẽ gốc Genko
                    } else {
                        // Chỉ cập nhật old_image_url nếu chưa có bản vẽ gốc trước đó (giữ lại bản gốc đầu tiên)
                        if (empty($page['old_image_url'])) {
                            $data['old_image_url'] = $page['image_url'];
                        } else {
                            // Nếu đã lưu bản gốc đầu tiên, xóa bản vẽ trung gian cũ vừa sửa để giải phóng dung lượng
                            $oldTempPath = __DIR__ . '/../' . ltrim($page['image_url'], '/');
                            if (!empty($page['image_url']) && file_exists($oldTempPath)) {
                                @unlink($oldTempPath);
                            }
                        }
                    }
                    $data['image_url'] = $imageUrl;
                } else {
                    // Xảy ra lỗi upload (dung lượng, định dạng)
                    header("Location: " . BASE_PATH . "/index.php?controller=page&action=edit&id={$id}");
                    exit;
                }
            }

            try {
                $oldStatus = $page['status'];
                $this->pageModel->update($id, $data);
                $_SESSION['success'] = "Cập nhật trang {$pageNumber} thành công!";
                
                // Nếu trang chuyển từ nháp sang đang vẽ (active)
                if ($oldStatus === 'drafting' && $status === 'drawing') {
                    // CHỈ gửi thông báo nếu Chapter đã được duyệt Storyboard (không còn ở dạng phác thảo drafting hoặc đang chờ duyệt kịch bản reviewing_draft)
                    if ($chapter['status'] !== 'drafting' && $chapter['status'] !== 'reviewing_draft') {
                        require_once __DIR__ . '/../models/Task.php';
                        $taskModel = new \Task();
                        $pageTasks = $taskModel->findByPageId($id);
                        
                        require_once __DIR__ . '/../models/Notification.php';
                        $notificationModel = new \Notification();
                        
                        foreach ($pageTasks as $t) {
                            $notificationModel->createNotification(
                                $t['assistant_id'],
                                'task_assigned',
                                "Bạn được giao công việc mới: '{$t['title']}' thuộc bộ truyện '{$series['title']}' (Chương {$chapter['chapter_number']} - Trang {$page['page_number']}).",
                                $t['task_id']
                            );
                        }
                    }
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }
            
            header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
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
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            $chapterId = $page['chapter_id'];
            
            // Ủy quyền
            $ownership = $this->checkChapterOwnership($chapterId);
            $chapter = $ownership['chapter'];

            // Khóa xóa trang nếu chapter đang bị khóa hoặc trang đã duyệt/xuất bản
            if ($this->isChapterLocked($chapter) || in_array($page['status'], ['approved', 'published'])) {
                $_SESSION['error'] = "Trang truyện hoặc chương truyện đang chờ duyệt, đã phê duyệt hoặc đã xuất bản, không thể xóa.";
                header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
                exit;
            }

            try {
                // Xóa file vật lý của trang trước
                $filePath = __DIR__ . '/../' . ltrim($page['image_url'], '/');
                if (!empty($page['image_url']) && file_exists($filePath)) {
                    @unlink($filePath);
                }

                // Xóa file vật lý của bản cũ (nếu có)
                if (!empty($page['old_image_url'])) {
                    $oldFilePath = __DIR__ . '/../' . ltrim($page['old_image_url'], '/');
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }

                // Xóa file các bản nộp của các task thuộc trang này
                require_once __DIR__ . '/../models/Submission.php';
                $submissionModel = new \Submission();
                $tasks = $this->taskModel->findByPageId($id);
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

                // Xóa record DB
                $this->pageModel->delete($id);
                $_SESSION['success'] = "Đã xóa trang thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi xóa trang: " . $e->getMessage();
            }
            
            header("Location: " . BASE_PATH . "/index.php?controller=chapter&action=show&id={$chapterId}");
            exit;
        }
    }


}
