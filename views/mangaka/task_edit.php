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
                
                <?php if (!empty($groupedRegionData)): ?>
                    <div id="grouped-regions-inputs" class="d-flex flex-column gap-3">
                        <?php foreach($groupedRegionData as $gData): 
                            $rId = $gData['region_id'];
                            $rType = $gData['region_type'];
                            $oldTitle = $gData['old_title'];
                            $oldTaskType = $gData['old_task_type'];
                            $oldDesc = $gData['old_description'];
                            
                            $isStandardType = in_array($oldTaskType, ['background', 'inking', 'coloring', 'effects', '']);
                            $selectedType = $isStandardType ? $oldTaskType : 'other';
                            $oldCustomType = !$isStandardType ? $oldTaskType : '';
                            
                            $typeLabel = 'Khác';
                            $badgeColor = '#6c757d'; // secondary
                            $bgColor = '#f8f9fa';
                            switch ($rType) {
                                case 'panel': $typeLabel = 'Khung truyện'; $badgeColor = '#dc3545'; $bgColor = '#fdf1f2'; break;
                                case 'bubble': $typeLabel = 'Bong bóng thoại'; $badgeColor = '#0d6efd'; $bgColor = '#f0f5ff'; break;
                                case 'character': $typeLabel = 'Nhân vật'; $badgeColor = '#198754'; $bgColor = '#f0f9f4'; break;
                                case 'background': $typeLabel = 'Bối cảnh/Nền'; $badgeColor = '#212529'; $bgColor = '#f8f9fa'; break;
                                case 'sfx': $typeLabel = 'Hiệu ứng SFX'; $badgeColor = '#ffc107'; $bgColor = '#fffdf0'; break;
                            }
                        ?>
                        <div class="card shadow-sm border-0 mb-3" style="border: 1px solid <?= $badgeColor ?>33 !important; border-radius: 8px; overflow: hidden; background-color: <?= $bgColor ?> !important;">
                            <div class="card-header border-bottom-0 py-2.5 px-3 d-flex align-items-center justify-content-between" style="background-color: transparent;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background-color: <?= $badgeColor ?>; font-size: 0.75rem; padding: 0.4em 0.7em; border-radius: 4px; font-weight: 600; box-shadow: 0 2px 4px <?= $badgeColor ?>33;"><?= htmlspecialchars($typeLabel) ?></span>
                                </div>
                                <span class="fw-bold text-slate-600" style="font-size: 0.82rem; letter-spacing: 0.5px;">PHÂN VÙNG #<?= $rId ?></span>
                            </div>
                            <div class="card-body pt-1 pb-3 px-3">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-edit me-1 text-slate-500"></i>Tiêu đề công việc (Tùy chọn)
                                        </label>
                                        <input type="text" class="form-control form-control-sm region-specific-title border-light-subtle" data-region-id="<?= $rId ?>" placeholder="Tiêu đề cho vùng này..." value="<?= htmlspecialchars($oldTitle) ?>" style="font-weight: 500; font-size: 0.85rem; border-radius: 6px; background-color: #fff; box-shadow: none; border: 1px solid #cbd5e1;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-tasks me-1 text-slate-500"></i>Loại công việc chi tiết
                                        </label>
                                        <select class="form-select form-select-sm region-specific-type border-light-subtle" data-region-id="<?= $rId ?>" style="font-size: 0.85rem; border-radius: 6px; background-color: #fff; color: #475569; font-weight: 500; box-shadow: none; border: 1px solid #cbd5e1;">
                                            <option value="" <?= $selectedType == '' ? 'selected' : '' ?>>-- Chọn loại công việc chi tiết --</option>
                                            <option value="background" <?= $selectedType == 'background' ? 'selected' : '' ?>>Vẽ nền (Background)</option>
                                            <option value="inking" <?= $selectedType == 'inking' ? 'selected' : '' ?>>Đi nét (Inking)</option>
                                            <option value="coloring" <?= $selectedType == 'coloring' ? 'selected' : '' ?>>Lên màu (Coloring)</option>
                                            <option value="effects" <?= $selectedType == 'effects' ? 'selected' : '' ?>>Hiệu ứng (Effects)</option>
                                            <option value="other" <?= $selectedType == 'other' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 region-custom-type-container <?= $selectedType === 'other' ? '' : 'd-none' ?>" data-region-id="<?= $rId ?>">
                                    <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-keyboard me-1 text-slate-500"></i>Nhập loại công việc khác <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-sm region-specific-custom-type border-light-subtle" data-region-id="<?= $rId ?>" placeholder="Ví dụ: Đổ tone, Dán decal..." value="<?= htmlspecialchars($oldCustomType) ?>" style="font-size: 0.85rem; border-radius: 6px; background-color: #fff; box-shadow: none; border: 1px solid #cbd5e1;">
                                </div>
                                <div>
                                    <label class="form-label mb-1 text-uppercase fw-bold text-slate-600" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-align-left me-1 text-slate-500"></i>Ghi chú mô tả chi tiết công việc
                                    </label>
                                    <div class="quill-region-editor-wrapper border rounded" style="background-color: #fff; border: 1px solid #cbd5e1 !important; border-radius: 6px; overflow: hidden;">
                                        <div class="quill-region-editor region-specific-desc" data-region-id="<?= $rId ?>" data-badge-color="<?= $badgeColor ?>" data-bg-color="<?= $bgColor ?>" data-type-label="<?= htmlspecialchars($typeLabel) ?>" style="height: 120px; font-size: 0.85rem;">
                                            <?= $oldDesc ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Quill container for single task -->
                    <div id="quill-editor"></div>
                <?php endif; ?>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Quill Editor for single task if exists
                let quill = null;
                const quillContainer = document.getElementById('quill-editor');
                if (quillContainer) {
                    quill = new Quill('#quill-editor', {
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
                }

                // Xử lý loại công việc tự chọn
                const taskTypeSelect = document.getElementById('task_type');
                const customTaskTypeContainer = document.getElementById('custom_task_type_container');
                const customTaskTypeInput = document.getElementById('custom_task_type');

                function toggleCustomTaskType() {
                    if (taskTypeSelect && taskTypeSelect.value === 'other') {
                        if (customTaskTypeContainer) customTaskTypeContainer.classList.remove('d-none');
                        if (customTaskTypeInput) customTaskTypeInput.required = true;
                    } else {
                        if (customTaskTypeContainer) customTaskTypeContainer.classList.add('d-none');
                        if (customTaskTypeInput) customTaskTypeInput.required = false;
                    }
                }

                if (taskTypeSelect && customTaskTypeContainer && customTaskTypeInput) {
                    taskTypeSelect.addEventListener('change', toggleCustomTaskType);
                    // Chạy ngay lần đầu để đồng bộ trạng thái mặc định
                    toggleCustomTaskType();
                }

                // Khởi tạo Quill Editor cho từng phân vùng (Region) nếu là task nhóm
                const regionQuills = {};
                document.querySelectorAll('.quill-region-editor').forEach(function(el) {
                    const rId = el.getAttribute('data-region-id');
                    regionQuills[rId] = new Quill(el, {
                        theme: 'snow',
                        placeholder: 'Nhập ghi chú chi tiết mô tả công việc cần xử lý trong vùng này...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['clean']
                            ]
                        }
                    });
                });

                // Xử lý loại công việc tự chọn cho từng phân vùng (Region) nếu là task nhóm
                document.querySelectorAll('.region-specific-type').forEach(function(select) {
                    const rId = select.getAttribute('data-region-id');
                    const customContainer = document.querySelector(`.region-custom-type-container[data-region-id="${rId}"]`);
                    
                    function toggleRegionCustom() {
                        if (customContainer) {
                            if (select.value === 'other') {
                                customContainer.classList.remove('d-none');
                            } else {
                                customContainer.classList.add('d-none');
                            }
                        }
                    }

                    select.addEventListener('change', toggleRegionCustom);
                    toggleRegionCustom();
                });

                // Sync Quill editor HTML with the hidden textarea on submit
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        const descriptionTextarea = document.getElementById('description');
                        const groupedInputs = document.querySelectorAll('.region-specific-desc');
                        
                        if (groupedInputs.length > 0) {
                            // Cấu trúc lại HTML đẹp mắt cho các thẻ vùng
                            let combinedHtml = '<div class="grouped-task-instructions" style="display: flex; flex-direction: column; gap: 12px;">';
                            groupedInputs.forEach(input => {
                                const rId = input.getAttribute('data-region-id');
                                
                                const titleInput = document.querySelector(`.region-specific-title[data-region-id="${rId}"]`);
                                const typeSelect = document.querySelector(`.region-specific-type[data-region-id="${rId}"]`);
                                
                                const quillInstance = regionQuills[rId];
                                const valHtml = quillInstance ? quillInstance.root.innerHTML.trim() : '';
                                const plainText = quillInstance ? quillInstance.getText().trim() : '';
                                
                                const rTitle = titleInput ? titleInput.value.trim() : '';
                                let rType = typeSelect ? typeSelect.options[typeSelect.selectedIndex].text : '';
                                const hasType = typeSelect && typeSelect.value !== '';
                                
                                if (typeSelect && typeSelect.value === 'other') {
                                    const customInput = document.querySelector(`.region-specific-custom-type[data-region-id="${rId}"]`);
                                    if (customInput && customInput.value.trim()) {
                                        rType = customInput.value.trim();
                                    }
                                }
                                
                                if (plainText !== '' || rTitle || hasType) {
                                    const bColor = input.getAttribute('data-badge-color');
                                    const bgColor = input.getAttribute('data-bg-color') || '#f8fafc';
                                    const tLabel = input.getAttribute('data-type-label');
                                    
                                    const safeTitle = rTitle.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                                    
                                    let contentHtml = '';
                                    if (safeTitle) contentHtml += `<h6 style="color: #0f172a; font-weight: 700; font-size: 14.5px; margin-bottom: 4px;">${safeTitle}</h6>`;
                                    if (hasType) contentHtml += `<p style="color: #64748b; font-size: 12px; margin-bottom: 6px; font-weight: 600;"><i class="fas fa-tag me-1"></i> Loại việc: ${rType}</p>`;
                                    if (plainText !== '') contentHtml += `<div class="region-desc-content ql-editor" style="font-size: 13.5px; color: #334155; line-height: 1.5; padding: 0; background: transparent;">${valHtml}</div>`;
                                    
                                    combinedHtml += `
                                    <div class="region-instruction-card" style="border: 1px solid ${bColor}; padding: 12px; border-radius: 6px; background-color: ${bgColor};">
                                        <div style="margin-bottom: 8px; border-bottom: 1px solid ${bColor}40; padding-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                                            <span style="background-color: ${bColor}; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">${tLabel}</span>
                                            <span style="font-size: 13px; font-weight: bold; color: #475569;">Phân vùng #${rId}</span>
                                        </div>
                                        ${contentHtml}
                                    </div>`;
                                }
                            });
                            combinedHtml += '</div>';
                            
                            descriptionTextarea.value = (combinedHtml === '<div class="grouped-task-instructions" style="display: flex; flex-direction: column; gap: 12px;"></div>') ? '' : combinedHtml;
                        } else if (quill) {
                            if (quill.getText().trim().length > 0) {
                                descriptionTextarea.value = quill.root.innerHTML;
                            } else {
                                descriptionTextarea.value = '';
                            }
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
                    <?php if (!empty($task['grouped_region_ids'])): ?>
                        <?php
                        $gidArr = array_filter(array_map('trim', explode(',', $task['grouped_region_ids'])));
                        $gidList = implode(', ', array_map(fn($id) => '#'.$id, $gidArr));
                        ?>
                        <label class="form-label">Phân vùng được giao (Nhóm)</label>
                        <div class="form-control bg-light d-flex align-items-center gap-2" style="min-height: 38px;">
                            <i class="fas fa-layer-group text-primary"></i>
                            <span class="fw-semibold text-primary">Nhóm vùng: <?= $gidList ?></span>
                            <span class="badge bg-primary ms-1" style="font-size: 0.7rem;">Nhóm</span>
                        </div>
                        <div class="form-text">Công việc nhóm — không thể thay đổi phân vùng khi chỉnh sửa.</div>
                        <!-- Giữ lại grouped_region_ids khi submit -->
                        <input type="hidden" name="grouped_region_ids" value="<?= htmlspecialchars($task['grouped_region_ids']) ?>">
                    <?php else: ?>
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
                    <?php endif; ?>
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
