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
            header('Location: ' . BASE_PATH . '/index.php');
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
        $pageId = intval($pageId);
        if ($pageId <= 0) return false;

        // 1. Tìm thông tin Trang truyện (Page)
        $page = $this->pageModel->findById($pageId);
        if (!$page) return false;

        // 2. Tìm thông tin Chương truyện (Chapter) chứa trang này
        $chapter = $this->chapterModel->findById($page['chapter_id']);
        if (!$chapter) return false;

        // 3. Tìm thông tin Bộ truyện (Series) chứa chương này
        $series = $this->seriesModel->findById($chapter['series_id']);
        if (!$series) return false;

        // Chặn sửa đổi task nếu bộ truyện không hoạt động chính thức (ongoing)
        $action = $_GET['action'] ?? '';
        if (in_array($action, ['create', 'store', 'edit', 'update', 'delete']) && $series['status'] !== 'ongoing') {
            return false;
        }

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
        $role = $_SESSION['role_name'] ?? '';
        
        if ($role === 'mangaka') {
            $tasks = $this->taskModel->findByMangakaId($_SESSION['user_id']);
            
            // Hỗ trợ lọc trạng thái theo query parameter
            $status = $_GET['status'] ?? null;
            if ($status && in_array($status, ['pending', 'in_progress', 'submitted', 'rejected', 'completed'])) {
                $tasks = array_filter($tasks, function($t) use ($status) {
                    return $t['status'] === $status;
                });
            }
            
            require __DIR__ . '/../views/mangaka/task_list.php';
            exit;
        }

        // Chỉ cho phép Assistant truy cập chức năng này
        \requireRole('assistant');
        
        // Lấy toàn bộ task của người dùng này qua hàm findByAssistantId
        $tasks = $this->taskModel->findByAssistantId($_SESSION['user_id']);
        
        // Hỗ trợ lọc trạng thái theo query parameter
        $status = $_GET['status'] ?? null;
        if ($status && in_array($status, ['pending', 'in_progress', 'submitted', 'rejected', 'completed'])) {
            $tasks = array_filter($tasks, function($t) use ($status) {
                return $t['status'] === $status;
            });
        }
        
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
        $pageId = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
        if ($pageId <= 0) {
            $_SESSION['error'] = 'Thiếu thông tin page_id hoặc page_id không hợp lệ.';
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        // Kiểm tra bảo mật: đảm bảo mangaka đang thao tác trên trang truyện của chính mình
        $ownership = $this->checkPageOwnership($pageId);
        if (!$ownership) {
            $_SESSION['error'] = 'Bạn không có quyền thao tác trên trang truyện này hoặc trang truyện không tồn tại.';
            header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
            exit;
        }

        $chapter = $ownership['chapter'];
        if ($this->isChapterLocked($chapter)) {
            $_SESSION['error'] = 'Chương truyện chứa trang này đang chờ duyệt, đã duyệt hoặc đã xuất bản, không thể giao thêm việc.';
            header('Location: ' . BASE_PATH . '/index.php?controller=chapter&action=show&id=' . $chapter['chapter_id']);
            exit;
        }

        // Lấy các dữ liệu ngữ cảnh để hiển thị trên giao diện (Tên truyện, Tên chapter, số trang)
        $page = $ownership['page'];
        $chapter = $ownership['chapter'];
        $series = $ownership['series'];
        
        // Lấy danh sách các vùng của trang truyện (PageRegion) đã phân đoạn
        require_once __DIR__ . '/../models/PageRegion.php';
        $pageRegionModel = new PageRegion();
        $regions = $pageRegionModel->findByPageId($pageId);
        
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
            $pageId = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
            $assistantId = isset($_POST['assistant_id']) ? intval($_POST['assistant_id']) : 0;

            $pageRegionId = !empty($_POST['page_region_id']) ? intval($_POST['page_region_id']) : null;
            $groupedRegionIds = !empty($_POST['grouped_region_ids']) ? trim($_POST['grouped_region_ids']) : null;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $isMultiTask = isset($_POST['is_multi_task']) && $_POST['is_multi_task'] == '1';
            $taskType = isset($_POST['task_type']) ? trim($_POST['task_type']) : 'other';
            if ($taskType === 'other' && !empty($_POST['custom_task_type'])) {
                $taskType = trim($_POST['custom_task_type']);
            }
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $resourceUrl = isset($_POST['resource_url']) ? trim($_POST['resource_url']) : '';
            $priority = isset($_POST['priority']) ? $_POST['priority'] : 'medium';
            $dueDate = isset($_POST['due_date']) ? $_POST['due_date'] : null;

            // 1. Validation: Title
            if (empty($title)) {
                $_SESSION['error'] = 'Tiêu đề công việc không được để trống.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                exit;
            }
            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = 'Tiêu đề công việc không được vượt quá 255 ký tự.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                exit;
            }

            // 2. Validation: page_id ownership
            $ownership = $this->checkPageOwnership($pageId);
            if ($pageId <= 0 || !$ownership) {
                $_SESSION['error'] = 'Lỗi phân quyền hoặc trang truyện không hợp lệ.';
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }
            $chapter = $ownership['chapter'];
            $page = $ownership['page'];
            $series = $ownership['series'];
            if ($this->isChapterLocked($chapter)) {
                $_SESSION['error'] = 'Chương truyện chứa trang này đang chờ duyệt, đã duyệt hoặc đã xuất bản, không thể giao thêm việc.';
                header('Location: ' . BASE_PATH . '/index.php?controller=chapter&action=show&id=' . $chapter['chapter_id']);
                exit;
            }
            $isDraft = ($chapter['status'] === 'drafting' || $page['status'] === 'drafting');

            // Validation: pageRegionId belongs to pageId
            if ($pageRegionId) {
                require_once __DIR__ . '/../models/PageRegion.php';
                $pageRegionModel = new \PageRegion();
                $region = $pageRegionModel->findById($pageRegionId);
                if (!$region || $region['page_id'] != $pageId) {
                    $_SESSION['error'] = 'Phân vùng của trang truyện không hợp lệ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                    exit;
                }
            }

            // 3. Validation: assistant_id exists and has role 'assistant' and is active
            if ($assistantId <= 0) {
                $_SESSION['error'] = 'Vui lòng chọn Assistant hợp lệ.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                exit;
            }
            $assistant = $this->userModel->getUserByIdWithRole($assistantId);
            if (!$assistant || $assistant['role_name'] !== 'assistant' || $assistant['status'] !== 'active') {
                $_SESSION['error'] = 'Assistant được giao không hợp lệ hoặc đã bị vô hiệu hóa.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                exit;
            }

            // 4. Validation: priority
            $allowedPriorities = ['low', 'medium', 'high'];
            if (!in_array($priority, $allowedPriorities)) {
                $_SESSION['error'] = 'Mức độ ưu tiên không hợp lệ.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                exit;
            }

            // Validate task_type
            if (!$isMultiTask) {
                if (empty($taskType) || strlen($taskType) > 50) {
                    $_SESSION['error'] = 'Loại công việc không hợp lệ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                    exit;
                }
            } else {
                $taskTypes = isset($_POST['task_types']) ? $_POST['task_types'] : [];
                if (empty($taskTypes)) {
                    $_SESSION['error'] = 'Vui lòng chọn ít nhất một loại công việc.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                    exit;
                }
            }

            // 5. Validation: due_date
            $formattedDueDate = null;
            if (!empty($dueDate)) {
                $dueTimestamp = strtotime($dueDate);
                if ($dueTimestamp === false) {
                    $_SESSION['error'] = 'Hạn chót (Due Date) không đúng định dạng.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                    exit;
                }
                if ($dueTimestamp < time()) {
                    $_SESSION['error'] = 'Hạn chót công việc không thể ở quá khứ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=create&page_id=$pageId");
                    exit;
                }
                $formattedDueDate = date('Y-m-d H:i:s', $dueTimestamp);
            }

            // Chuẩn bị thông tin để tạo Task
            $finalTaskType = $taskType;
            $finalTitle = $title;
            $finalDescription = $description;

            if ($isMultiTask) {
                $taskTypes = isset($_POST['task_types']) ? $_POST['task_types'] : [];
                $labels = [];
                foreach ($taskTypes as $type) {
                    $actualType = $type;
                    if ($type === 'other') {
                        $actualType = isset($_POST['custom_multi_task_type']) ? trim($_POST['custom_multi_task_type']) : 'other';
                        if (empty($actualType)) {
                            $actualType = 'other';
                        }
                    }
                    
                    // Map nhãn tiếng Việt
                    if ($actualType === 'background') $labels[] = 'Vẽ nền';
                    elseif ($actualType === 'inking') $labels[] = 'Đi nét';
                    elseif ($actualType === 'coloring') $labels[] = 'Lên màu';
                    elseif ($actualType === 'effects') $labels[] = 'Hiệu ứng';
                    else $labels[] = $actualType;
                }

                // Tiêu đề của Task ghép các loại việc
                $finalTitle = $title . " (Nhóm: " . implode(', ', $labels) . ")";
                
                // Loại công việc được đặt là 'other' vì đây là tổ hợp công việc
                $finalTaskType = 'other';

                // Tự động tạo danh sách checklist trong mô tả công việc (HTML)
                $checklistHtml = "<p><strong>[Nhóm công việc] Trợ lý cần hoàn thành tất cả các mục dưới đây trước khi nộp bài:</strong></p>";
                $checklistHtml .= "<ul>";
                foreach ($labels as $label) {
                    $checklistHtml .= "<li>⬜ " . htmlspecialchars($label) . "</li>";
                }
                $checklistHtml .= "</ul>";
                if (!empty($description)) {
                    $checklistHtml .= "<hr>" . $description;
                }
                $finalDescription = $checklistHtml;
            }

            // Thực hiện thêm mới vào bảng tasks
            $taskId = $this->taskModel->insert([
                'page_id' => $pageId,
                'page_region_id' => $pageRegionId,
                'grouped_region_ids' => $groupedRegionIds,
                'mangaka_id' => $_SESSION['user_id'], // Lấy ID của Mangaka đang tạo task
                'assistant_id' => $assistantId,
                'title' => $finalTitle,
                'task_type' => $finalTaskType,
                'description' => $finalDescription,
                'resource_url' => $resourceUrl,
                'priority' => $priority,
                'status' => 'pending', // Mặc định khi vừa tạo là pending (Chưa làm)
                'due_date' => $formattedDueDate
            ]);

            // Đồng thời cập nhật trạng thái của PageRegion liên kết thành 'in_progress'
            if ($taskId) {
                require_once __DIR__ . '/../models/PageRegion.php';
                $pageRegionModel = new \PageRegion();
                if (!empty($groupedRegionIds)) {
                    $ids = explode(',', $groupedRegionIds);
                    foreach ($ids as $id) {
                        $pageRegionModel->update(intval($id), ['status' => 'in_progress']);
                    }
                } elseif ($pageRegionId) {
                    $pageRegionModel->update($pageRegionId, ['status' => 'in_progress']);
                }
            }

            // Đồng thời tạo một thông báo gửi tới Assistant vừa được giao việc (chỉ khi không phải Bản nháp)
            if ($taskId && !$isDraft) {
                $this->notificationModel->createNotification(
                    $assistantId,
                    'task_assigned',
                    "Bạn được giao công việc mới: '{$finalTitle}' thuộc bộ truyện '{$series['title']}' (Chương {$chapter['chapter_number']} - Trang {$page['page_number']}).",
                    $taskId
                );
            }

            // Đồng bộ lại trạng thái trang truyện
            $this->syncPageStatus($pageId);

            // Chuyển hướng quay lại trang chi tiết (page_detail) cùng thông báo thành công
            $_SESSION['success'] = $isMultiTask ? 'Đã giao nhóm công việc thành công.' : 'Đã giao công việc thành công.';
            header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=$pageId");
            exit;
        } else {
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }
    }

    /**
     * Action: Hiển thị form chỉnh sửa Task hiện có (Dành cho Mangaka)
     */
    public function edit() {
        \requireRole('mangaka');
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID công việc không hợp lệ.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        // Lấy thông tin task cần sửa
        $task = $this->taskModel->findById($id);
        
        // Kiểm tra xem task này có tồn tại và có thuộc về mangaka đang đăng nhập không
        if (!$task || $task['mangaka_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền sửa task này hoặc công việc không tồn tại.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        $page = $this->pageModel->findById($task['page_id']);
        if ($page) {
            $chapter = $this->chapterModel->findById($page['chapter_id']);
            if ($chapter) {
                if ($this->isChapterLocked($chapter)) {
                    $_SESSION['error'] = 'Chương truyện chứa công việc này đang chờ duyệt, đã duyệt hoặc đã xuất bản, không thể sửa công việc.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id']);
                    exit;
                }
                $series = $this->seriesModel->findById($chapter['series_id']);
                if ($series && $series['status'] !== 'ongoing') {
                    $_SESSION['error'] = 'Bộ truyện chưa được phê duyệt hoặc đã kết thúc, tạm ngưng, đã hủy. Không thể chỉnh sửa công việc.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id']);
                    exit;
                }
            }
        }

        // Lấy toàn bộ thông tin bối cảnh (page, chapter, series) để hiện trên Form
        $page = $this->pageModel->findById($task['page_id']);
        $chapter = $this->chapterModel->findById($page['chapter_id']);
        $series = $this->seriesModel->findById($chapter['series_id']);
        
        // Lấy danh sách các vùng của trang truyện (PageRegion) đã phân đoạn
        require_once __DIR__ . '/../models/PageRegion.php';
        $pageRegionModel = new PageRegion();
        $regions = $pageRegionModel->findByPageId($task['page_id']);
        
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
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        $task = $this->taskModel->findById($id);
        if (!$task) {
            $_SESSION['error'] = 'Không tìm thấy công việc cần cập nhật.';
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }

        // Kiểm tra xem chapter có bị khóa không
        $page = $this->pageModel->findById($task['page_id']);
        if ($page) {
            $chapter = $this->chapterModel->findById($page['chapter_id']);
            if ($chapter) {
                if ($this->isChapterLocked($chapter)) {
                    $_SESSION['error'] = 'Chương truyện chứa công việc này đang chờ duyệt, đã duyệt hoặc đã xuất bản, không thể cập nhật.';
                    if ($_SESSION['role_name'] === 'assistant') {
                        header('Location: ' . BASE_PATH . '/index.php?controller=task&action=index');
                    } else {
                        header('Location: ' . BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id']);
                    }
                    exit;
                }
                $series = $this->seriesModel->findById($chapter['series_id']);
                if ($series && $series['status'] !== 'ongoing') {
                    $_SESSION['error'] = 'Bộ truyện chưa được phê duyệt hoặc đã kết thúc, tạm ngưng, đã hủy. Không thể cập nhật công việc.';
                    if ($_SESSION['role_name'] === 'assistant') {
                        header('Location: ' . BASE_PATH . '/index.php?controller=task&action=index');
                    } else {
                        header('Location: ' . BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id']);
                    }
                    exit;
                }
            }
        }

        $role = $_SESSION['role_name'] ?? '';

        // LUỒNG 1: Nếu người dùng là Mangaka
        if ($role === 'mangaka') {
            // Phân quyền: Kiểm tra đúng task của mangaka này tạo
            if ($task['mangaka_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Bạn không có quyền sửa task này.';
                header('Location: ' . BASE_PATH . '/index.php');
                exit;
            }

            // Lấy toàn bộ dữ liệu mangaka có thể đổi
            $assistantId = isset($_POST['assistant_id']) ? intval($_POST['assistant_id']) : intval($task['assistant_id']);
            $pageRegionId = !empty($_POST['page_region_id']) ? intval($_POST['page_region_id']) : null;
            // Giữ lại grouped_region_ids từ form (chặn encode an toàn: chỉ chấp nhận số và dấu phẩy)
            $groupedRegionIds = null;
            if (isset($_POST['grouped_region_ids']) && preg_match('/^[0-9,\s]+$/', $_POST['grouped_region_ids'])) {
                $groupedRegionIds = trim($_POST['grouped_region_ids']);
            } elseif (!empty($task['grouped_region_ids'])) {
                // Fallback: giữ nguyên giá trị cũ nếu không có trong POST
                $groupedRegionIds = $task['grouped_region_ids'];
            }

            // Validation: pageRegionId belongs to task's pageId
            if ($pageRegionId) {
                require_once __DIR__ . '/../models/PageRegion.php';
                $pageRegionModel = new \PageRegion();
                $region = $pageRegionModel->findById($pageRegionId);
                if (!$region || $region['page_id'] != $task['page_id']) {
                    $_SESSION['error'] = 'Phân vùng của trang truyện không hợp lệ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                    exit;
                }
            }
            $title = isset($_POST['title']) ? trim($_POST['title']) : $task['title'];
            $taskType = isset($_POST['task_type']) ? trim($_POST['task_type']) : 'other';
            if ($taskType === 'other' && !empty($_POST['custom_task_type'])) {
                $taskType = trim($_POST['custom_task_type']);
            }
            $description = isset($_POST['description']) ? trim($_POST['description']) : $task['description'];
            $resourceUrl = isset($_POST['resource_url']) ? trim($_POST['resource_url']) : $task['resource_url'];
            $priority = isset($_POST['priority']) ? $_POST['priority'] : $task['priority'];
            $status = isset($_POST['status']) ? $_POST['status'] : $task['status'];
            $dueDate = isset($_POST['due_date']) ? $_POST['due_date'] : $task['due_date'];

            // Validate dữ liệu trống và độ dài
            if (empty($title)) {
                $_SESSION['error'] = 'Tiêu đề không được để trống.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                exit;
            }
            if (mb_strlen($title) > 255) {
                $_SESSION['error'] = 'Tiêu đề không được vượt quá 255 ký tự.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                exit;
            }

            // Validate assistant
            $assistant = $this->userModel->getUserByIdWithRole($assistantId);
            if (!$assistant || $assistant['role_name'] !== 'assistant' || $assistant['status'] !== 'active') {
                $_SESSION['error'] = 'Assistant được giao không hợp lệ hoặc đã bị vô hiệu hóa.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                exit;
            }

            // Validate priority
            if (!in_array($priority, ['low', 'medium', 'high'])) {
                $_SESSION['error'] = 'Mức độ ưu tiên không hợp lệ.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                exit;
            }

            // Validate task_type
            if (empty($taskType) || strlen($taskType) > 50) {
                $_SESSION['error'] = 'Loại công việc không hợp lệ.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                exit;
            }

            // Validate status
            if (!in_array($status, ['pending', 'in_progress', 'completed'])) {
                $_SESSION['error'] = 'Trạng thái công việc không hợp lệ.';
                header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                exit;
            }

            // Validate due_date
            $formattedDueDate = null;
            if (!empty($dueDate)) {
                $dueTimestamp = strtotime($dueDate);
                if ($dueTimestamp === false) {
                    $_SESSION['error'] = 'Hạn chót (Due Date) không hợp lệ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                    exit;
                }
                $formattedDueDate = date('Y-m-d H:i:s', $dueTimestamp);
                if ($dueTimestamp < time() && $formattedDueDate !== $task['due_date']) {
                    $_SESSION['error'] = 'Hạn chót công việc không thể ở quá khứ.';
                    header("Location: " . BASE_PATH . "/index.php?controller=task&action=edit&id=$id");
                    exit;
                }
            }

            // Update tất cả các trường
            $this->taskModel->update($id, [
                'assistant_id' => $assistantId,
                'page_region_id' => $pageRegionId,
                'grouped_region_ids' => $groupedRegionIds,
                'title' => $title,
                'task_type' => $taskType,
                'description' => $description,
                'resource_url' => $resourceUrl,
                'priority' => $priority,
                'status' => $status,
                'due_date' => $formattedDueDate
            ]);

            // Đồng thời cập nhật trạng thái của PageRegion nếu có đổi vùng
            if ($pageRegionId != $task['page_region_id']) {
                require_once __DIR__ . '/../models/PageRegion.php';
                $pageRegionModel = new \PageRegion();
                // 1. Trả trạng thái phân vùng cũ (nếu có) về 'pending'
                if ($task['page_region_id']) {
                    $pageRegionModel->update($task['page_region_id'], ['status' => 'pending']);
                }
                // 2. Cập nhật trạng thái phân vùng mới (nếu có) thành 'in_progress'
                if ($pageRegionId) {
                    $pageRegionModel->update($pageRegionId, ['status' => 'in_progress']);
                }
            }

            // Gửi thông báo đến assistant nếu không phải bản nháp
            $page = $this->pageModel->findById($task['page_id']);
            $isDraft = false;
            if ($page) {
                $chapter = $this->chapterModel->findById($page['chapter_id']);
                if ($chapter && $chapter['status'] === 'drafting') {
                    $isDraft = true;
                }
            }
            if (!$isDraft) {
                $series = $chapter ? $this->seriesModel->findById($chapter['series_id']) : null;
                $seriesTitle = $series ? $series['title'] : 'Không rõ';
                $chapNum = $chapter ? $chapter['chapter_number'] : 'Không rõ';
                $pageNum = $page ? $page['page_number'] : 'Không rõ';

                $this->notificationModel->createNotification(
                    $assistantId,
                    'task_assigned',
                    "Mangaka " . $_SESSION['full_name'] . " đã cập nhật thông tin công việc: '{$title}' thuộc bộ truyện '{$seriesTitle}' (Chương {$chapNum} - Trang {$pageNum}).",
                    $id
                );
                if ($assistantId != $task['assistant_id'] && !empty($task['assistant_id'])) {
                    $this->notificationModel->createNotification(
                        $task['assistant_id'],
                        'task_assigned',
                        "Công việc '{$task['title']}' trước đó của bạn (thuộc bộ truyện '{$seriesTitle}', Chương {$chapNum} - Trang {$pageNum}) đã được chuyển giao cho người khác.",
                        $id
                    );
                }
            }

            $this->syncPageStatus($task['page_id']);
            $_SESSION['success'] = 'Cập nhật task thành công.';
            header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $task['page_id']);
            exit;

        } 
        // LUỒNG 2: Nếu người dùng là Assistant
        elseif ($role === 'assistant') {
            // Phân quyền: Đảm bảo Assistant chỉ update được Task được giao đích danh cho mình
            if ($task['assistant_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Bạn không có quyền sửa task này.';
                header('Location: ' . BASE_PATH . '/index.php?controller=task&action=index');
                exit;
            }

            // Chặn thay đổi trạng thái nếu công việc đã hoàn thành
            if ($task['status'] === 'completed') {
                $_SESSION['error'] = 'Công việc đã hoàn thành và được duyệt, không thể thay đổi trạng thái.';
                header('Location: ' . BASE_PATH . '/index.php?controller=task&action=index');
                exit;
            }

            $status = isset($_POST['status']) ? $_POST['status'] : $task['status'];
            $allowedStatus = ['pending', 'in_progress'];
            if ($task['status'] === 'completed') {
                $allowedStatus[] = 'completed'; // Cho phép giữ nguyên completed nếu trước đó đã completed
            }
            
            // Validate: Assistant chỉ được quyền đổi status hợp lệ, không được sửa title/deadline...
            if (in_array($status, $allowedStatus)) {
                $this->taskModel->update($id, ['status' => $status]);
                $this->syncPageStatus($task['page_id']);
                $_SESSION['success'] = 'Cập nhật tiến độ thành công.';
            } else {
                $_SESSION['error'] = 'Trạng thái cập nhật không hợp lệ hoặc bạn không có quyền tự đánh dấu hoàn thành.';
            }

            // Trả assistant về dashboard của họ
            header('Location: ' . BASE_PATH . '/index.php?controller=task&action=index');
            exit;
        }
    }

    /**
     * Action: Xóa task (Chỉ dành cho Mangaka)
     */
    public function delete() {
        \requireRole('mangaka');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($id <= 0) {
                $_SESSION['error'] = 'ID công việc không hợp lệ.';
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            // Lấy thông tin task để kiểm tra phân quyền
            $task = $this->taskModel->findById($id);
            if (!$task) {
                $_SESSION['error'] = 'Không tìm thấy công việc cần xóa.';
                header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
                exit;
            }

            // Kiểm tra xem chapter có bị khóa không
            $page = $this->pageModel->findById($task['page_id']);
            if ($page) {
                $chapter = $this->chapterModel->findById($page['chapter_id']);
                if ($chapter) {
                    if ($this->isChapterLocked($chapter)) {
                        $_SESSION['error'] = 'Chương truyện chứa công việc này đang chờ duyệt, đã duyệt hoặc đã xuất bản, không thể xóa.';
                        header('Location: ' . BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id']);
                        exit;
                    }
                    $series = $this->seriesModel->findById($chapter['series_id']);
                    if ($series && $series['status'] !== 'ongoing') {
                        $_SESSION['error'] = 'Bộ truyện chưa được phê duyệt hoặc đã kết thúc, tạm ngưng, đã hủy. Không thể xóa công việc.';
                        header('Location: ' . BASE_PATH . '/index.php?controller=page&action=show&id=' . $task['page_id']);
                        exit;
                    }
                }
            }

            // Mangaka chỉ xóa được task thuộc về mình
            if ($task['mangaka_id'] == $_SESSION['user_id']) {
                // Xóa các file vật lý của bản nộp thuộc task này trước
                require_once __DIR__ . '/../models/Submission.php';
                $submissionModel = new \Submission();
                $subs = $submissionModel->findByTaskId($id);
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
                // Hoàn trả trạng thái phân vùng liên kết (nếu có)
                require_once __DIR__ . '/../models/PageRegion.php';
                $pageRegionModel = new \PageRegion();
                if (!empty($task['grouped_region_ids'])) {
                    $ids = explode(',', $task['grouped_region_ids']);
                    foreach ($ids as $id) {
                        $pageRegionModel->update(intval($id), ['status' => 'pending']);
                    }
                } elseif (!empty($task['page_region_id'])) {
                    $pageRegionModel->update($task['page_region_id'], ['status' => 'pending']);
                }
                $this->taskModel->delete($id);
                $this->syncPageStatus($task['page_id']);
                $_SESSION['success'] = 'Đã xóa task thành công.';
                
                // Xóa xong quay lại trang chứa task đó
                header("Location: " . BASE_PATH . "/index.php?controller=page&action=show&id=" . $task['page_id']);
                exit;
            } else {
                $_SESSION['error'] = 'Không có quyền xóa task này.';
            }
        } else {
            $_SESSION['error'] = 'Phương thức yêu cầu không hợp lệ.';
        }
        header('Location: ' . BASE_PATH . '/index.php?controller=series&action=index');
        exit;
    }
}
