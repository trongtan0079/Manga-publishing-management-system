<?php


require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Page.php';
require_once __DIR__ . '/../models/Chapter.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';


class TaskController extends BaseController
{
    private $taskModel;
    private $pageModel;
    private $chapterModel;
    private $seriesModel;
    private $userModel;
    private $notificationModel;

    /**
     * Hàm khởi tạo Controller
     * Tự động chạy mỗi khi TaskController được gọi.
     * Chịu trách nhiệm kiểm tra đăng nhập và phân quyền chung.
     */
    public function __construct() {
        parent::__construct();
        // Yêu cầu người dùng phải đăng nhập trước khi thao tác
        \requireLogin();
        
        // Kiểm tra xem người dùng hiện tại có thuộc Role hợp lệ không
        // Chỉ Mangaka và Assistant mới được phép thao tác với Task
        $role = $_SESSION['role_name'] ?? '';
        if ($role !== 'mangaka' && $role !== 'assistant') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập quản lý Task.';
            header('Location: /index.php');
            exit;
        }

        // Khởi tạo các Model để thao tác với cơ sở dữ liệu
        $this->taskModel = new Task();
        $this->pageModel = new Page();
        $this->chapterModel = new Chapter();
        $this->seriesModel = new Series();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    /**
     * Hàm kiểm tra quyền sở hữu của Mangaka đối với một Trang truyện cụ thể.
     * Đảm bảo Mangaka không thể tạo/sửa/xóa task ở truyện của người khác.
     * 
     * @param int $pageId ID của trang truyện cần kiểm tra
     * @return array|false Trả về mảng chứa thông tin (page, chapter, series) nếu hợp lệ, false nếu bị lỗi quyền.
     */
    private function checkPageOwnership($pageId) {
        // 1. Tìm thông tin Trang truyện (Page)
        $page = $this->pageModel->findById($pageId);
        if (!$page) return false;

        // 2. Tìm thông tin Chương truyện (Chapter) chứa trang này
        $chapter = $this->chapterModel->findById($page['chapter_id']);
        if (!$chapter) return false;

        // 3. Tìm thông tin Bộ truyện (Series) chứa chương này
        $series = $this->seriesModel->findById($chapter['series_id']);
        if (!$series) return false;

        // 4. So sánh ID của tác giả sở hữu bộ truyện với ID người dùng đang đăng nhập
        if ($series['mangaka_id'] != $_SESSION['user_id']) {
            return false;
        }

        // Nếu qua hết các bước kiểm tra thì xác nhận quyền hợp lệ
        return ['page' => $page, 'chapter' => $chapter, 'series' => $series];
    }

    /**
     * Action: Dashboard dành riêng cho Assistant
     * Liệt kê tất cả các task được giao cho Assistant đang đăng nhập.
     */
    public function index() {
        // Chỉ cho phép Assistant truy cập chức năng này
        \requireRole('assistant');
        
        // Lấy toàn bộ task của người dùng này qua hàm findByAssistantId
        $tasks = $this->taskModel->findByAssistantId($_SESSION['user_id']);
        
        // Nạp view để hiển thị giao diện danh sách task
        require __DIR__ . '/../views/assistant/task_list.php';
    }

    /**
     * Action: Hiển thị form tạo Task mới (Chỉ dành cho Mangaka)
     */
    public function create() {
        // Chỉ Mangaka mới có quyền tạo Task
        \requireRole('mangaka');
        
        // Lấy ID của trang truyện từ URL
        $pageId = $_GET['page_id'] ?? null;
        if (!$pageId) {
            $_SESSION['error'] = 'Thiếu thông tin page_id.';
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        // Kiểm tra bảo mật: đảm bảo mangaka đang thao tác trên trang truyện của chính mình
        $ownership = $this->checkPageOwnership($pageId);
        if (!$ownership) {
            $_SESSION['error'] = 'Bạn không có quyền thao tác trên trang truyện này.';
            header('Location: /index.php?controller=series&action=index');
            exit;
        }

        // Lấy các dữ liệu ngữ cảnh để hiển thị trên giao diện (Tên truyện, Tên chapter, số trang)
        $page = $ownership['page'];
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];
        
        // Lấy danh sách tất cả Assistant để Mangaka chọn người giao việc
        $assistants = $this->userModel->findByRoleName('assistant');

        // Nạp view chứa form tạo task
        require __DIR__ . '/../views/mangaka/task_create.php';
    }

    /**
     * Action: Xử lý lưu Task mới vào cơ sở dữ liệu
     */
    public function store() {
        // Chỉ Mangaka mới có quyền lưu Task
        \requireRole('mangaka');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nhận dữ liệu từ form submit lên
            $pageId = $_POST['page_id'] ?? '';
            $assistantId = $_POST['assistant_id'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';
            $dueDate = $_POST['due_date'] ?? null;

            // Kiểm tra các trường bắt buộc không được để trống
            if (empty($pageId) || empty($assistantId) || empty($title)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ Page, Assistant và Tiêu đề.';
                header("Location: /index.php?controller=task&action=create&page_id=$pageId");
                exit;
            }

            // Kiểm tra phân quyền trước khi thực hiện lưu DB
            if (!$this->checkPageOwnership($pageId)) {
                $_SESSION['error'] = 'Lỗi phân quyền.';
                header('Location: /index.php?controller=series&action=index');
                exit;
            }

            // Thực hiện thêm mới vào bảng tasks
            $this->taskModel->insert([
                'page_id' => $pageId,
                'mangaka_id' => $_SESSION['user_id'], // Lấy ID của Mangaka đang tạo task
                'assistant_id' => $assistantId,
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'status' => 'pending', // Mặc định khi vừa tạo là pending (Chưa làm)
                'due_date' => $dueDate ?: null
            ]);

            // Đồng thời tạo một thông báo gửi tới Assistant vừa được giao việc
            $this->notificationModel->createNotification(
                $assistantId,
                'task_assigned',
                'Bạn được giao công việc mới: ' . $title
            );

            // Chuyển hướng quay lại trang chi tiết (page_detail) cùng thông báo thành công
            $_SESSION['success'] = 'Đã giao task thành công.';
            header("Location: /index.php?controller=page&action=show&id=$pageId");
            exit;
        }
    }

    /**
     * Action: Hiển thị form chỉnh sửa Task hiện có (Dành cho Mangaka)
     */
    public function edit() {
        \requireRole('mangaka');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /index.php');
            exit;
        }

        // Lấy thông tin task cần sửa
        $task = $this->taskModel->findById($id);
        
        // Kiểm tra xem task này có tồn tại và có thuộc về mangaka đang đăng nhập không
        if (!$task || $task['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền sửa task này.';
            header('Location: /index.php');
            exit;
        }

        // Lấy toàn bộ thông tin bối cảnh (page, chapter, series) để hiện trên Form
        $page = $this->pageModel->findById($task['page_id']);
        $chapter = $this->chapterModel->findById($page['chapter_id']);
        $series = $this->seriesModel->findById($chapter['series_id']);
        
        // Lấy danh sách Assistant để Mangaka có quyền đổi người thực hiện (Re-assign)
        $assistants = $this->userModel->findByRoleName('assistant');

        // Nạp view chỉnh sửa task
        require __DIR__ . '/../views/mangaka/task_edit.php';
    }

    /**
     * Action: Xử lý cập nhật thông tin Task
     * Action này được gọi từ cả Mangaka (sửa mọi thứ) và Assistant (chỉ cập nhật status)
     */
    public function update() {
        $id = $_GET['id'] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php');
            exit;
        }

        $task = $this->taskModel->findById($id);
        if (!$task) {
            header('Location: /index.php');
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';

        // LUỒNG 1: Nếu người dùng là Mangaka
        if ($role === 'mangaka') {
            // Phân quyền: Kiểm tra đúng task của mangaka này tạo
            if ($task['mangaka_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Bạn không có quyền sửa task này.';
                header('Location: /index.php');
                exit;
            }

            // Lấy toàn bộ dữ liệu mangaka có thể đổi
            $assistantId = $_POST['assistant_id'] ?? $task['assistant_id'];
            $title = trim($_POST['title'] ?? $task['title']);
            $description = trim($_POST['description'] ?? $task['description']);
            $priority = $_POST['priority'] ?? $task['priority'];
            $status = $_POST['status'] ?? $task['status'];
            $dueDate = $_POST['due_date'] ?? $task['due_date'];

            // Validate dữ liệu trống
            if (empty($title) || empty($assistantId)) {
                $_SESSION['error'] = 'Tiêu đề và Assistant không được để trống.';
                header("Location: /index.php?controller=task&action=edit&id=$id");
                exit;
            }

            // Update tất cả các trường
            $this->taskModel->update($id, [
                'assistant_id' => $assistantId,
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'status' => $status,
                'due_date' => $dueDate ?: null
            ]);

            $_SESSION['success'] = 'Cập nhật task thành công.';
            header("Location: /index.php?controller=page&action=show&id=" . $task['page_id']);
            exit;

        } 
        // LUỒNG 2: Nếu người dùng là Assistant
        elseif ($role === 'assistant') {
            // Phân quyền: Đảm bảo Assistant chỉ update được Task được giao đích danh cho mình
            if ($task['assistant_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Bạn không có quyền sửa task này.';
                header('Location: /index.php');
                exit;
            }

            $status = $_POST['status'] ?? $task['status'];
            $allowedStatus = ['pending', 'in_progress', 'completed'];
            
            // Validate: Assistant chỉ được quyền đổi status hợp lệ, không được sửa title/deadline...
            if (in_array($status, $allowedStatus)) {
                $this->taskModel->update($id, ['status' => $status]);
                $_SESSION['success'] = 'Cập nhật tiến độ thành công.';
            }

            // Trả assistant về dashboard của họ
            header('Location: /index.php?controller=task&action=index');
            exit;
        }
    }

    /**
     * Action: Xóa task (Chỉ dành cho Mangaka)
     */
    public function delete() {
        \requireRole('mangaka');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                // Lấy thông tin task để kiểm tra phân quyền
                $task = $this->taskModel->findById($id);
                
                // Mangaka chỉ xóa được task thuộc về mình
                if ($task && $task['mangaka_id'] == $_SESSION['user_id']) {
                    $this->taskModel->delete($id);
                    $_SESSION['success'] = 'Đã xóa task thành công.';
                    
                    // Xóa xong quay lại trang chứa task đó
                    header("Location: /index.php?controller=page&action=show&id=" . $task['page_id']);
                    exit;
                } else {
                    $_SESSION['error'] = 'Không có quyền xóa task này.';
                }
            }
        }
        header('Location: /index.php?controller=series&action=index');
        exit;
    }
}
