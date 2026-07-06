<?php


require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Notification.php';


/**
 * Class ReviewController
 * 
 * Quản lý hoạt động đánh giá (Review) các bản thảo (Submission).
 * Cho phép Editor đánh giá bản thảo chương truyện (Chapter) và Mangaka đánh giá sản phẩm của Assistant.
 */
class ReviewController extends BaseController
{
    private $reviewModel;
    private $submissionModel;
    private $notificationModel;

    /**
     * Khởi tạo ReviewController.
     * Xác thực đăng nhập và khởi tạo các Model cần thiết.
     */
    public function __construct() {
        parent::__construct();
        \requireLogin();
        $this->reviewModel = new Review();
        $this->submissionModel = new Submission();
        $this->notificationModel = new Notification();
    }

    /**
     * Hiển thị danh sách các bản thảo cần đánh giá dựa trên vai trò của người dùng.
     */
    public function index() {
        $role = $_SESSION['role_name'] ?? '';
        if ($role === 'editor') {
            // Editor chỉ xem các bản thảo của các bộ truyện được phân công gán phụ trách
            $submissions = $this->submissionModel->findAllChapterSubmissionsByEditorId($_SESSION['user_id']);

            // Hỗ trợ lọc trạng thái theo query parameter
            $status = $_GET['status'] ?? null;
            if ($status) {
                if ($status === 'pending') {
                    $submissions = array_filter($submissions, function($s) {
                        return $s['status'] === 'pending';
                    });
                } elseif ($status === 'reviewed') {
                    $submissions = array_filter($submissions, function($s) {
                        return in_array($s['status'], ['approved', 'rejected']);
                    });
                }
            }
            require_once __DIR__ . '/../views/editor/review_list.php';
        } elseif ($role === 'mangaka') {
            // Mangaka xem các bản thảo của Task đang chờ duyệt thuộc về các Task do họ giao
            $userId = $_SESSION['user_id'];
            $submissions = $this->submissionModel->findPendingSubmissionsByMangakaId($userId);
            require_once __DIR__ . '/../views/editor/review_list.php';
        } else {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=dashboard&action=' . $role);
            exit;
        }
    }

    /**
     * Kiểm tra quyền đánh giá bản thảo của người dùng hiện tại.
     * 
     * @param array $submission Thông tin bản thảo cần kiểm tra
     * @return bool Trả về true nếu có quyền, ngược lại trả về false
     */
    private function checkReviewPermission($submission) {
        $role = $_SESSION['role_name'] ?? '';

        // 1. Xác định chapter_id tương ứng
        $chapterId = null;
        if ($submission['chapter_id']) {
            $chapterId = $submission['chapter_id'];
        } elseif ($submission['task_id']) {
            require_once __DIR__ . '/../models/Task.php';
            $taskModel = new \Task();
            $task = $taskModel->findById($submission['task_id']);
            if ($task) {
                require_once __DIR__ . '/../models/Page.php';
                $pageModel = new \Page();
                $page = $pageModel->findById($task['page_id']);
                if ($page) {
                    $chapterId = $page['chapter_id'];
                }
            }
        }

        // 2. Kiểm tra xem chapter và series có bị khóa hoặc bộ truyện bị tạm ngưng/hủy/hoàn thành không
        if ($chapterId) {
            require_once __DIR__ . '/../models/Chapter.php';
            $chapterModel = new \Chapter();
            $chapter = $chapterModel->findById($chapterId);
            if ($chapter) {
                if ($chapter['status'] === 'approved' || $chapter['status'] === 'published') {
                    return false;
                }
                require_once __DIR__ . '/../models/Series.php';
                $seriesModel = new \Series();
                $series = $seriesModel->findById($chapter['series_id']);
                if ($series && in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
                    return false;
                }
            }
        }

        // Editor có quyền đánh giá bản thảo của Chương truyện (chapter_id không rỗng) và phải được gán phụ trách
        if ($role === 'editor' && $submission['chapter_id']) {
            require_once __DIR__ . '/../models/Chapter.php';
            $chapterModel = new \Chapter();
            $chapter = $chapterModel->findById($submission['chapter_id']);
            if ($chapter) {
                require_once __DIR__ . '/../models/Series.php';
                $seriesModel = new \Series();
                $series = $seriesModel->findById($chapter['series_id']);
                if ($series && $series['editor_id'] == $_SESSION['user_id'] && $series['status'] !== 'planning') {
                    return true;
                }
            }
        }
        // Mangaka có quyền đánh giá bản thảo của Nhiệm vụ (task_id không rỗng)
        if ($role === 'mangaka' && $submission['task_id']) {
            // Kiểm tra xem nhiệm vụ này có phải do Mangaka này giao hay không
            require_once __DIR__ . '/../models/Task.php';
            $taskModel = new \Task();
            $task = $taskModel->findById($submission['task_id']);
            if ($task && $task['mangaka_id'] == $_SESSION['user_id']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hiển thị giao diện tạo đánh giá mới cho bản thảo.
     */
    public function create() {
        if (!isset($_GET['submission_id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        $submissionId = $_GET['submission_id'];
        $submission = $this->submissionModel->findWithMetadataById($submissionId);
        
        if (!$submission || !$this->checkReviewPermission($submission)) {
            $_SESSION['error'] = 'Không tìm thấy bản thảo hoặc bạn không có quyền đánh giá.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        require_once __DIR__ . '/../views/editor/review_create.php';
    }

    /**
     * Lưu thông tin đánh giá mới vào cơ sở dữ liệu và cập nhật trạng thái bản thảo.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submissionId = $_POST['submission_id'] ?? null;
            $comments = trim($_POST['comments'] ?? '');
            $rating = $_POST['rating'] ?? null;
            $decision = $_POST['decision'] ?? '';

            if (empty($comments) || empty($decision)) {
                $_SESSION['error'] = 'Comments và Decision là bắt buộc.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
                exit;
            }

            if ($decision !== 'approved' && $decision !== 'rejected') {
                $_SESSION['error'] = 'Quyết định không hợp lệ.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
                exit;
            }

            if (!empty($rating)) {
                $ratingVal = intval($rating);
                if ($ratingVal < 1 || $ratingVal > 10) {
                    $_SESSION['error'] = 'Điểm số phải nằm trong khoảng từ 1 đến 10.';
                    header('Location: ' . BASE_PATH . '/index.php?controller=review&action=create&submission_id=' . $submissionId);
                    exit;
                }
                $rating = $ratingVal;
            } else {
                $rating = null;
            }

            $submission = $this->submissionModel->findById($submissionId);
            if (!$submission || !$this->checkReviewPermission($submission)) {
                $_SESSION['error'] = 'Bạn không có quyền đánh giá bản thảo này.';
                header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
                exit;
            }

            // Thêm mới thông tin đánh giá
            $reviewerId = $_SESSION['user_id'];
            $data = [
                'submission_id' => $submissionId,
                'reviewer_id' => $reviewerId,
                'comments' => $comments,
                'rating' => empty($rating) ? null : $rating
            ];
            $this->reviewModel->insert($data);

            // Cập nhật trạng thái của bản thảo (Phê duyệt hoặc Từ chối)
            $status = ($decision === 'approved') ? 'approved' : 'rejected';
            $this->submissionModel->update($submissionId, ['status' => $status]);

            // Xác định chi tiết bản thảo được review
            $itemInfo = 'bản thảo';
            if (!empty($submission['chapter_id'])) {
                require_once __DIR__ . '/../models/Chapter.php';
                require_once __DIR__ . '/../models/Series.php';
                $chapModel = new \Chapter();
                $serModel = new \Series();
                $chapter = $chapModel->findById($submission['chapter_id']);
                $series = $chapter ? $serModel->findById($chapter['series_id']) : null;
                $seriesTitle = $series ? $series['title'] : 'Không rõ';
                $chapNum = $chapter ? $chapter['chapter_number'] : 'Không rõ';
                $itemInfo = "Chapter {$chapNum} của bộ truyện '{$seriesTitle}'";
            } elseif (!empty($submission['task_id'])) {
                require_once __DIR__ . '/../models/Task.php';
                require_once __DIR__ . '/../models/Page.php';
                require_once __DIR__ . '/../models/Series.php';
                $tModel = new \Task();
                $pModel = new \Page();
                $sModel = new \Series();
                
                $task = $tModel->findById($submission['task_id']);
                $page = $task ? $pModel->findById($task['page_id']) : null;
                $chapter = $page ? $pModel->getConnection()->query("SELECT * FROM chapters WHERE chapter_id = " . intval($page['chapter_id']))->fetch(PDO::FETCH_ASSOC) : null;
                $series = $chapter ? $sModel->findById($chapter['series_id']) : null;
                
                $seriesTitle = $series ? $series['title'] : 'Không rõ';
                $chapNum = $chapter ? $chapter['chapter_number'] : 'Không rõ';
                $pageNum = $page ? $page['page_number'] : 'Không rõ';
                $itemInfo = "bản vẽ cho công việc '{$task['title']}' thuộc bộ truyện '{$seriesTitle}' (Chương {$chapNum} - Trang {$pageNum})";
            }

            // Gửi thông báo đến người nộp bản thảo
            $this->notificationModel->createNotification(
                $submission['user_id'],
                'review_created',
                "Có nhận xét đánh giá mới cho {$itemInfo}."
            );

            $statusText = $status === 'approved' ? 'ĐƯỢC PHÊ DUYỆT thành công' : 'BỊ TỪ CHỐI';
            $notifType = $status === 'approved' ? 'submission_approved' : 'submission_rejected';
            
            $this->notificationModel->createNotification(
                $submission['user_id'],
                $notifType,
                "Bản thảo của bạn cho {$itemInfo} đã {$statusText}. Nhận xét: " . mb_substr($comments, 0, 80) . "..."
            );

            // Nếu là bản thảo nhiệm vụ (Task) và được duyệt, cập nhật trạng thái Task thành 'completed'
            if ($submission['task_id'] && $status === 'approved') {
                require_once __DIR__ . '/../models/Task.php';
                $taskModel = new \Task();
                $taskModel->update($submission['task_id'], ['status' => 'completed']);

                // Đồng thời cập nhật trạng thái PageRegion liên kết thành 'completed'
                $taskDetail = $taskModel->findById($submission['task_id']);
                if ($taskDetail) {
                    $pageId = $taskDetail['page_id'];
                    require_once __DIR__ . '/../models/PageRegion.php';
                    $pageRegionModel = new \PageRegion();

                    if (!empty($taskDetail['page_region_id'])) {
                        $pageRegionModel->update($taskDetail['page_region_id'], ['status' => 'completed']);
                    }

                    // Tự động duyệt trang vẽ (Page status = 'approved') nếu tất cả các Task của trang này đã hoàn thành
                    $tasksOnPage = $taskModel->findByPageId($pageId);
                    $allTasksCompleted = true;
                    foreach ($tasksOnPage as $t) {
                        if ($t['status'] !== 'completed') {
                            $allTasksCompleted = false;
                            break;
                        }
                    }

                    // Đồng thời kiểm tra xem tất cả các phân vùng của trang này đã hoàn thành chưa (để đồng bộ)
                    $regions = $pageRegionModel->findByPageId($pageId);
                    $allRegionsCompleted = true;
                    foreach ($regions as $r) {
                        if ($r['status'] !== 'completed') {
                            $allRegionsCompleted = false;
                            break;
                        }
                    }

                    if ($allTasksCompleted && $allRegionsCompleted) {
                        require_once __DIR__ . '/../models/Page.php';
                        $pageModel = new \Page();
                        $pageModel->update($pageId, ['status' => 'approved']);
                    }
                }
            } elseif ($submission['task_id'] && $status === 'rejected') {
                require_once __DIR__ . '/../models/Task.php';
                $taskModel = new \Task();
                $taskModel->update($submission['task_id'], ['status' => 'pending']);

                $taskDetail = $taskModel->findById($submission['task_id']);
                if ($taskDetail && !empty($taskDetail['page_region_id'])) {
                    require_once __DIR__ . '/../models/PageRegion.php';
                    $pageRegionModel = new \PageRegion();
                    $pageRegionModel->update($taskDetail['page_region_id'], ['status' => 'pending']);
                }
            }

            // Nếu là bản thảo chương truyện (Chapter)
            if ($submission['chapter_id']) {
                require_once __DIR__ . '/../models/Chapter.php';
                $chapterModel = new \Chapter();
                if ($status === 'approved') {
                    $chapterModel->update($submission['chapter_id'], ['status' => 'approved']);

                    // Kiểm tra xem chapter có phải là chương cuối không để thông báo cho Board
                    $chapDetail = $chapterModel->findById($submission['chapter_id']);
                    if ($chapDetail && !empty($chapDetail['is_final'])) {
                        // Lấy thông tin bộ truyện
                        require_once __DIR__ . '/../models/Series.php';
                        $seriesModel = new \Series();
                        $seriesDetail = $seriesModel->findById($chapDetail['series_id']);
                        $seriesTitle = $seriesDetail ? $seriesDetail['title'] : 'bộ truyện';
                        
                        // Tìm toàn bộ tài khoản có role là 'board' đang hoạt động để gửi thông báo
                        require_once __DIR__ . '/../models/User.php';
                        $userModel = new \User();
                        $boardMembers = $userModel->findByRoleName('board');
                        
                        if (!empty($boardMembers)) {
                            foreach ($boardMembers as $member) {
                                $this->notificationModel->createNotification(
                                    $member['user_id'],
                                    'series_completed',
                                    "Chương cuối của bộ truyện '{$seriesTitle}' đã được duyệt. Vui lòng xác nhận hoàn thành."
                                );
                            }
                        }
                    }
                } else {
                    // Nếu bị từ chối (rejected), tự động chuyển trạng thái Chapter về 'drawing' để Mangaka chỉnh sửa
                    $chapterModel->update($submission['chapter_id'], ['status' => 'drawing']);
                }
            }

            $_SESSION['success'] = 'Đã đánh giá bản thảo thành công.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
    }

    /**
     * Hiển thị chi tiết một đánh giá cụ thể.
     */
    public function show() {
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }
        
        $reviewId = $_GET['id'];
        $review = $this->reviewModel->findById($reviewId);
        
        if (!$review) {
            $_SESSION['error'] = 'Không tìm thấy đánh giá.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }

        $submission = $this->submissionModel->findWithMetadataById($review['submission_id']);
        
        // Kiểm tra quyền xem đánh giá: Chỉ những người liên quan trực tiếp mới được xem
        $role = $_SESSION['role_name'] ?? '';
        $userId = $_SESSION['user_id'];
        $hasAccess = false;
        if ($role === 'editor') $hasAccess = true;
        if ($submission['user_id'] == $userId) $hasAccess = true;
        if ($review['reviewer_id'] == $userId) $hasAccess = true;
        
        if (!$hasAccess) {
            $_SESSION['error'] = 'Bạn không có quyền xem đánh giá này.';
            header('Location: ' . BASE_PATH . '/index.php?controller=review&action=index');
            exit;
        }

        require_once __DIR__ . '/../views/editor/review_detail.php';
    }

    /**
     * AJAX: Lưu ghi chú/đánh dấu lỗi trực quan của Editor trên trang truyện
     */
    public function save_annotation() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Phương thức không được hỗ trợ']);
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        if ($role !== 'editor') {
            echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thực hiện chức năng này']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $pageId = isset($input['page_id']) ? intval($input['page_id']) : 0;
        $x = isset($input['x']) ? intval($input['x']) : 0;
        $y = isset($input['y']) ? intval($input['y']) : 0;
        $width = isset($input['width']) ? intval($input['width']) : 0;
        $height = isset($input['height']) ? intval($input['height']) : 0;
        $comments = isset($input['comments']) ? trim($input['comments']) : '';

        if ($pageId <= 0 || empty($comments)) {
            echo json_encode(['success' => false, 'error' => 'Thông tin không hợp lệ hoặc thiếu nội dung']);
            exit;
        }

        // Kiểm tra trạng thái khóa của chapter
        require_once __DIR__ . '/../models/Page.php';
        $pageModel = new Page();
        $page = $pageModel->findById($pageId);
        if (!$page) {
            echo json_encode(['success' => false, 'error' => 'Trang truyện không tồn tại']);
            exit;
        }

        require_once __DIR__ . '/../models/Chapter.php';
        $chapterModel = new Chapter();
        $chapter = $chapterModel->findById($page['chapter_id']);
        if ($chapter) {
            if ($chapter['status'] === 'approved' || $chapter['status'] === 'published') {
                echo json_encode(['success' => false, 'error' => 'Chương truyện đã được phê duyệt hoặc xuất bản, không thể chỉnh sửa ghi chú lỗi']);
                exit;
            }
            require_once __DIR__ . '/../models/Series.php';
            $seriesModel = new Series();
            $series = $seriesModel->findById($chapter['series_id']);
            if ($series && in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
                echo json_encode(['success' => false, 'error' => 'Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể chỉnh sửa ghi chú lỗi']);
                exit;
            }
        }

        require_once __DIR__ . '/../models/EditorAnnotation.php';
        $editorAnnotationModel = new EditorAnnotation();
        
        $data = [
            'page_id' => $pageId,
            'editor_id' => $_SESSION['user_id'],
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'comments' => $comments
        ];

        $insertedId = $editorAnnotationModel->insert($data);
        if ($insertedId) {
            echo json_encode([
                'success' => true, 
                'annotation_id' => $insertedId,
                'editor_name' => $_SESSION['full_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Không thể lưu ghi chú vào CSDL']);
        }
        exit;
    }

    /**
     * AJAX: Xóa ghi chú lỗi của Editor
     */
    public function delete_annotation() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Phương thức không được hỗ trợ']);
            exit;
        }

        $role = $_SESSION['role_name'] ?? '';
        if ($role !== 'editor') {
            echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thực hiện chức năng này']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $annotationId = isset($input['annotation_id']) ? intval($input['annotation_id']) : 0;

        if ($annotationId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID không hợp lệ']);
            exit;
        }

        require_once __DIR__ . '/../models/EditorAnnotation.php';
        $editorAnnotationModel = new EditorAnnotation();
        
        $annotation = $editorAnnotationModel->findById($annotationId);
        if (!$annotation) {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy ghi chú']);
            exit;
        }

        if ($annotation['editor_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'error' => 'Bạn không thể xóa ghi chú của Editor khác']);
            exit;
        }

        // Kiểm tra trạng thái khóa của chapter
        require_once __DIR__ . '/../models/Page.php';
        $pageModel = new Page();
        $page = $pageModel->findById($annotation['page_id']);
        if ($page) {
            require_once __DIR__ . '/../models/Chapter.php';
            $chapterModel = new Chapter();
            $chapter = $chapterModel->findById($page['chapter_id']);
            if ($chapter) {
                if ($chapter['status'] === 'approved' || $chapter['status'] === 'published') {
                    echo json_encode(['success' => false, 'error' => 'Chương truyện đã được phê duyệt hoặc xuất bản, không thể xóa ghi chú lỗi']);
                    exit;
                }
                require_once __DIR__ . '/../models/Series.php';
                $seriesModel = new Series();
                $series = $seriesModel->findById($chapter['series_id']);
                if ($series && in_array($series['status'], ['suspended', 'canceled', 'completed'])) {
                    echo json_encode(['success' => false, 'error' => 'Bộ truyện đã tạm ngưng, đã hủy hoặc đã hoàn thành. Không thể xóa ghi chú lỗi']);
                    exit;
                }
            }
        }

        $result = $editorAnnotationModel->delete($annotationId);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Không thể xóa ghi chú từ CSDL']);
        }
        exit;
    }

    /**
     * AJAX: Lấy danh sách ghi chú lỗi của Editor trên trang truyện
     */
    public function get_annotations() {
        header('Content-Type: application/json');
        $pageId = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
        
        if ($pageId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID không hợp lệ']);
            exit;
        }

        require_once __DIR__ . '/../models/EditorAnnotation.php';
        $editorAnnotationModel = new EditorAnnotation();
        $annotations = $editorAnnotationModel->findByPageId($pageId);
        
        echo json_encode(['success' => true, 'annotations' => $annotations]);
        exit;
    }
}
