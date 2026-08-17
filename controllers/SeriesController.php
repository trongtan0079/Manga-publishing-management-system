<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/SystemLog.php';


class SeriesController extends BaseController
{
    /** @var Series */
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
     * Hiển thị danh sách các bộ truyện kèm tìm kiếm, lọc trạng thái và phân trang
     */
    public function index() {
        $role = $_SESSION['role_name'] ?? '';
        $currentUserId = (int)$_SESSION['user_id'];
        
        if (!in_array($role, ['mangaka', 'editor', 'board', 'admin'])) {
            http_response_code(403);
            $_SESSION['error'] = 'Bạn không có quyền truy cập.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit = 10; // 10 bản ghi mỗi trang
        $offset = ($page - 1) * $limit;

        $result = $this->seriesModel->getPaginatedSeries($role, $currentUserId, $search, $status, $limit, $offset);
        $seriesList = $result['series'];
        $totalSeries = $result['total'];
        $totalPages = ceil($totalSeries / $limit);

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
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
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

            // Kiểm tra trùng lặp tiêu đề bộ truyện
            if ($this->seriesModel->isTitleExists($title)) {
                $_SESSION['error'] = "Tiêu đề bộ truyện '{$title}' đã tồn tại trong hệ thống. Vui lòng chọn tiêu đề khác!";
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
    /**
     * @param int|string $id
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
    /**
     * @param array $series
     * @param int|string $id
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

        // Chặn chỉnh sửa bộ truyện nếu đã bị khóa
        $action = $_GET['action'] ?? '';
        if (in_array($action, ['edit', 'update']) && $this->isSeriesLocked($series)) {
            $_SESSION['error'] = "Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể chỉnh sửa thông tin.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=show&id=' . $id);
            exit;
        }

        if (!$this->hasSeriesAccess($series)) {
            $_SESSION['error'] = "Truy cập bị từ chối! Bạn không có quyền thao tác trên bộ truyện này.";
            if ($role === 'editor') {
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            } else {
                header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            }
            exit;
        }
    }

    /**
     * Hiển thị form chỉnh sửa bộ truyện
     */
    /**
     * @param int|string $id
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
    /**
     * @param int|string $id
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

            // Kiểm tra trùng lặp tiêu đề bộ truyện (loại trừ chính bộ truyện đang sửa)
            if ($this->seriesModel->isTitleExists($title, $id)) {
                $_SESSION['error'] = "Tiêu đề bộ truyện '{$title}' đã được sử dụng bởi bộ truyện khác. Vui lòng chọn tiêu đề khác!";
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
    /**
     * @param int|string $id
     */
    public function show($id) {
        $series = $this->seriesModel->findById($id);
        $this->checkOwnership($series, $id);
        
        $chapterModel = new Chapter();
        $chapters = $chapterModel->findBySeriesIdWithStats($id);
        
        require_once __DIR__ . '/../views/mangaka/series_detail.php';
    }

    /**
     * Xóa bộ truyện
     */
    /**
     * @param int|string $id
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
        $seriesList = $this->seriesModel->getSeriesForPublishing();

        // Nạp thống kê bỏ phiếu thực tế từ bảng board_votes
        require_once __DIR__ . '/../models/BoardVote.php';
        $boardVoteModel = new \BoardVote();
        $currentUserId = $_SESSION['user_id'];
        foreach ($seriesList as &$series) {
            $series['approval_stats'] = $boardVoteModel->getApprovalStats($series['series_id']);
            $series['my_vote'] = $boardVoteModel->getMemberVote($series['series_id'], $currentUserId);
        }
        unset($series);
        
        // Lấy danh sách các chapter đã duyệt nhưng chưa xuất bản
        require_once __DIR__ . '/../models/Chapter.php';
        $chapterModel = new \Chapter();
        $approvedChapters = $chapterModel->findApprovedChapters();
        $publishedChapters = $chapterModel->findPublishedChapters();
        
        require_once __DIR__ . '/../views/board/publish_series.php';
    }

    /**
     * Cập nhật trạng thái Series (Dành cho Editorial Board duyệt xuất bản)
     */
    /**
     * @param int|string $id
     */
    public function updateStatus($id) {
        requireRole('board');
        
        // Ràng buộc: Chỉ Trưởng ban Hội đồng mới có quyền chốt duyệt / đổi trạng thái bộ truyện
        if (empty($_SESSION['is_head_board']) || $_SESSION['is_head_board'] != 1) {
            $_SESSION['error'] = "Quyền hạn không hợp lệ. Chỉ Trưởng ban Hội đồng Biên tập mới có quyền chốt quyết định phê duyệt bộ truyện.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
            exit;
        }

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
                    $chapterModel = new \Chapter();
                    if ($chapterModel->countUnfinishedChapters($id) > 0) {
                        $_SESSION['error'] = "Không thể hoàn thành bộ truyện khi vẫn còn các chương truyện đang vẽ hoặc chờ duyệt.";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }

                    // Kiểm tra xem đã có chương cuối nào được duyệt/xuất bản chưa
                    if (!$chapterModel->hasFinalApprovedChapter($id)) {
                        $_SESSION['error'] = "Không thể hoàn thành bộ truyện khi chưa có chương cuối (End Chapter) nào được phê duyệt.";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }
                }

                // Ràng buộc: Đối với đề xuất mới (status = planning), chỉ cho phép chốt quyết định (ongoing hoặc canceled) khi tất cả thành viên Hội đồng đã bỏ phiếu xong.
                if ($series['status'] === 'planning' && in_array($status, ['ongoing', 'canceled'])) {
                    require_once __DIR__ . '/../models/BoardVote.php';
                    $boardVoteModel = new \BoardVote();
                    $stats = $boardVoteModel->getApprovalStats($id);
                    $totalVotes = $stats['approve_count'] + $stats['reject_count'];
                    
                    if ($totalVotes < $stats['total_members']) {
                        $_SESSION['error'] = "Không thể chốt quyết định khi chưa đầy đủ phiếu bầu từ các thành viên Hội đồng (Hiện tại: {$totalVotes}/{$stats['total_members']} phiếu).";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }
                }

                $editorId = isset($_POST['editor_id']) && $_POST['editor_id'] !== '' ? intval($_POST['editor_id']) : null;

                // Lỗ hổng 1: Bắt buộc chọn Biên tập viên khi phê duyệt bộ truyện đang hoạt động
                if ($status === 'ongoing' && $editorId === null) {
                    $_SESSION['error'] = "Vui lòng chọn Biên tập viên chuyên trách (Tantou Editor) khi phê duyệt phát hành bộ truyện.";
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                    exit;
                }

                // Ràng buộc: Chỉ cho phép phê duyệt bộ truyện (status = ongoing) khi tỉ lệ tán thành >= 50%
                if ($status === 'ongoing') {
                    require_once __DIR__ . '/../models/BoardVote.php';
                    $boardVoteModel = new \BoardVote();
                    $stats = $boardVoteModel->getApprovalStats($id);
                    if ($stats['percentage'] < 50) {
                        $_SESSION['error'] = "Không thể phê duyệt bộ truyện khi tỉ lệ tán thành của Hội đồng chưa đạt tối thiểu 50% (Hiện tại: " . $stats['percentage'] . "%).";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }
                }

                if ($editorId !== null) {
                    require_once __DIR__ . '/../models/User.php';
                    $userModel = new \User();
                    $editorUser = $userModel->getUserByIdWithRole($editorId);
                    if (!$editorUser || ($editorUser['role_name'] ?? '') !== 'editor' || $editorUser['status'] !== 'active') {
                        $_SESSION['error'] = "Biên tập viên chuyên trách không hợp lệ hoặc không hoạt động.";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }
                }

                // Lỗ hổng 6: Chặn duyệt bộ truyện vẫn còn ở dạng Bản nháp hoặc thiếu file tài liệu đề xuất
                if ($status === 'ongoing' && (($series['publish_type'] ?? '') === 'draft' || empty($series['proposal_file']))) {
                    $_SESSION['error'] = "Không thể phê duyệt bộ truyện vẫn ở dạng Bản nháp (Draft) hoặc chưa đính kèm file kịch bản đề xuất.";
                    header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                    exit;
                }

                // Lỗ hổng 11: Chặn hạ cấp bộ truyện về trạng thái Kế hoạch (Planning) nếu đã tồn tại chapter
                if ($status === 'planning') {
                    $sqlChapCount = "SELECT COUNT(*) FROM chapters WHERE series_id = :series_id";
                    $stmtChapCount = $this->seriesModel->getConnection()->prepare($sqlChapCount);
                    $stmtChapCount->execute(['series_id' => $id]);
                    if ($stmtChapCount->fetchColumn() > 0) {
                        $_SESSION['error'] = "Không thể hạ cấp bộ truyện về trạng thái Kế hoạch (Planning) khi đã có các chương truyện được tạo.";
                        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                        exit;
                    }
                }

                try {
                    $this->seriesModel->update($id, [
                        'status' => $status,
                        'publish_type' => $publishType,
                        'editor_id' => $editorId
                    ]);
                    
                    // Lỗ hổng 4: Ghi nhật ký hoạt động cho Board
                    $editorName = 'Chưa gán';
                    if ($editorId) {
                        require_once __DIR__ . '/../models/User.php';
                        $userModel = new \User();
                        $edObj = $userModel->findById($editorId);
                        if ($edObj) {
                            $editorName = $edObj['full_name'];
                        }
                    }
                    $logDetails = "Thay đổi trạng thái bộ truyện '{$series['title']}' (ID: {$id}). Cũ: '{$series['status']}' -> Mới: '{$status}', Lịch: '{$publishType}', Biên tập viên: '{$editorName}'";
                    \SystemLog::logAction($_SESSION['user_id'], 'Cập nhật trạng thái Series', $logDetails);
                    
                    // Gửi thông báo đến mangaka
                    require_once __DIR__ . '/../models/Notification.php';
                    $notificationModel = new Notification();
                    $mangakaId = $series['mangaka_id'];
                    $publishTypeViet = $publishType === 'weekly' ? 'Hàng tuần' : 'Hàng tháng';
                    
                    // Lấy số liệu bỏ phiếu của hội đồng để thông báo chi tiết
                    require_once __DIR__ . '/../models/BoardVote.php';
                    $boardVoteModel = new \BoardVote();
                    $stats = $boardVoteModel->getApprovalStats($id);
                    $percentage = $stats['percentage'];
                    $approveCount = $stats['approve_count'];
                    $totalMembers = $stats['total_members'];
                    
                    // Kiểm tra xem đây có phải là phê duyệt đề xuất từ nháp/chờ duyệt không
                    if ($series['status'] === 'planning' && ($series['publish_type'] ?? '') === 'submitted') {
                        if ($status === 'ongoing') {
                            $msg = "Chúc mừng! Đề xuất bộ truyện '{$series['title']}' của bạn đã chính thức được Hội đồng phê duyệt thông qua với tỉ lệ tán thành đạt {$percentage}% ({$approveCount}/{$totalMembers} phiếu). Truyện chính thức bắt đầu giai đoạn Đang triển khai với lịch xuất bản: {$publishTypeViet}.";
                        } elseif ($status === 'canceled') {
                            $msg = "Rất tiếc, đề xuất bộ truyện '{$series['title']}' của bạn đã bị từ chối phê duyệt do không đạt đủ số phiếu đồng thuận cần thiết từ Hội đồng Biên tập (Tỉ lệ tán thành đạt: {$percentage}%).";
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
                    
                    $notificationModel->createNotification($mangakaId, 'series_warning', $msg, $id);

                    // Gửi thông báo đến editor phụ trách nếu mới gán
                    if ($editorId && $editorId != $series['editor_id']) {
                        require_once __DIR__ . '/../models/User.php';
                        $userModel = new \User();
                        $mangaka = $userModel->findById($series['mangaka_id']);
                        $mangakaName = $mangaka['full_name'] ?? 'Tác giả';
                        
                        $notificationModel->createNotification(
                            $editorId,
                            'task_assigned',
                            "Bạn đã được phân công phụ trách kiểm duyệt bộ truyện mới: '{$series['title']}' của tác giả {$mangakaName} (Lịch xuất bản: {$publishTypeViet}).",
                            $id
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

    /**
     * Action: Thành viên Hội đồng Biên tập bỏ phiếu cho bộ truyện đang chờ duyệt
     */
    public function vote() {
        requireRole('board');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seriesId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $voteValue = isset($_POST['vote']) ? trim($_POST['vote']) : '';
            $memberId = $_SESSION['user_id'];
            
            if ($seriesId <= 0 || !in_array($voteValue, ['approve', 'reject'])) {
                $_SESSION['error'] = "Dữ liệu bỏ phiếu không hợp lệ.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                exit;
            }
            
            $series = $this->seriesModel->findById($seriesId);
            if (!$series) {
                $_SESSION['error'] = "Không tìm thấy bộ truyện.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                exit;
            }
            
            if ($series['status'] !== 'planning') {
                $_SESSION['error'] = "Chỉ có thể bỏ phiếu cho bộ truyện đang ở trạng thái chờ duyệt (Kế hoạch).";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
                exit;
            }
            
            require_once __DIR__ . '/../models/BoardVote.php';
            $boardVoteModel = new \BoardVote();
            
            if ($boardVoteModel->castVote($seriesId, $memberId, $voteValue)) {
                // Gửi thông báo
                require_once __DIR__ . '/../models/Notification.php';
                $notificationModel = new Notification();
                
                $stats = $boardVoteModel->getApprovalStats($seriesId);
                $percentage = $stats['percentage'];
                $approveCount = $stats['approve_count'];
                $totalMembers = $stats['total_members'];
                
                // 1. Thông báo cho Mangaka
                $voteViet = $voteValue === 'approve' ? 'Đồng ý' : 'Từ chối';
                $mangakaMsg = "Đề xuất truyện tranh '{$series['title']}' của bạn vừa nhận thêm 1 phiếu {$voteViet} từ thành viên Hội đồng Biên tập (Tiến độ hiện tại: {$approveCount}/{$totalMembers} phiếu tán thành).";
                $notificationModel->createNotification($series['mangaka_id'], 'series_warning', $mangakaMsg, $seriesId);
                
                // 2. Thông báo cho các thành viên Board khác về việc ghi nhận phiếu bầu mới
                $voterName = $_SESSION['full_name'] ?? 'Một thành viên Hội đồng';
                $totalVotes = $stats['approve_count'] + $stats['reject_count'];
                $boardVoteMsg = "Thành viên {$voterName} vừa bỏ phiếu {$voteViet} cho đề xuất bộ truyện '{$series['title']}' (Tiến độ: {$totalVotes}/{$totalMembers} phiếu).";
                
                require_once __DIR__ . '/../models/User.php';
                $userModel = new \User();
                $boardMembers = $userModel->findByRoleName('board');
                foreach ($boardMembers as $bm) {
                    if ($bm['status'] === 'active' && $bm['user_id'] != $memberId) {
                        $notificationModel->createNotification($bm['user_id'], 'series_warning', $boardVoteMsg, $seriesId);
                    }
                }
                
                // 3. Thông báo cho tất cả thành viên Board nếu đã đầy đủ phiếu bầu
                if ($totalVotes === $totalMembers) {
                    $resultText = $percentage >= 50 ? "đạt đủ tỉ lệ tán thành tối thiểu ({$percentage}%)" : "chưa đạt tỉ lệ tán thành tối thiểu ({$percentage}%)";
                    $boardMsg = "Đề xuất bộ truyện '{$series['title']}' đã nhận đầy đủ phiếu bầu từ Hội đồng và {$resultText}. Vui lòng tiến hành chốt quyết định phê duyệt.";
                    foreach ($boardMembers as $bm) {
                        if ($bm['status'] === 'active') {
                            $notificationModel->createNotification($bm['user_id'], 'series_submitted', $boardMsg, $seriesId);
                        }
                    }
                }
                
                $_SESSION['success'] = "Ghi nhận phiếu bầu thành công.";
            } else {
                $_SESSION['error'] = "Không thể ghi nhận phiếu bầu.";
            }
            
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=publish');
            exit;
        }
    }

    /**
     * AJAX/HTML: Hiển thị danh sách Hồ sơ & Số liệu bảo vệ Series (Dành cho Editor)
     */
    public function dossiers() {
        requireRole('editor');
        $editorId = $_SESSION['user_id'];

        $seriesList = $this->seriesModel->getDossiersByEditorId($editorId);

        // Nạp thêm thông tin xếp hạng mới nhất cho từng series
        require_once __DIR__ . '/../models/SeriesRanking.php';
        $rankingModel = new \SeriesRanking();
        foreach ($seriesList as &$series) {
            $series['latest_ranking'] = $rankingModel->getLatestRanking($series['series_id']);
        }

        require_once __DIR__ . '/../views/editor/dossiers.php';
    }

    /**
     * AJAX/HTML: Chi tiết hồ sơ bảo vệ của một Series cụ thể (Dành cho Editor)
     */
    public function dossierDetail() {
        requireRole('editor');
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $series = $this->seriesModel->findById($id);
        
        if (!$series || !$this->hasSeriesAccess($series)) {
            $_SESSION['error'] = "Không tìm thấy bộ truyện hoặc bạn không phụ trách bộ truyện này.";
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=dossiers');
            exit;
        }

        // Lấy thông tin Tác giả
        require_once __DIR__ . '/../models/User.php';
        $userModel = new \User();
        $mangaka = $userModel->findById($series['mangaka_id']);

        // Lấy danh sách lịch sử xếp hạng của Series
        require_once __DIR__ . '/../models/SeriesRanking.php';
        $rankingModel = new \SeriesRanking();
        $rankingHistory = $rankingModel->findBySeriesId($id);

        // Lấy danh sách chapter
        require_once __DIR__ . '/../models/Chapter.php';
        $chapterModel = new \Chapter();
        $chapters = $chapterModel->findBySeriesId($id);

        require_once __DIR__ . '/../views/editor/dossier_detail.php';
    }

    /**
     * Xử lý cập nhật ghi chú hồ sơ bảo vệ
     */
    public function updateDossierNotes() {
        requireRole('editor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $series = $this->seriesModel->findById($id);
            
            if (!$series || !$this->hasSeriesAccess($series)) {
                $_SESSION['error'] = "Không tìm thấy bộ truyện hoặc bạn không phụ trách bộ truyện này.";
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=dossiers');
                exit;
            }

            $dossierNotes = isset($_POST['dossier_notes']) ? trim($_POST['dossier_notes']) : '';

            try {
                $this->seriesModel->update($id, [
                    'dossier_notes' => $dossierNotes
                ]);
                $_SESSION['success'] = "Cập nhật hồ sơ & biện hộ số liệu bảo vệ bộ truyện '{$series['title']}' thành công!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi cập nhật hồ sơ bảo vệ: " . $e->getMessage();
            }

            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=dossierDetail&id=' . $id);
            exit;
        }
    }
}

