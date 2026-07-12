<?php 
/**
 * View: Sửa Task
 * @var array $task Thông tin task hiện tại
 * @var array $page Thông tin trang hiện tại
 * @var array $chapter Thông tin chapter chứa trang
 * @var array $series Thông tin series
 * @var array $assistants Danh sách assistant
 */
$pageTitle = 'Cập nhật Công việc';
$current_page = 'series';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
require_once __DIR__ . '/../layouts/sidebar.php';
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
        <h4 class="mb-0">Cập nhật công việc</h4>
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
                <label for="description" class="form-label fw-bold text-slate-700">Mô tả (Tùy chọn)</label>
                <!-- Hidden textarea to store the HTML content for backend submission -->
                <textarea id="description" name="description" style="display: none;"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                
                <!-- Quill container -->
                <div id="quill-editor"></div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Quill Editor
                const quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    placeholder: 'Mô tả cụ thể yêu cầu của bạn cho assistant...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],        // text styling
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],     // lists
                            ['clean']                                         // clear formatting
                        ]
                    }
                });

                // Preload existing task description HTML safely
                const oldContent = document.getElementById('description').value;
                if (oldContent) {
                    quill.root.innerHTML = oldContent;
                }

                // Xử lý loại công việc tự chọn
                const taskTypeSelect = document.getElementById('task_type');
                const customTaskTypeContainer = document.getElementById('custom_task_type_container');
                const customTaskTypeInput = document.getElementById('custom_task_type');

                function toggleCustomTaskType() {
                    if (taskTypeSelect.value === 'other') {
                        customTaskTypeContainer.classList.remove('d-none');
                        customTaskTypeInput.required = true;
                    } else {
                        customTaskTypeContainer.classList.add('d-none');
                        customTaskTypeInput.required = false;
                    }
                }

                if (taskTypeSelect && customTaskTypeContainer && customTaskTypeInput) {
                    taskTypeSelect.addEventListener('change', toggleCustomTaskType);
                    // Chạy ngay lần đầu để đồng bộ trạng thái mặc định
                    toggleCustomTaskType();
                }

                // Sync Quill editor HTML with the hidden textarea on submit
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        const descriptionTextarea = document.getElementById('description');
                        if (quill.getText().trim().length > 0) {
                            descriptionTextarea.value = quill.root.innerHTML;
                        } else {
                            descriptionTextarea.value = '';
                        }
                    });
                }
            });
            </script>

            <!-- Loại công việc và Phân vùng (New) -->
            <?php 
            $isCustomTaskType = !in_array($task['task_type'] ?? 'other', ['background', 'inking', 'coloring', 'effects']);
            ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="task_type" class="form-label fw-bold">Loại công việc <span class="text-danger">*</span></label>
                    <select class="form-select" id="task_type" name="task_type" required>
                        <option value="background" <?= ($task['task_type'] ?? 'other') === 'background' ? 'selected' : '' ?>>Vẽ nền (Background)</option>
                        <option value="inking" <?= ($task['task_type'] ?? 'other') === 'inking' ? 'selected' : '' ?>>Đi nét (Inking)</option>
                        <option value="coloring" <?= ($task['task_type'] ?? 'other') === 'coloring' ? 'selected' : '' ?>>Lên màu (Coloring)</option>
                        <option value="effects" <?= ($task['task_type'] ?? 'other') === 'effects' ? 'selected' : '' ?>>Hiệu ứng (Effects)</option>
                        <option value="other" <?= (($task['task_type'] ?? 'other') === 'other' || $isCustomTaskType) ? 'selected' : '' ?>>Khác (Other)</option>
                    </select>
                    
                    <div class="mt-2 d-none" id="custom_task_type_container">
                        <label for="custom_task_type" class="form-label fw-semibold text-slate-700" style="font-size: 0.85rem;">Nhập loại công việc tự chọn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="custom_task_type" name="custom_task_type" placeholder="Ví dụ: Đổ tone, Dán decal, v.v." value="<?= $isCustomTaskType ? htmlspecialchars($task['task_type']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="page_region_id" class="form-label">Phân vùng ảnh</label>
                    <select class="form-select" id="page_region_id" name="page_region_id">
                        <option value="">-- Toàn bộ trang truyện --</option>
                        <?php if (!empty($regions)): ?>
                            <?php foreach ($regions as $region): 
                                $lbl = ucfirst($region['region_type']) . " #" . $region['region_id'] . " (" . $region['width'] . "x" . $region['height'] . ")";
                                $selected = ($region['region_id'] == ($task['page_region_id'] ?? 0)) ? 'selected' : '';
                            ?>
                                <option value="<?= $region['region_id'] ?>" <?= $selected ?>><?= htmlspecialchars($lbl) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Link tài nguyên đính kèm (New) -->
            <div class="mb-3">
                <label for="resource_url" class="form-label">Tài nguyên hỗ trợ đính kèm (URL)</label>
                <input type="url" class="form-control" id="resource_url" name="resource_url" value="<?= htmlspecialchars($task['resource_url'] ?? '') ?>" placeholder="https://drive.google.com/drive/folders/... hoặc liên kết khác">
                <div class="form-text">Đường dẫn Google Drive, Figma... chứa tài liệu mẫu hoặc asset hỗ trợ vẽ.</div>
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
                        <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý (Pending)</option>
                        <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>Đang làm (In Progress)</option>
                        <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành (Completed)</option>
                    </select>
                </div>
            </div>

            <!-- Hạn chót công việc -->
            <div class="mb-3">
                <label for="due_date" class="form-label">Hạn chót</label>
                <!-- Định dạng lại chuỗi datetime để gán vào input type="datetime-local" -->
                <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?= $task['due_date'] ? date('Y-m-d\TH:i', strtotime($task['due_date'])) : '' ?>">
            </div>

            <button type="submit" class="btn btn-warning">Lưu thay đổi</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
