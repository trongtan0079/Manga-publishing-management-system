<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Series.php';


class SubmissionController extends BaseController
{
    private $submissionModel;
    private $taskModel;
    private $chapterModel;
    private $seriesModel;

    public function __construct() {
        parent::__construct();
        // Yêu cầu người dùng đăng nhập trước khi thao tác
        \requireLogin();
        
        $this->submissionModel = new Submission();
        $this->taskModel = new Task();
        $this->chapterModel = new Chapter();
        $this->seriesModel = new Series();
    }

    /**
     * Hiển thị danh sách Submission
     */
    public function index() {
        $role = $_SESSION['role_name'] ?? '';
        $userId = $_SESSION['user_id'];

        if ($role === 'editor') {
            // Editor chỉ xem các bản thảo của các bộ truyện được phân công gán phụ trách
            $submissions = $this->submissionModel->findAllChapterSubmissionsByEditorId($userId);

            // Hỗ trợ lọc trạng thái theo query parameter
            $status = $_GET['status'] ?? null;
            if ($status) {
                if (in_array($status, ['pending', 'approved', 'rejected'])) {
                    $submissions = array_filter($submissions, function($s) use ($status) {
                        return $s['status'] === $status;
                    });
                } elseif ($status === 'reviewed') {
                    $submissions = array_filter($submissions, function($s) {
                        return in_array($s['status'], ['approved', 'rejected']);
                    });
                }
            }
        } elseif ($role === 'mangaka' || $role === 'assistant') {
            // Assistant và Mangaka xem lịch sử nộp bài của chính mình
            $submissions = $this->submissionModel->findByUserId($userId);
        } else {
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền xem danh sách bản thảo.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }

        // Nạp view danh sách
        require_once __DIR__ . '/../views/editor/submission_list.php';
    }

    /**
     * Hiển thị form upload
     */
    /**
     * Hiển thị form upload
     */
    public function create() {
        $role = $_SESSION['role_name'] ?? '';
        $userId = $_SESSION['user_id'];

        if ($role === 'assistant') {
            // Chỉ hiển thị task được giao cho assistant và chưa hoàn thành
            $tasks = $this->taskModel->findActiveByAssistantId($userId);
            require_once __DIR__ . '/../views/assistant/upload_submission.php';
        } elseif ($role === 'mangaka') {
            // Chỉ hiển thị chapter thuộc series của mangaka và chưa được duyệt/xuất bản, và chưa ở trạng thái reviewing
            $allChapters = $this->chapterModel->findByMangakaId($userId);
            $chapters = array_filter($allChapters, function($c) {
                return !in_array($c['status'], ['reviewing', 'approved', 'published']);
            });
            require_once __DIR__ . '/../views/mangaka/submission_create.php';
        } else {
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền nộp bản thảo mới.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }
    }

    /**
     * Lưu Submission mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        $userId = $_SESSION['user_id'];

        $taskId = null;
        $chapterId = null;

        // 1. Phân quyền và validate thực thể liên quan
        if ($role === 'assistant') {
            $taskId = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
            if ($taskId <= 0) {
                $_SESSION['error'] = 'Vui lòng chọn Task cần nộp.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }

            // Kiểm tra Task có thuộc về Assistant hiện tại không
            $task = $this->taskModel->findById($taskId);
            if (!$task || $task['assistant_id'] != $userId) {
                $_SESSION['error'] = 'Task không hợp lệ hoặc không thuộc quyền sở hữu của bạn.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
            if ($task['status'] === 'completed') {
                $_SESSION['error'] = 'Công việc này đã hoàn thành, không thể nộp thêm bản thảo.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }

            // Kiểm tra xem chapter và series có bị khóa hoặc tạm ngưng không
            require_once __DIR__ . '/../models/Page.php';
            $pageModel = new \Page();
            $page = $pageModel->findById($task['page_id']);
            if ($page) {
                require_once __DIR__ . '/../models/Chapter.php';
                $chapterModel = new \Chapter();
                $chapter = $chapterModel->findById($page['chapter_id']);
                if ($chapter) {
                    require_once __DIR__ . '/../models/Series.php';
                    $seriesModel = new \Series();
                    $series = $seriesModel->findById($chapter['series_id']);
                    if ($series && in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
                        $_SESSION['error'] = 'Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể nộp bản vẽ cho công việc này.';
                        header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                        exit;
                    }
                    if (in_array($chapter['status'], ['reviewing', 'approved', 'published'])) {
                        $_SESSION['error'] = 'Chương truyện chứa công việc này đang chờ duyệt, đã được phê duyệt hoặc xuất bản, không thể nộp bản thảo.';
                        header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                        exit;
                    }
                }
            }
        } elseif ($role === 'mangaka') {
            $chapterId = isset($_POST['chapter_id']) ? intval($_POST['chapter_id']) : 0;
            if ($chapterId <= 0) {
                $_SESSION['error'] = 'Vui lòng chọn Chapter cần nộp.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }

            // Kiểm tra Chapter có thuộc series của Mangaka không
            $chapter = $this->chapterModel->findById($chapterId);
            if (!$chapter) {
                $_SESSION['error'] = 'Chương truyện không tồn tại.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }

            // Nạp model Series để kiểm tra tác giả
            require_once __DIR__ . '/../models/Series.php';
            $seriesModel = new \Series();
            $series = $seriesModel->findById($chapter['series_id']);
            if (!$series || $series['mangaka_id'] != $userId) {
                $_SESSION['error'] = 'Chương truyện không thuộc Series của bạn.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
            if (in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
                $_SESSION['error'] = 'Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể nộp bản thảo.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
            if (in_array($chapter['status'], ['reviewing', 'approved', 'published'])) {
                $_SESSION['error'] = 'Chương truyện này đang chờ duyệt, đã được phê duyệt hoặc xuất bản, không thể nộp thêm bản thảo.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }

            // Kiểm tra xem có task nào chưa hoàn thành thuộc chapter này không
            require_once __DIR__ . '/../models/Task.php';
            $taskModel = new \Task();
            $tasks = $taskModel->findTasksByChapterId($chapterId);
            $hasUncompleted = false;
            foreach ($tasks as $t) {
                if ($t['status'] !== 'completed') {
                    $hasUncompleted = true;
                    break;
                }
            }
            if ($hasUncompleted) {
                $_SESSION['error'] = 'Không thể nộp bản thảo chương truyện khi vẫn còn công việc (Task) chưa hoàn thành của các Trợ lý.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
        } else {
            http_response_code(403);
            $_SESSION['error'] = 'Vai trò này không được phép nộp bản thảo.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        // 2. Validate File Upload
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Vui lòng chọn file hợp lệ để tải lên.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
            exit;
        }

        $file = $_FILES['file'];
        $originalName = basename($file['name']);
        $fileSize = $file['size'];
        $tmpPath = $file['tmp_name'];

        // Kiểm tra kích thước (tối đa 20MB)
        $maxSizeBytes = 20 * 1024 * 1024;
        if ($fileSize > $maxSizeBytes) {
            $_SESSION['error'] = 'Kích thước file vượt quá giới hạn cho phép (20MB).';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
            exit;
        }

        // Kiểm tra phần mở rộng file (whitelist)
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'zip'];
        if (!in_array($ext, $allowedExtensions)) {
            $_SESSION['error'] = 'Định dạng file không hợp lệ. Chỉ cho phép: jpg, jpeg, png, pdf, zip.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
            exit;
        }

        // Kiểm tra MIME Type thực tế
        $allowedMimes = [
            'jpg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
            'png' => ['image/png', 'image/x-png'],
            'pdf' => ['application/pdf'],
            'zip' => ['application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip']
        ];

        $mimeType = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($tmpPath);
        } else {
            $mimeType = $file['type'];
        }

        $validMime = false;
        if (isset($allowedMimes[$ext]) && in_array($mimeType, $allowedMimes[$ext])) {
            $validMime = true;
        } else {
            // Cải thiện UX: Tự động đổi đuôi file nếu MIME type hợp lệ với một định dạng khác
            foreach ($allowedMimes as $correctExt => $mimes) {
                if (in_array($mimeType, $mimes)) {
                    $ext = $correctExt;
                    $validMime = true;
                    // Cập nhật lại tên file gốc với đuôi mới
                    $originalName = pathinfo($originalName, PATHINFO_FILENAME) . '.' . $ext;
                    break;
                }
            }
        }

        if (!$validMime) {
            $_SESSION['error'] = 'Nội dung file không hợp lệ (MIME type không được hỗ trợ).';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
            exit;
        }

        // --- BỔ SUNG KIỂM TRA ĐĂNG KÝ PHẦN MỞ RỘNG VÀ CHỮ KÝ FILE THẬT ---
        // 1. Kiểm tra ảnh bằng getimagesize()
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $imageInfo = @getimagesize($tmpPath);
            if ($imageInfo === false) {
                $_SESSION['error'] = 'File ảnh không hợp lệ hoặc bị giả mạo.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
        }

        // 2. Kiểm tra PDF bằng Signature (%PDF)
        if ($ext === 'pdf') {
            $handle = @fopen($tmpPath, 'rb');
            if ($handle) {
                $firstBytes = fread($handle, 4);
                fclose($handle);
                if ($firstBytes !== '%PDF') {
                    $_SESSION['error'] = 'File PDF không hợp lệ hoặc bị giả mạo.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Không thể đọc file PDF để kiểm tra chữ ký.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
        }

        // 3. Kiểm tra ZIP bằng Signature (PK\x03\x04)
        if ($ext === 'zip') {
            $handle = @fopen($tmpPath, 'rb');
            if ($handle) {
                $firstBytes = fread($handle, 4);
                fclose($handle);
                if ($firstBytes !== "PK\x03\x04") {
                    $_SESSION['error'] = 'File ZIP không hợp lệ hoặc bị giả mạo.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Không thể đọc file ZIP để kiểm tra chữ ký.';
                header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
                exit;
            }
        }

        // 3. Tiến hành lưu file
        $uploadDir = __DIR__ . '/../uploads/submissions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Tạo tên file an toàn: timestamp_tengoc
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $targetPath = $uploadDir . $safeName;
        $dbFileUrl = 'uploads/submissions/' . $safeName;

        if (!move_uploaded_file($tmpPath, $targetPath)) {
            $_SESSION['error'] = 'Lỗi hệ thống khi lưu trữ file tải lên.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=create');
            exit;
        }

        // 4. Lưu vào Database
        $submissionData = [
            'user_id' => $userId,
            'task_id' => $taskId ?: null,
            'chapter_id' => $chapterId ?: null,
            'file_url' => $dbFileUrl,
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => 'pending'
        ];

        $submissionId = $this->submissionModel->insert($submissionData);

        if ($submissionId) {
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new \Notification();

            if ($role === 'mangaka' && $chapterId > 0) {
                $this->chapterModel->update($chapterId, ['status' => 'reviewing']);
                
                require_once __DIR__ . '/../models/User.php';
                $userModel = new \User();
                
                $chapter = $this->chapterModel->findById($chapterId);
                $series = null;
                if ($chapter) {
                    $series = $this->seriesModel->findById($chapter['series_id']);
                }
                $seriesTitle = $series ? $series['title'] : 'bộ truyện';
                
                if ($series && !empty($series['editor_id'])) {
                    // Gửi DUY NHẤT cho Editor chuyên trách
                    $notificationModel->createNotification(
                        $series['editor_id'],
                        'chapter_submitted',
                        "Mangaka " . $_SESSION['full_name'] . " vừa nộp Chapter " . ($chapter['chapter_number'] ?? '') . " cho bộ truyện: '{$seriesTitle}'."
                    );
                } else {
                    // Bể duyệt chung fallback
                    $editors = $userModel->findByRoleName('editor');
                    foreach ($editors as $editor) {
                        $notificationModel->createNotification(
                            $editor['user_id'],
                            'chapter_submitted',
                            "Mangaka " . $_SESSION['full_name'] . " vừa nộp Chapter " . ($chapter['chapter_number'] ?? '') . " cho bộ truyện: '{$seriesTitle}' (Chưa gán Editor chuyên trách)."
                        );
                    }
                }
            } elseif ($role === 'assistant' && $taskId > 0) {
                $task = $this->taskModel->findById($taskId);
                if ($task) {
                    // Cập nhật trạng thái Task thành 'in_progress' khi Assistant nộp bài
                    $this->taskModel->update($taskId, ['status' => 'in_progress']);

                    require_once __DIR__ . '/../models/Page.php';
                    require_once __DIR__ . '/../models/Chapter.php';
                    require_once __DIR__ . '/../models/Series.php';
                    $pageModel = new \Page();
                    $chapterModel = new \Chapter();
                    $seriesModel = new \Series();
                    
                    $page = $pageModel->findById($task['page_id']);
                    $chapter = $page ? $chapterModel->findById($page['chapter_id']) : null;
                    $series = $chapter ? $seriesModel->findById($chapter['series_id']) : null;
                    $seriesTitle = $series ? $series['title'] : 'Không rõ';
                    $chapNum = $chapter ? $chapter['chapter_number'] : 'Không rõ';
                    $pageNum = $page ? $page['page_number'] : 'Không rõ';
                    
                    $assistantName = $_SESSION['full_name'] ?? $_SESSION['username'];

                    $notificationModel->createNotification(
                        $task['mangaka_id'],
                        'submission_submitted',
                        "Trợ lý {$assistantName} vừa nộp bản vẽ cho công việc: '{$task['title']}' thuộc bộ truyện '{$seriesTitle}' (Chương {$chapNum} - Trang {$pageNum})."
                    );
                }
            }

            $_SESSION['success'] = 'Nộp bản thảo thành công và đang chờ review.';
        } else {
            // Xóa file đã upload nếu chèn database thất bại
            if (file_exists($targetPath)) {
                @unlink($targetPath);
            }
            $_SESSION['error'] = 'Không thể lưu thông tin Submission vào Database.';
        }

        header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
        exit;
    }

    /**
     * Xem chi tiết Submission
     */
    public function show($id) {
        $id = intval($id);
        if ($id <= 0) {
            $_SESSION['error'] = 'ID bản thảo không hợp lệ.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        $userId = $_SESSION['user_id'];

        $submission = $this->submissionModel->findWithMetadataById($id);
        if (!$submission) {
            http_response_code(404);
            $_SESSION['error'] = 'Không tìm thấy bản thảo yêu cầu.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        // Kiểm tra quyền xem chi tiết bản thảo:
        // - Editor xem tất cả
        // - Assistant chỉ được xem của chính mình
        // - Mangaka được xem của chính mình hoặc các bản thảo thuộc series/task thuộc quyền của mình
        $hasAccess = false;
        if ($role === 'editor') {
            $hasAccess = true;
        } elseif ($role === 'assistant') {
            if ($submission['user_id'] == $userId) {
                $hasAccess = true;
            }
        } elseif ($role === 'mangaka') {
            if ($submission['user_id'] == $userId || $submission['mangaka_id'] == $userId) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền truy cập xem bản thảo này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        $reviewModel = new \Review();
        $reviews = $reviewModel->findBySubmissionId($id);

        // Nạp view chi tiết
        require_once __DIR__ . '/../views/editor/submission_detail.php';
    }

    /**
     * Xóa Submission chưa được review (chỉ ở trạng thái pending)
     */
    public function delete($id) {
        $id = intval($id);
        if ($id <= 0 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        $userId = $_SESSION['user_id'];

        $submission = $this->submissionModel->findById($id);
        if (!$submission) {
            $_SESSION['error'] = 'Không tìm thấy bản thảo cần xóa.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        // Kiểm tra quyền sở hữu: chỉ người nộp mới được xóa
        if ($submission['user_id'] != $userId) {
            $_SESSION['error'] = 'Bạn không có quyền xóa bản thảo này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        // Kiểm tra trạng thái: chỉ cho phép xóa nếu là 'pending'
        if ($submission['status'] !== 'pending') {
            $_SESSION['error'] = 'Không thể xóa bản thảo đã được đánh giá (reviewed/approved/rejected).';
            header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
            exit;
        }

        // Xóa file vật lý khỏi đĩa
        if (!empty($submission['file_url'])) {
            $filePath = __DIR__ . '/../' . $submission['file_url'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Xóa dòng trong Database
        if ($this->submissionModel->delete($id)) {
            $_SESSION['success'] = 'Xóa bản thảo thành công.';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra trong quá trình xóa dữ liệu.';
        }

        header('Location: ' . BASE_PATH . '/index.php?controller=submission&action=index');
        exit;
    }
}
