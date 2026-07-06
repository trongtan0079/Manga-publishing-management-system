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
        
        if ($role === 'board' || $role === 'admin') {
            $sql = "SELECT * FROM series WHERE publish_type != 'draft' ORDER BY series_id DESC";
            $stmt = $this->seriesModel->getConnection()->prepare($sql);
            $stmt->execute();
            $seriesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($role === 'editor') {
            // Editor chỉ xem các bộ truyện được gán phụ trách và đã được phê duyệt (status !== 'planning')
            $sql = "SELECT * FROM series WHERE editor_id = :editor_id AND status != 'planning' ORDER BY series_id DESC";
            $stmt = $this->seriesModel->getConnection()->prepare($sql);
            $stmt->execute([':editor_id' => $currentUserId]);
            $seriesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($role === 'mangaka') {
            $seriesList = $this->seriesModel->findByMangakaId($currentUserId);
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
        
        // Kiểm tra kích thước file (tối đa 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $_SESSION['error'] = "File ảnh bìa vượt quá dung lượng cho phép (10MB).";
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
     * Xử lý file đề xuất/bản thảo sơ bộ upload (PDF, ZIP, DOCX, DOC, RAR, PPTX)
     * @return string|null Đường dẫn file nếu thành công, null nếu có lỗi
     */
    private function handleProposalUpload() {
        if (!isset($_FILES['proposal_file']) || $_FILES['proposal_file']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['proposal_file'];
        
        // Kiểm tra kích thước file (tối đa 20MB)
        if ($file['size'] > 20 * 1024 * 1024) {
            $_SESSION['error'] = "File đề xuất vượt quá dung lượng cho phép (20MB).";
            return null;
        }

        // Kiểm tra định dạng
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        $allowedExtensions = ['pdf', 'zip', 'docx', 'doc', 'rar', 'pptx'];
        if (!in_array($extension, $allowedExtensions)) {
            $_SESSION['error'] = "Định dạng file đề xuất không được hỗ trợ. Chỉ cho phép: pdf, zip, docx, doc, rar, pptx";
            return null;
        }

        // Tạo tên file ngẫu nhiên để tránh trùng lặp
        $newFileName = uniqid('proposal_') . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/proposals/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $newFileName;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Trả về đường dẫn tương đối để lưu vào DB
            return '/uploads/proposals/' . $newFileName;
        }

        $_SESSION['error'] = "Có lỗi xảy ra khi lưu file bản thảo đề xuất.";
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

            $proposalFile = null;
            if (isset($_FILES['proposal_file']) && $_FILES['proposal_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedProposal = $this->handleProposalUpload();
                if ($uploadedProposal) {
                    $proposalFile = $uploadedProposal;
                } else {
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=create');
                    exit;
                }
            }

            $data = [
                'mangaka_id'  => $_SESSION['user_id'],
                'title'       => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status'      => 'planning',
                'publish_type'=> 'draft', // Mặc định tạo mới ở trạng thái bản nháp
                'cover_image' => $coverImage,
                'proposal_file' => $proposalFile
            ];

            try {
                $this->seriesModel->insert($data);
                $_SESSION['success'] = "Tạo bộ truyện '{$title}' thành công! Trạng thái hiện tại là Bản nháp.";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi tạo bộ truyện: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }
    }

    /**
     * Nộp đề xuất bộ truyện lên Ban Biên Tập (Từ Nháp sang Chờ duyệt)
     */
    public function submit($id) {
        requireRole('mangaka');
        $id = (int)$id;
        $series = $this->seriesModel->findById($id);
        if (!$series) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $this->checkOwnership($series, $id);

        if (empty($series['proposal_file'])) {
            $_SESSION['error'] = "Vui lòng đính kèm file bản thảo đề xuất trước khi nộp đề xuất bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $id);
            exit;
        }

        if ($series['status'] !== 'planning' || $series['publish_type'] !== 'draft') {
            $_SESSION['error'] = "Chỉ có thể nộp đề xuất khi bộ truyện ở trạng thái Nháp.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $id);
            exit;
        }

        try {
            $this->seriesModel->update($id, [
                'publish_type' => 'submitted'
            ]);
            
            // Tìm toàn bộ tài khoản có role là 'board' đang hoạt động để gửi thông báo
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new \Notification();
            require_once __DIR__ . '/../models/User.php';
            $userModel = new \User();
            $boardMembers = $userModel->findByRoleName('board');
            if (!empty($boardMembers)) {
                foreach ($boardMembers as $member) {
                    $notificationModel->createNotification(
                        $member['user_id'],
                        'series_submitted',
                        "Mangaka " . $_SESSION['full_name'] . " vừa nộp đề xuất bộ truyện mới: '" . $series['title'] . "'."
                    );
                }
            }

            $_SESSION['success'] = "Đề xuất bộ truyện '{$series['title']}' đã được gửi đến Ban Biên tập thành công!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Lỗi khi gửi đề xuất: " . $e->getMessage();
        }

        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $id);
        exit;
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
        
        // Nếu là bản nháp (draft), chỉ tác giả (Mangaka) sở hữu mới có quyền truy cập
        if (($series['publish_type'] ?? '') === 'draft' && $_SESSION['user_id'] != $series['mangaka_id']) {
            $_SESSION['error'] = "Bộ truyện này hiện đang là Bản nháp và chưa được nộp.";
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }

        // Chặn chỉnh sửa bộ truyện nếu đã tạm ngưng, đã hủy hoặc đã hoàn thành
        $action = $_GET['action'] ?? '';
        if (in_array($action, ['edit', 'update']) && in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
            $_SESSION['error'] = "Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể chỉnh sửa thông tin.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $id);
            exit;
        }

        // Admin, Board có quyền xem thông tin chi tiết các bộ truyện đã nộp hoặc đang hoạt động
        if ($role === 'admin' || $role === 'board') {
            return;
        }

        // Editor chỉ được xem nếu được gán phụ trách và bộ truyện đã được duyệt (status !== 'planning')
        if ($role === 'editor') {
            if ($series['editor_id'] == $_SESSION['user_id'] && $series['status'] !== 'planning') {
                return;
            }
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không được phân công quản lý bộ truyện này.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
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

            $proposalFile = $series['proposal_file'] ?? null;
            if (isset($_FILES['proposal_file']) && $_FILES['proposal_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedProposal = $this->handleProposalUpload();
                if ($uploadedProposal) {
                    // Xóa file cũ nếu có
                    if (!empty($series['proposal_file'])) {
                        $oldPropPath = __DIR__ . '/../' . ltrim($series['proposal_file'], '/');
                        if (file_exists($oldPropPath)) {
                            @unlink($oldPropPath);
                        }
                    }
                    $proposalFile = $uploadedProposal;
                } else {
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=edit&id=' . $id);
                    exit;
                }
            }

            $data = [
                'title'       => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status'      => $series['status'], // Trạng thái giữ nguyên, thuộc quyền Board thay đổi
                'publish_type'=> $series['publish_type'] ?? 'weekly', // Lịch xuất bản giữ nguyên
                'cover_image' => $coverImage,
                'proposal_file' => $proposalFile
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

            // Chặn xóa bộ truyện nếu trạng thái không phải planning
            if ($series['status'] !== 'planning') {
                $_SESSION['error'] = "Không thể xóa bộ truyện đã được duyệt hoặc xuất bản! Chỉ có thể xóa bộ truyện ở trạng thái Kế hoạch (Planning).";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

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

                // Xóa file đề xuất của bộ truyện nếu có
                if (!empty($series['proposal_file'])) {
                    $proposalPath = __DIR__ . '/../' . ltrim($series['proposal_file'], '/');
                    if (file_exists($proposalPath)) {
                        @unlink($proposalPath);
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
        
        // Lấy danh sách các editor đang active để gán chuyên trách
        require_once __DIR__ . '/../models/User.php';
        $userModel = new \User();
        $editors = $userModel->findByRoleName('editor');
        
        // Lấy danh sách truyện đang chờ duyệt (planning) và đang xuất bản (ongoing)
        $sql = "SELECT s.*, u.full_name as mangaka_name, ed.full_name as editor_name,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id) as total_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND status IN ('approved', 'published')) as finished_chapters,
                (SELECT COUNT(*) FROM chapters WHERE series_id = s.series_id AND is_final = 1 AND status IN ('approved', 'published')) as has_final_approved
                FROM series s 
                JOIN users u ON s.mangaka_id = u.user_id 
                LEFT JOIN users ed ON s.editor_id = ed.user_id
                WHERE s.status IN ('planning', 'ongoing', 'suspended') AND s.publish_type != 'draft'
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
            if (in_array($status, $this->allowedStatuses) && in_array($publishType, ['weekly', 'monthly'])) {
                
                // Ràng buộc: Không cho phép hoàn thành series nếu vẫn còn các chapter chưa hoàn thành hoặc chưa có chương cuối được duyệt
                if ($status === 'completed') {
                    $sql = "SELECT COUNT(*) as unfinished_chapters 
                            FROM chapters 
                            WHERE series_id = :series_id AND status IN ('drafting', 'drawing', 'reviewing')";
                    $stmt = $this->seriesModel->getConnection()->prepare($sql);
                    $stmt->execute(['series_id' => $id]);
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($res && $res['unfinished_chapters'] > 0) {
                        $_SESSION['error'] = "Không thể hoàn thành bộ truyện khi vẫn còn các chương truyện đang vẽ hoặc chờ duyệt.";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }

                    // Kiểm tra xem đã có chương cuối nào được duyệt/xuất bản chưa
                    $sqlFinal = "SELECT COUNT(*) as has_final 
                                 FROM chapters 
                                 WHERE series_id = :series_id AND is_final = 1 AND status IN ('approved', 'published')";
                    $stmtFinal = $this->seriesModel->getConnection()->prepare($sqlFinal);
                    $stmtFinal->execute(['series_id' => $id]);
                    $resFinal = $stmtFinal->fetch(PDO::FETCH_ASSOC);
                    if (!$resFinal || $resFinal['has_final'] == 0) {
                        $_SESSION['error'] = "Không thể hoàn thành bộ truyện khi chưa có chương cuối (End Chapter) nào được phê duyệt.";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }
                }

                $editorId = isset($_POST['editor_id']) && $_POST['editor_id'] !== '' ? intval($_POST['editor_id']) : null;

                try {
                    $this->seriesModel->update($id, [
                        'status' => $status,
                        'publish_type' => $publishType,
                        'editor_id' => $editorId
                    ]);
                    
                    // Gửi thông báo đến mangaka
                    require_once __DIR__ . '/../models/Notification.php';
                    $notificationModel = new Notification();
                    $mangakaId = $series['mangaka_id'];
                    $publishTypeViet = $publishType === 'weekly' ? 'Hàng tuần' : 'Hàng tháng';
                    
                    // Kiểm tra xem đây có phải là phê duyệt đề xuất từ nháp/chờ duyệt không
                    if ($series['status'] === 'planning' && ($series['publish_type'] ?? '') === 'submitted') {
                        if ($status === 'ongoing') {
                            $msg = "Đề xuất bộ truyện '{$series['title']}' của bạn đã được Hội đồng Biên tập PHÊ DUYỆT thành công! Truyện chính thức bắt đầu giai đoạn Đang triển khai với lịch xuất bản: {$publishTypeViet}.";
                        } elseif ($status === 'canceled') {
                            $msg = "Đề xuất bộ truyện '{$series['title']}' của bạn đã bị Hội đồng Biên tập TỪ CHỐI phê duyệt.";
                        } else {
                            $msg = "Đề xuất bộ truyện '{$series['title']}' của bạn đã được cập nhật trạng thái quyết định.";
                        }
                    } else {
                        // Cập nhật trạng thái của một bộ truyện đang chạy
                        $statusViet = 'Kế hoạch';
                        switch ($status) {
                            case 'ongoing': $statusViet = 'Đang triển khai'; break;
                            case 'completed': $statusViet = 'Hoàn thành'; break;
                            case 'canceled': $statusViet = 'Đã hủy'; break;
                            case 'suspended': $statusViet = 'Tạm ngưng'; break;
                        }
                        $msg = "Bộ truyện '{$series['title']}' của bạn đã được cập nhật trạng thái thành '{$statusViet}'";
                        if ($status === 'ongoing') {
                            $msg .= " với lịch xuất bản: {$publishTypeViet}";
                        }
                        $msg .= ".";
                    }
                    
                    $notificationModel->createNotification($mangakaId, 'series_warning', $msg);

                    // Gửi thông báo đến editor phụ trách nếu mới gán
                    if ($editorId && $editorId != $series['editor_id']) {
                        require_once __DIR__ . '/../models/User.php';
                        $userModel = new \User();
                        $mangaka = $userModel->findById($series['mangaka_id']);
                        $mangakaName = $mangaka['full_name'] ?? 'Tác giả';
                        
                        $notificationModel->createNotification(
                            $editorId,
                            'task_assigned',
                            "Bạn đã được phân công phụ trách kiểm duyệt bộ truyện mới: '{$series['title']}' của tác giả {$mangakaName} (Lịch xuất bản: {$publishTypeViet})."
                        );
                    }

                    $_SESSION['success'] = "Cập nhật trạng thái và lịch xuất bản bộ truyện thành công.";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Lỗi khi cập nhật: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Trạng thái hoặc lịch xuất bản không hợp lệ.";
            }

            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
            exit;
        }
    }
}
