<?php 
/**
 * View: Sửa Task
 * @var array $task Thông tin task hiện tại
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang
 * @var array $series Thông tin series
 * @var array $assistants Danh sách assistant
 */
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="mb-3">
    <a href="<?= BASE_PATH ?>/index.php?controller=page&action=show&id=<?= htmlspecialchars($page['page_id']) ?>" class="btn btn-secondary">&larr; Quay lại Trang</a>
</div>

<!-- 
  Form cập nhật Task hiện tại.
  Action trỏ về hàm update() trong TaskController với ID task cần sửa.
-->
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Cập nhật Task</h4>
        <!-- Bối cảnh công việc -->
        <small>Trang <?= htmlspecialchars($page['page_number']) ?> - Chapter <?= htmlspecialchars($chapter['chapter_number']) ?> (<?= htmlspecialchars($series['title']) ?>)</small>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/index.php?controller=task&action=update&id=<?= $task['task_id'] ?>" method="POST">
            <!-- Tiêu đề công việc -->
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề công việc <span class="text-danger">*</span></label>
                <!-- Đổ dữ liệu cũ vào value -->
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>

            <!-- Mô tả công việc -->
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả (Tùy chọn)</label>
                <!-- Đổ nội dung cũ vào giữa thẻ textarea -->
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
            </div>

            <div class="row mb-3">
                <!-- Dropdown chọn Assistant -->
                <div class="col-md-4">
                    <label for="assistant_id" class="form-label">Giao cho (Assistant) <span class="text-danger">*</span></label>
                    <select class="form-select" id="assistant_id" name="assistant_id" required>
                        <option value="">-- Chọn Assistant --</option>
                        <?php foreach ($assistants as $assistant): ?>
                            <!-- Thêm thuộc tính selected nếu assistant này là người đang được giao -->
                            <option value="<?= $assistant['user_id'] ?>" <?= $assistant['user_id'] == $task['assistant_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($assistant['full_name']) ?> (<?= htmlspecialchars($assistant['username']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Dropdown thay đổi mức độ ưu tiên -->
                <div class="col-md-4">
                    <label for="priority" class="form-label">Mức độ ưu tiên</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="low" <?= $task['priority'] == 'low' ? 'selected' : '' ?>>Thấp (Low)</option>
                        <option value="medium" <?= $task['priority'] == 'medium' ? 'selected' : '' ?>>Trung bình (Medium)</option>
                        <option value="high" <?= $task['priority'] == 'high' ? 'selected' : '' ?>>Cao (High)</option>
                    </select>
                </div>
                
                <!-- Dropdown thay đổi trạng thái tiến độ -->
                <div class="col-md-4">
                    <label for="status" class="form-label">Trạng thái (Status)</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending (Chưa bắt đầu)</option>
                        <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress (Đang làm)</option>
                        <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed (Đã xong)</option>
                    </select>
                </div>
            </div>

            <!-- Hạn chót công việc -->
            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót (Due Date)</label>
                <!-- Định dạng lại chuỗi datetime để gán vào input type="datetime-local" -->
                <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?= $task['due_date'] ? date('Y-m-d\TH:i', strtotime($task['due_date'])) : '' ?>">
            </div>

            <button type="submit" class="btn btn-warning">Lưu thay đổi</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
