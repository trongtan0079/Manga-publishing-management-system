<?php 
/**
 * View: Giao Task mới
 * Khai báo biến để Editor/IDE hiểu và không báo lỗi
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang
 * @var array $series Thông tin series
 * @var array $assistants Danh sách assistant
 */
$pageTitle = 'Tạo Công Việc Mới';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= htmlspecialchars($page['page_id']) ?>" class="btn btn-secondary">&larr; Quay lại Trang</a>
</div>

<!-- 
  Form tạo Task mới. Action trỏ về hàm store() trong TaskController bằng phương thức POST.
  - Sử dụng htmlspecialchars để chống lỗi XSS bảo mật.
-->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Tạo công việc mới</h4>
        <!-- Hiển thị ngữ cảnh công việc đang được giao cho trang nào, chương nào -->
        <small>Trang <?= htmlspecialchars($page['page_number']) ?> - Chapter <?= htmlspecialchars($chapter['chapter_number']) ?> (<?= htmlspecialchars($series['title']) ?>)</small>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=task&action=store" method="POST">
            <!-- page_id được truyền ngầm để Controller biết task này thuộc về trang nào -->
            <input type="hidden" name="page_id" value="<?= htmlspecialchars($page['page_id']) ?>">
            
            <!-- Trường Tiêu đề (Bắt buộc) -->
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề công việc <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="Ví dụ: Vẽ background, Tô màu nhân vật...">
            </div>

            <!-- Trường Mô tả chi tiết (Tùy chọn) -->
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả chi tiết (Tùy chọn)</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mô tả cụ thể yêu cầu của bạn cho assistant..."></textarea>
            </div>

            <div class="row mb-3">
                <!-- Dropdown chọn Assistant (Bắt buộc) -->
                <div class="col-md-6">
                    <label for="assistant_id" class="form-label">Giao cho (Assistant) <span class="text-danger">*</span></label>
                    <select class="form-select" id="assistant_id" name="assistant_id" required>
                        <option value="">-- Chọn Assistant --</option>
                        <!-- Duyệt danh sách các assistant đang active để hiển thị -->
                        <?php foreach ($assistants as $assistant): ?>
                            <option value="<?= $assistant['user_id'] ?>"><?= htmlspecialchars($assistant['full_name']) ?> (<?= htmlspecialchars($assistant['username']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Dropdown chọn mức độ ưu tiên -->
                <div class="col-md-6">
                    <label for="priority" class="form-label">Mức độ ưu tiên</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="low">Thấp (Low)</option>
                        <option value="medium" selected>Trung bình (Medium)</option>
                        <option value="high">Cao (High)</option>
                    </select>
                </div>
            </div>

            <!-- Trường hạn chót (Sử dụng datetime-local để chọn giờ) -->
            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót</label>
                <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                <div class="form-text">Bạn có thể bỏ trống nếu công việc này không có hạn chót cụ thể.</div>
            </div>

            <button type="submit" class="btn btn-primary">Giao công việc</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
