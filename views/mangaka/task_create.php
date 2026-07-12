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

            <?php
            $selectedRegionId = isset($_GET['page_region_id']) ? intval($_GET['page_region_id']) : 0;
            ?>

            <!-- Trường Mô tả chi tiết (Tùy chọn) -->
            <div class="mb-3">
                <label for="description" class="form-label fw-bold text-slate-700">Mô tả chi tiết (Tùy chọn)</label>
                <!-- Hidden textarea to store the HTML content for backend submission -->
                <textarea id="description" name="description" style="display: none;"></textarea>
                
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

                // Xử lý loại công việc tự chọn (Single)
                const taskTypeSelect = document.getElementById('task_type');
                const customTaskTypeContainer = document.getElementById('custom_task_type_container');
                const customTaskTypeInput = document.getElementById('custom_task_type');

                function toggleCustomTaskType() {
                    if (taskTypeSelect && taskTypeSelect.value === 'other') {
                        customTaskTypeContainer.classList.remove('d-none');
                        customTaskTypeInput.required = true;
                    } else {
                        if (customTaskTypeContainer) customTaskTypeContainer.classList.add('d-none');
                        if (customTaskTypeInput) customTaskTypeInput.required = false;
                    }
                }
                
                if (taskTypeSelect && customTaskTypeContainer && customTaskTypeInput) {
                    taskTypeSelect.addEventListener('change', toggleCustomTaskType);
                    toggleCustomTaskType();
                }

                // Xử lý chuyển đổi giữa Single Task và Multi Task
                const multiTaskToggle = document.getElementById('multi_task_toggle');
                const singleContainer = document.getElementById('single_task_type_container');
                const multiContainer = document.getElementById('multi_task_types_container');
                const cbOther = document.getElementById('cb_other');
                const customMultiInput = document.getElementById('custom_multi_task_type');

                if (multiTaskToggle) {
                    multiTaskToggle.addEventListener('change', function() {
                        if (this.checked) {
                            singleContainer.classList.add('d-none');
                            multiContainer.classList.remove('d-none');
                            if (taskTypeSelect) taskTypeSelect.required = false;
                            if (customTaskTypeInput) customTaskTypeInput.required = false;
                        } else {
                            singleContainer.classList.remove('d-none');
                            multiContainer.classList.add('d-none');
                            if (taskTypeSelect) taskTypeSelect.required = true;
                            toggleCustomTaskType();
                        }
                    });
                }

                if (cbOther && customMultiInput) {
                    cbOther.addEventListener('change', function() {
                        if (this.checked) {
                            customMultiInput.classList.remove('d-none');
                            customMultiInput.required = true;
                        } else {
                            customMultiInput.classList.add('d-none');
                            customMultiInput.required = false;
                        }
                    });
                }
                
                // Sync Quill editor HTML with the hidden textarea on submit and validate checkboxes
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Check if multi-task is active and at least one checkbox is checked
                        if (multiTaskToggle && multiTaskToggle.checked) {
                            const checkedBoxes = document.querySelectorAll('.task-type-cb:checked');
                            const errorMsg = document.getElementById('cb_error_msg');
                            if (checkedBoxes.length === 0) {
                                e.preventDefault();
                                errorMsg.classList.remove('d-none');
                                return false;
                            } else {
                                errorMsg.classList.add('d-none');
                            }
                        }

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
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Loại công việc <span class="text-danger">*</span></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="multi_task_toggle" name="is_multi_task" value="1">
                            <label class="form-check-label text-slate-600 font-semibold" for="multi_task_toggle" style="font-size: 0.8rem; cursor: pointer;">Giao nhiều việc cùng lúc</label>
                        </div>
                    </div>
                    
                    <!-- Single task type container (Default) -->
                    <div id="single_task_type_container">
                        <select class="form-select" id="task_type" name="task_type" required>
                            <option value="background">Vẽ nền (Background)</option>
                            <option value="inking">Đi nét (Inking)</option>
                            <option value="coloring">Lên màu (Coloring)</option>
                            <option value="effects">Hiệu ứng (Effects)</option>
                            <option value="other" selected>Khác (Other)</option>
                        </select>
                        
                        <div class="mt-2 d-none" id="custom_task_type_container">
                            <label for="custom_task_type" class="form-label fw-semibold text-slate-700" style="font-size: 0.85rem;">Nhập loại công việc tự chọn <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="custom_task_type" name="custom_task_type" placeholder="Ví dụ: Đổ tone, Dán decal, v.v.">
                        </div>
                    </div>
                    
                    <!-- Multi task types container (hidden by default) -->
                    <div id="multi_task_types_container" class="d-none border p-3 rounded bg-light">
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="background" id="cb_background">
                            <label class="form-check-label" for="cb_background">Vẽ nền (Background)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="inking" id="cb_inking">
                            <label class="form-check-label" for="cb_inking">Đi nét (Inking)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="coloring" id="cb_coloring">
                            <label class="form-check-label" for="cb_coloring">Lên màu (Coloring)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="effects" id="cb_effects">
                            <label class="form-check-label" for="cb_effects">Hiệu ứng (Effects)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input task-type-cb" type="checkbox" name="task_types[]" value="other" id="cb_other">
                            <label class="form-check-label" for="cb_other">Khác</label>
                            <input type="text" class="form-control form-control-sm mt-1 d-none" id="custom_multi_task_type" name="custom_multi_task_type" placeholder="Nhập loại công việc khác...">
                        </div>
                        <div class="form-text text-danger d-none" id="cb_error_msg" style="font-size: 0.75rem;">Vui lòng chọn ít nhất một loại công việc.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="page_region_id" class="form-label fw-bold">Phân vùng ảnh</label>
                    <select class="form-select" id="page_region_id" name="page_region_id">
                        <option value="">-- Toàn bộ trang truyện --</option>
                        <?php if (!empty($regions)): ?>
                            <?php foreach ($regions as $region): 
                                $lbl = ucfirst($region['region_type']) . " #" . $region['region_id'] . " (" . $region['width'] . "x" . $region['height'] . ")";
                                $selected = ($region['region_id'] == $selectedRegionId) ? 'selected' : '';
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
                <input type="url" class="form-control" id="resource_url" name="resource_url" placeholder="https://drive.google.com/drive/folders/... hoặc liên kết khác">
                <div class="form-text">Đường dẫn Google Drive, Figma... chứa tài liệu mẫu hoặc asset hỗ trợ vẽ.</div>
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
